<?php
include '../includes/header.php';
include '../includes/email.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../autentimine/vendor/autoload.php';

$veateade = "";
$onnestus = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kasutajanimi = mysqli_real_escape_string($yhendus, $_POST["kasutajanimi"]);
    $email = mysqli_real_escape_string($yhendus, $_POST["email"]);
    $parool = $_POST["parool"];
    $parool2 = $_POST["parool2"];

    $eesnimi = mysqli_real_escape_string($yhendus, $_POST["eesnimi"]);
    $perenimi = mysqli_real_escape_string($yhendus, $_POST["perenimi"]);
    $suunakood = mysqli_real_escape_string($yhendus, $_POST["suunakood"]);
    $telefoninr = mysqli_real_escape_string($yhendus, $_POST["telefoninr"]);
    $isikukood = mysqli_real_escape_string($yhendus, $_POST["isikukood"]);
    $telefon = $suunakood . $telefoninr;

    if (empty($kasutajanimi) || empty($email) || empty($parool) || empty($parool2)
        || empty($eesnimi) || empty($perenimi) || empty($telefoninr) || empty($isikukood)) {
        $veateade = "Palun täida kõik väljad.";
    } elseif ($parool != $parool2) {
        $veateade = "Paroolid ei ühti.";
    } elseif (!ctype_digit($isikukood) || strlen($isikukood) > 11) {
        $veateade = "Isikukood peab sisaldama ainult kuni 11 numbrit.";
    } elseif (!ctype_digit($telefoninr) || strlen($telefoninr) > 20) {
        $veateade = "Telefoninumber peab sisaldama ainult kuni 20 numbrit.";
    } else {
        $kontrolli = mysqli_query($yhendus, "SELECT id FROM kasutajad WHERE email = '$email'");
        if (mysqli_num_rows($kontrolli) > 0) {
            $veateade = "Email on juba kasutusel.";
        } else {
            $parool_hashed = password_hash($parool, PASSWORD_DEFAULT);
            $kood = bin2hex(random_bytes(16));

            $lisakonto = mysqli_query($yhendus,
                "INSERT INTO kasutajad (kasutajanimi, email, parool, email_kinnituskood)
                 VALUES ('$kasutajanimi', '$email', '$parool_hashed', '$kood')"
            );

            if ($lisakonto) {
                $kasutaja_id = mysqli_insert_id($yhendus);

                $lisaklient = mysqli_query($yhendus,
                    "INSERT INTO kliendid (kasutaja_id, eesnimi, perenimi, telefon, isikukood)
                     VALUES ('$kasutaja_id', '$eesnimi', '$perenimi', '$telefon', '$isikukood')"
                );

                if ($lisaklient) {
                    $kinnituslink = "https://kplaas.ee/hotell/autentimine/kinnitus.php?kood=$kood";

                    $teema = "$eesnimi, palun kinnita oma kplaas Hotelli konto";
                    $sisu = '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Konto kinnitamine</title>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-bottom: 1px solid #dee2e6; }
                            .content { padding: 30px; }
                            .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #6c757d; border-top: 1px solid #dee2e6; }
                            .button { background-color: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; }
                            .security-note { font-size: 12px; color: #6c757d; margin-top: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="header">
                                <h2 style="color: #28a745; margin: 0;">kplaas Hotell</h2>
                            </div>
                            
                            <div class="content">
                                <h3 style="margin-top: 0;">Tere, '.$eesnimi.' '.$perenimi.'!</h3>
                                <p>Aitäh registreerumise eest kplaas Hotelli süsteemi!</p>
                                <p>Palun kinnita oma e-posti aadress klõpsates allolevale lingile:</p>
                                
                                <div style="margin: 30px 0; text-align: center;">
                                    <a href="'.$kinnituslink.'" class="button">Kinnita konto</a>
                                </div>
                                
                                <p>Kui nupp ei tööta, kopeeri see URL oma brauseri aadressiribale:<br>
                                <span style="word-break: break-all;">'.$kinnituslink.'</span></p>
                                
                                <div class="security-note">
                                    <p>Turvateade: Ärge jagage kunagi oma kinnituslinki kolmandate isikutega.</p>
                                    <p>Kui see polnud teie, ignoreerige seda kirja.</p>
                                </div>
                            </div>
                            
                            <div class="footer">
                                <p>© '.date('Y').' kplaas Hotell. Kõik õigused kaitstud.</p>
                                <p>
                                    <a href="https://www.facebook.com/kplaas" style="color: #6c757d; text-decoration: none; margin: 0 5px;">Facebook</a> |
                                    <a href="https://www.instagram.com/kplaas" style="color: #6c757d; text-decoration: none; margin: 0 5px;">Instagram</a> |
                                    <a href="https://www.kplaas.ee" style="color: #6c757d; text-decoration: none; margin: 0 5px;">Koduleht</a>
                                </p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ';

                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = EMAIL_HOST;
                        $mail->SMTPAuth = true;
                        $mail->Username = EMAIL_USERNAME;
                        $mail->Password = EMAIL_PASSWORD;
                        $mail->SMTPSecure = 'ssl';
                        $mail->Port = EMAIL_PORT;

                        $mail->setFrom(VERIFICATION_EMAIL_FROM, VERIFICATION_EMAIL_FROM_NAME);
                        $mail->addAddress($email, $eesnimi);
                        $mail->isHTML(true);
                        $mail->Subject = $teema;
                        $mail->Body = $sisu;

                        $mail->send();
                        $onnestus = "Konto loodud! Kontrolli oma e-posti kinnituse jaoks.";
                    } catch (Exception $e) {
                        $veateade = "Konto loodi, aga e-posti ei õnnestunud saata. Veateade: " . $e->getMessage();
                    }
                } else {
                    $veateade = "Kasutaja lisati, kuid kliendi andmed jäid lisamata.";
                }
            } else {
                $veateade = "Midagi läks valesti kasutaja lisamisel.";
            }
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white py-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-plus-fill me-3" viewBox="0 0 16 16">
                            <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>
                        <h2 class="mb-0 text-center">Loo uus konto</h2>
                    </div>
                </div>
                
                <div class="card-body p-5">
                    <?php if (!empty($veateade)): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <?= $veateade ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif (!empty($onnestus)): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4">
                            <?= $onnestus ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="needs-validation" novalidate>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="eesnimi" class="form-label">Eesnimi <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                        </svg>
                                    </span>
                                    <input type="text" class="form-control" id="eesnimi" name="eesnimi" placeholder="Sisesta eesnimi" required>
                                    <div class="invalid-feedback">
                                        Palun sisesta oma eesnimi
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="perenimi" class="form-label">Perekonnanimi <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                        </svg>
                                    </span>
                                    <input type="text" class="form-control" id="perenimi" name="perenimi" placeholder="Sisesta perekonnanimi" required>
                                    <div class="invalid-feedback">
                                        Palun sisesta oma perekonnanimi
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="kasutajanimi" class="form-label">Kasutajanimi <span class="text-danger">*</span></label>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-badge-fill" viewBox="0 0 16 16">
                                        <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm4.5 0a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zM8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm5 2.755C12.146 12.825 10.623 12 8 12s-4.146.826-5 1.755V14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-.245z"/>
                                    </svg>
                                </span>
                                <input type="text" class="form-control" id="kasutajanimi" name="kasutajanimi" placeholder="Sisesta kasutajanimi" required>
                                <div class="invalid-feedback">
                                    Palun sisesta kasutajanimi
                                </div>
                            </div>
                            <small class="text-muted">See on teie sisselogimiseks vajalik nimi</small>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">E-post <span class="text-danger">*</span></label>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                                        <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
                                    </svg>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="nimi@example.com" required>
                                <div class="invalid-feedback">
                                    Palun sisesta korrektne e-posti aadress
                                </div>
                            </div>
                            <small class="text-muted">Saadame kinnituse sellele aadressile</small>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="suunakood" class="form-label">Suunakood <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                                        </svg>
                                    </span>
                                    <select class="form-select" id="suunakood" name="suunakood" required>
                                        <option value="" selected disabled>Vali...</option>
                                        <option value="+372">+372 (Eesti)</option>
                                        <option value="+358">+358 (Soome)</option>
                                        <option value="+371">+371 (Läti)</option>
                                        <option value="+370">+370 (Leedu)</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Palun vali suunakood
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label for="telefoninr" class="form-label">Telefoninumber <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-phone-fill" viewBox="0 0 16 16">
                                            <path d="M3 2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V2zm6 11a1 1 0 1 0-2 0 1 1 0 0 0 2 0z"/>
                                        </svg>
                                    </span>
                                    <input type="tel" class="form-control" id="telefoninr" name="telefoninr" placeholder="51234567" required pattern="\d{7,20}" maxlength="20">
                                    <div class="invalid-feedback">
                                        Palun sisesta korrektne telefoninumber (ainult numbrid)
                                    </div>
                                </div>
                                <small class="text-muted">Ainult numbrid, maksimaalselt 20</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="isikukood" class="form-label">Isikukood <span class="text-danger">*</span></label>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-card-fill" viewBox="0 0 16 16">
                                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0V4zm0 3v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7H0zm3 2h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1z"/>
                                    </svg>
                                </span>
                                <input type="text" class="form-control" id="isikukood" name="isikukood" placeholder="Sisesta isikukood" required pattern="\d{11}" maxlength="11">
                                <div class="invalid-feedback">
                                    Palun sisesta korrektne isikukood (11 numbrit)
                                </div>
                            </div>
                            <small class="text-muted">Ainult numbrid, täpselt 11 numbrit</small>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="parool" class="form-label">Parool <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                        </svg>
                                    </span>
                                    <input type="password" class="form-control" id="parool" name="parool" placeholder="Sisesta parool" required minlength="8">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                        </svg>
                                    </button>
                                    <div class="invalid-feedback">
                                        Parool peab olema vähemalt 8 tähemärki pikk
                                    </div>
                                </div>
                                <div class="password-strength mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted">Parooli tugevus: <span class="strength-text">nõrk</span></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="parool2" class="form-label">Korda parooli <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text bg-light">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                        </svg>
                                    </span>
                                    <input type="password" class="form-control" id="parool2" name="parool2" placeholder="Korda parooli" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                        </svg>
                                    </button>
                                    <div class="invalid-feedback">
                                        Paroolid peavad ühtima
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block">Kinnita oma parool</small>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Nõustun <a href="#" class="text-decoration-none">kasutustingimustega</a> <span class="text-danger">*</span>
                            </label>
                            <div class="invalid-feedback">
                                Peate nõustuma kasutustingimustega
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-plus-fill me-2" viewBox="0 0 16 16">
                                <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
                            </svg>
                            Loo konto
                        </button>
                        
                        <div class="text-center mt-3">
                            <p class="mb-0">Juba kasutaja? <a href="../autentimine/login.php" class="text-decoration-none fw-bold">Logi sisse</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password toggle functionality
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.parentNode.querySelector('input');
        const icon = this.querySelector('svg');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-icon', 'eye-slash');
        } else {
            input.type = 'password';
            icon.setAttribute('data-icon', 'eye');
        }
    });
});

// Password strength indicator
const passwordInput = document.getElementById('parool');
const strengthBar = document.querySelector('.progress-bar');
const strengthText = document.querySelector('.strength-text');

passwordInput.addEventListener('input', function() {
    const strength = calculatePasswordStrength(this.value);
    const width = (strength.score / 4) * 100;
    let color = 'bg-danger';
    let text = 'Nõrk';
    
    if (strength.score > 2) {
        color = 'bg-warning';
        text = 'Keskmine';
    }
    if (strength.score > 3) {
        color = 'bg-success';
        text = 'Tugev';
    }
    
    strengthBar.style.width = width + '%';
    strengthBar.className = 'progress-bar ' + color;
    strengthText.textContent = text;
});

function calculatePasswordStrength(password) {
    let score = 0;
    
    // Length
    if (password.length > 0) score++;
    if (password.length >= 8) score++;
    
    // Contains both lower and upper case
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    
    // Contains numbers
    if (/\d/.test(password)) score++;
    
    // Contains special chars
    if (/[^a-zA-Z0-9]/.test(password)) score++;
    
    return { score };
}

// Form validation
(() => {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }

      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

<?php include '../includes/footer.php'; ?>