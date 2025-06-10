<?php
header('Content-Type: application/json');
include '../includes/session.php';

if (!admin()) {
    echo json_encode(['edu' => false, 'sonum' => 'Puuduvad õigused']);
    exit;
}

// Võtame andmed
$kasutaja_id = $_POST['id'];
$kasutajanimi = $_POST['kasutajanimi'];
$email = $_POST['email'];
$roll = $_POST['roll'];
$eesnimi = $_POST['eesnimi'];
$perenimi = $_POST['perenimi'];
$telefon = $_POST['telefon'];

// Uuendame kasutajat
$paring = "UPDATE kasutajad 
           SET kasutajanimi='$kasutajanimi', email='$email', roll='$roll' 
           WHERE id=$kasutaja_id";
mysqli_query($yhendus, $paring);

// Uuendame kliendi andmeid
$paring = "UPDATE kliendid 
           SET eesnimi='$eesnimi', perenimi='$perenimi', telefon='$telefon' 
           WHERE kasutaja_id=$kasutaja_id";
mysqli_query($yhendus, $paring);

echo json_encode(['edu' => true, 'sonum' => 'Kasutaja andmed uuendatud']);
mysqli_close($yhendus);
?>