<?php
include '../includes/header.php';
include '../includes/email.php';
require '../autentimine/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$veateade = "";
$onnestus = "";

if (isset($_GET['email'])) {
    $email = mysqli_real_escape_string($yhendus, $_GET['email']);

    $kysitav = mysqli_query($yhendus, "SELECT id FROM kasutajad WHERE email = '$email'");
    if ($rida = mysqli_fetch_assoc($kysitav)) {
        $reset_kood = bin2hex(random_bytes(16));
        $aeg_nyyd = date("Y-m-d H:i:s");

        mysqli_query($yhendus, "
            UPDATE kasutajad 
            SET reset_kood = '$reset_kood', reset_kood_aeg = '$aeg_nyyd'
            WHERE email = '$email'
        ");

        $link = BASE_URL . "/autentimine/vaheta_parool.php?kood=$reset_kood";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = EMAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = EMAIL_USERNAME;
            $mail->Password = EMAIL_PASSWORD;
            $mail->SMTPSecure = 'ssl';
            $mail->Port = EMAIL_PORT;

            $mail->setFrom(PASSWORD_RESET_EMAIL_FROM, PASSWORD_RESET_EMAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Parooli taastamine – kplaas";
            $mail->Body = "Tere,<br><br>Parooli muutmiseks klikka lingil:<br><a href='$link'>$link</a><br><br>Kui see ei olnud sina, ignoreeri seda kirja.";

            $mail->send();
            $onnestus = "Taastelink saadeti e-posti.";
        } catch (Exception $e) {
            $veateade = "Viga: e-kirja saatmine ebaõnnestus.";
        }
    } else {
        $veateade = "Kasutajat selle e-posti aadressiga ei leitud.";
    }
} else {
    $veateade = "E-posti aadress on puudu.";
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
