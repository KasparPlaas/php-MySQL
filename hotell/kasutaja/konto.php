<?php
include '../includes/session.php';

if (!sisse_logitud()) {
    header("Location: ../autentimine/login.php");
    exit();
}

$kasutaja_id = intval($_SESSION['kasutaja_id']);

// Sisendi puhastamine
$eesnimi = mysqli_real_escape_string($yhendus, $_POST['eesnimi'] ?? '');
$perenimi = mysqli_real_escape_string($yhendus, $_POST['perenimi'] ?? '');
$email = mysqli_real_escape_string($yhendus, $_POST['email'] ?? '');
$telefon = mysqli_real_escape_string($yhendus, $_POST['telefon'] ?? '');
$isikukood = mysqli_real_escape_string($yhendus, $_POST['isikukood'] ?? '');
$parool = $_POST['parool'] ?? '';

// Kontrolli, et kõik väljad peale parooli oleks täidetud
$vigane = empty($eesnimi) || empty($perenimi) || empty($email) || empty($telefon) || empty($isikukood);

if ($vigane) {
    die("Kõik väljad peale parooli on kohustuslikud.");
}

// Uuenda kasutaja e‑posti ja vajadusel parooli
if (!empty($parool)) {
    $krüpteeritud = password_hash($parool, PASSWORD_DEFAULT);
    mysqli_query($yhendus, "UPDATE kasutajad SET email = '$email', parool = '$krüpteeritud' WHERE id = $kasutaja_id");
} else {
    mysqli_query($yhendus, "UPDATE kasutajad SET email = '$email' WHERE id = $kasutaja_id");
}

// Uuenda kliendi andmed (eesnimi, perenimi, telefon, isikukood)
mysqli_query($yhendus, "
    UPDATE kliendid SET
        eesnimi = '$eesnimi',
        perenimi = '$perenimi',
        telefon = '$telefon',
        isikukood = '$isikukood'
    WHERE kasutaja_id = $kasutaja_id
");

// Suuna tagasi
header("Location: ../avaleht/?muudatused=onnestus");
exit();
?>
