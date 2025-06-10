<?php
ob_start();
include("../includes/header.php");
include("../includes/email.php");
require '../autentimine/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_GET['session_id'])) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Makse sessioon puudub
            </div>
            <a href="../avaleht/" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Tagasi avalehele
            </a>
          </div>';
    include("../includes/footer.php");
    exit;
}

$stripe_session_id = $_GET['session_id'];

// 1. Päring makse andmete saamiseks
$makse_query = mysqli_query($yhendus, 
    "SELECT id, broneering_id, summa, staatus, makseviis 
     FROM maksed 
     WHERE stripe_id = '".mysqli_real_escape_string($yhendus, $stripe_session_id)."'");

if (!$makse_query || mysqli_num_rows($makse_query) === 0) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Makseandmeid ei leitud
            </div>
            <a href="../avaleht/" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Tagasi avalehele
            </a>
          </div>';
    include("../includes/footer.php");
    exit;
}

$makse = mysqli_fetch_assoc($makse_query);
$broneering_id = $makse['broneering_id'];

// 2. Päring broneeringu andmete saamiseks
$broneering_query = mysqli_query($yhendus, 
    "SELECT broneeringud.*, toad.toa_nr, toa_tyyp.toa_tyyp, toa_tyyp.toa_hind 
     FROM broneeringud
     JOIN toad ON broneeringud.toa_id = toad.id
     JOIN toa_tyyp ON toad.toa_id = toa_tyyp.id
     WHERE broneeringud.id = $broneering_id");

$broneering = mysqli_fetch_assoc($broneering_query);

// 3. Külalise andmed
$kylaline_query = mysqli_query($yhendus, 
    "SELECT eesnimi, perenimi, isikukood, telefon, email 
     FROM kylalised 
     WHERE id = ".$broneering['kylaline_id']);
    
$kylaline = mysqli_fetch_assoc($kylaline_query);
$email = $kylaline['email'] ?? '';
$eesnimi = $kylaline['eesnimi'] ?? '';
$perenimi = $kylaline['perenimi'] ?? '';
$telefon = $kylaline['telefon'] ?? '';
$isikukood = $kylaline['isikukood'] ?? '';

// 4. Päring teenuste andmete saamiseks
$teenused_query = mysqli_query($yhendus, 
    "SELECT teenused.teenus, broneeringu_teenused.kogus, broneeringu_teenused.hind 
     FROM broneeringu_teenused
     JOIN teenused ON broneeringu_teenused.teenus_id = teenused.id
     WHERE broneeringu_teenused.broneering_id = $broneering_id");

$teenused = [];
$total_teenused = 0;

while ($teenus = mysqli_fetch_assoc($teenused_query)) {
    $teenused[] = $teenus;
    $total_teenused += $teenus['kogus'] * $teenus['hind'];
}

// Arvutused
$saabumine = new DateTime($broneering['saabumine']);
$lahkumine = new DateTime($broneering['lahkumine']);
$oode_arv = $saabumine->diff($lahkumine)->days;
$toa_summa = $oode_arv * $broneering['toa_hind'];
$km = 0.24 * ($toa_summa + $total_teenused);
$kokku = $toa_summa + $total_teenused + $km;

// Uuenda staatuseid
mysqli_query($yhendus, "UPDATE broneeringud SET staatus = 'kinnitatud' WHERE id = $broneering_id");
mysqli_query($yhendus, "UPDATE maksed SET staatus = 'tasutud' WHERE id = ".$makse['id']);

// E-kirja saatmise staatus
$email_sent = false;
$email_error = '';
$manual_email = '';

// Kui vorm on saadetud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email'])) {
    $manual_email = trim($_POST['email']);
    
    if (filter_var($manual_email, FILTER_VALIDATE_EMAIL)) {
        $email = $manual_email;
        $email_sent = send_confirmation_email($email, $eesnimi, $perenimi, $broneering_id, $broneering, $oode_arv, $toa_summa, $teenused, $total_teenused, $km, $kokku, $makse);
        
        if (!$email_sent) {
            $email_error = 'E-kirja saatmine ebaõnnestus. Palun proovige uuesti.';
        }
    } else {
        $email_error = 'Palun sisestage kehtiv e-posti aadress';
    }
} elseif (!empty($email)) {
    // Proovime automaatselt saata
    $email_sent = send_confirmation_email($email, $eesnimi, $perenimi, $broneering_id, $broneering, $oode_arv, $toa_summa, $teenused, $total_teenused, $km, $kokku, $makse);
}

// E-kirja saatmise funktsioon
function send_confirmation_email($email, $eesnimi, $perenimi, $broneering_id, $broneering, $oode_arv, $toa_summa, $teenused, $total_teenused, $km, $kokku, $makse) {
    global $email_error;
    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = EMAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = EMAIL_PORT;

        $mail->setFrom(PAYMENT_EMAIL_FROM, PAYMENT_EMAIL_FROM_NAME);
        $mail->addAddress($email, $eesnimi.' '.$perenimi);
        $mail->isHTML(true);
        $mail->Subject = 'Teie broneeringu kinnitus (nr #'.$broneering_id.')';
        
        // E-kirja sisu
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Broneeringu kinnitus</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; border: 1px solid #ddd; border-top: none; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
                .total { font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Broneeringu kinnitus</h1>
                </div>
                
                <div class="content">
                    <p>Tere, '.htmlspecialchars($eesnimi).'!</p>
                    <p>Teie broneering hotellis on edukalt kinnitatud. Allpool on broneeringu üksikasjad:</p>
                    
                    <h3>Broneeringu andmed</h3>
                    <p><strong>Broneeringu number:</strong> #'.$broneering_id.'</p>
                    <p><strong>Toa number:</strong> '.htmlspecialchars($broneering['toa_nr']).'</p>
                    <p><strong>Toa tüüp:</strong> '.htmlspecialchars($broneering['toa_tyyp']).'</p>
                    <p><strong>Saabumine:</strong> '.date('d.m.Y', strtotime($broneering['saabumine'])).'</p>
                    <p><strong>Lahkumine:</strong> '.date('d.m.Y', strtotime($broneering['lahkumine'])).'</p>
                    <p><strong>Ööde arv:</strong> '.$oode_arv.'</p>
                    
                    <h3>Makseandmed</h3>
                    <table>
                        <tr>
                            <td>Toa hind ('.$oode_arv.' ööd)</td>
                            <td>'.number_format($toa_summa, 2, ',', ' ').' €</td>
                        </tr>';
        
        if (!empty($teenused)) {
            $mail->Body .= '<tr><td colspan="2"><strong>Lisateenused:</strong></td></tr>';
            foreach ($teenused as $teenus) {
                $mail->Body .= '
                        <tr>
                            <td>'.htmlspecialchars($teenus['teenus']).' ('.$teenus['kogus'].' tk)</td>
                            <td>'.number_format($teenus['kogus'] * $teenus['hind'], 2, ',', ' ').' €</td>
                        </tr>';
            }
            $mail->Body .= '
                        <tr>
                            <td>Teenuste kogusumma</td>
                            <td>'.number_format($total_teenused, 2, ',', ' ').' €</td>
                        </tr>';
        }
        
        $mail->Body .= '
                        <tr>
                            <td>KM (24%)</td>
                            <td>'.number_format($km, 2, ',', ' ').' €</td>
                        </tr>
                        <tr class="total">
                            <td>KOKKU</td>
                            <td>'.number_format($kokku, 2, ',', ' ').' €</td>
                        </tr>
                    </table>
                    
                    <p><strong>Makseviis:</strong> '.htmlspecialchars($makse['makseviis']).'</p>
                    <p><strong>Makse staatus:</strong> Tasutud</p>
                    
                    <p>Täname, et valisite meie hotelli! Loodame, et Teie peatus on meeldiv.</p>
                    
                    <p>Kui teil on küsimusi, võtke meiega ühendust aadressil '.PAYMENT_EMAIL_FROM.'</p>
                    
                    <p>Parimate soovidega,<br>Kplaas Hotelli meeskond</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        $email_error = 'E-kirja saatmine ebaõnnestus: '.$mail->ErrorInfo;
        return false;
    }
}
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h2 class="mb-0"><i class="bi bi-check-circle me-2"></i>Broneering kinnitatud!</h2>
        </div>
        
        <div class="card-body">
            <div class="alert alert-success d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                <div>
                    <h4 class="alert-heading mb-1">Täname Teie broneeringu eest!</h4>
                    <p class="mb-0">Teie broneering on edukalt kinnitatud ja makse on sooritatud.</p>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h4 class="mb-0"><i class="bi bi-person me-2"></i>Teie andmed</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Nimi:</strong> <?= htmlspecialchars($eesnimi.' '.$perenimi) ?></p>
                            <p><strong>E-post:</strong> <?= htmlspecialchars($email) ?></p>
                            <p><strong>Telefon:</strong> <?= htmlspecialchars($telefon) ?></p>
                            <p><strong>Isikukood:</strong> <?= htmlspecialchars($isikukood) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h4 class="mb-0"><i class="bi bi-house me-2"></i>Toa info</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Toa number:</strong> <?= htmlspecialchars($broneering['toa_nr']) ?></p>
                            <p><strong>Toa tüüp:</strong> <?= htmlspecialchars($broneering['toa_tyyp']) ?></p>
                            <p><strong>Saabumine:</strong> <?= date('d.m.Y', strtotime($broneering['saabumine'])) ?></p>
                            <p><strong>Lahkumine:</strong> <?= date('d.m.Y', strtotime($broneering['lahkumine'])) ?></p>
                            <p><strong>Ööde arv:</strong> <?= $oode_arv ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h4 class="mb-0"><i class="bi bi-list-check me-2"></i>Hinnakokkutulek</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kirjeldus</th>
                                <th class="text-end">Summa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Toa hind (<?= $oode_arv ?> ööd)</td>
                                <td class="text-end"><?= number_format($toa_summa, 2, ',', ' ') ?> €</td>
                            </tr>
                            
                            <?php if (!empty($teenused)): ?>
                                <tr>
                                    <td colspan="2"><strong>Lisateenused:</strong></td>
                                </tr>
                                <?php foreach ($teenused as $teenus): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($teenus['teenus']) ?> (<?= $teenus['kogus'] ?> tk)</td>
                                        <td class="text-end"><?= number_format($teenus['kogus'] * $teenus['hind'], 2, ',', ' ') ?> €</td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td>Teenuste kogusumma</td>
                                    <td class="text-end"><?= number_format($total_teenused, 2, ',', ' ') ?> €</td>
                                </tr>
                            <?php endif; ?>
                            
                            <tr>
                                <td>KM (24%)</td>
                                <td class="text-end"><?= number_format($km, 2, ',', ' ') ?> €</td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>KOKKU</strong></td>
                                <td class="text-end"><strong><?= number_format($kokku, 2, ',', ' ') ?> €</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Oluline teave külalistele:</strong> Palun hoidke broneeringu numbrit (#<?= $broneering_id ?>) kinni, 
                seda võidakse teilt küsida registreerimisel.
            </div>
            
            <?php if ($email_sent): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    Kinnitusmeil on saadetud aadressile <strong><?= htmlspecialchars($email) ?></strong>.
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php if (!empty($email_error)): ?>
                        <?= $email_error ?>
                    <?php else: ?>
                        Kinnitusmeili ei saadetud. Palun sisestage e-posti aadress, kuhu saata kinnitus.
                    <?php endif; ?>
                </div>
                
                <form method="post" class="mb-4">
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Sisesta e-posti aadress" value="<?= htmlspecialchars($manual_email ?: $email) ?>" required>
                        <button type="submit" name="send_email" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Saada kinnitus
                        </button>
                    </div>
                </form>
            <?php endif; ?>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="../avaleht/" class="btn btn-primary">
                    <i class="bi bi-house me-2"></i>Avalehele
                </a>
            </div>
        </div>
    </div>
</div>

<?php
include("../includes/footer.php");
?>