<?php
include '../includes/header.php';
include '../includes/email.php';
require '../autentimine/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$veateade = "";
$onnestus = "";

if (isset($_GET["email"])) {
    $email = mysqli_real_escape_string($yhendus, $_GET["email"]);

    $kysitav = mysqli_query($yhendus, "SELECT id, eesnimi, email_kinnitatud, email_koodi_aeg FROM kasutajad 
        LEFT JOIN kliendid ON kasutajad.id = kliendid.kasutaja_id
        WHERE email = '$email'");

    if ($rida = mysqli_fetch_assoc($kysitav)) {
        if ($rida['email_kinnitatud']) {
            $veateade = "Konto on juba kinnitatud.";
        } else {
            $viimane = strtotime($rida['email_koodi_aeg'] ?? '1970-01-01 00:00:00');
            if ((time() - $viimane) < 300) {
                $veateade = "Kood saadeti vähem kui 5 min tagasi. Palun oota.";
            } else {
                // Saadame uue koodi
                $uus_kood = bin2hex(random_bytes(16));
                $aeg_nyyd = date("Y-m-d H:i:s");

                mysqli_query($yhendus, "
                    UPDATE kasutajad 
                    SET email_kinnituskood = '$uus_kood', email_koodi_aeg = '$aeg_nyyd'
                    WHERE email = '$email'
                ");

                $link = "https://kplaas.ee/kinnitus.php?kood=$uus_kood";
                $nimi = $rida['eesnimi'] ?? 'Kasutaja';

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
                    $mail->addAddress($email, $nimi);
                    $mail->isHTML(true);
                    $mail->Subject = "Kinnita oma konto - kplaas";
                    $mail->Body = "Tere $nimi,<br><br>Klikka lingil, et kinnitada konto:<br><a href='$link'>$link</a><br><br>Kui see polnud sina, võid selle e-kirja ignoreerida.";

                    $mail->send();
                    $onnestus = "Kood saadeti uuesti e-posti aadressile.";
                } catch (Exception $e) {
                    $veateade = "Koodi saatmine ebaõnnestus.";
                }
            }
        }
    } else {
        $veateade = "Kasutajat ei leitud.";
    }
} else {
    $veateade = "E-maili parameeter puudub.";
}
?>

<div class="container mt-5" style="max-width: 500px;">
    <?php if ($veateade): ?>
        <div class="alert alert-danger"><?= $veateade ?></div>
    <?php elseif ($onnestus): ?>
        <div class="alert alert-success"><?= $onnestus ?></div>
    <?php endif; ?>
    <a href="../autentimine/login.php" class="btn btn-primary">Tagasi sisselogimisse</a>
</div>

<?php include '../includes/footer.php'; ?>
