<?php
header('Content-Type: application/json');
include '../includes/session.php';

if (!admin()) {
    echo json_encode(['edu' => false, 'sonum' => 'Puuduvad õigused']);
    exit;
}

// Võtame andmed
$toa_nr = $_POST['toa_nr'];
$korrus = $_POST['korrus'];
$toa_tyyp_id = $_POST['toa_tyyp'];

// Lisame uue toa
$paring = "INSERT INTO toad (toa_id, toa_nr, toa_korrus) 
           VALUES ($toa_tyyp_id, '$toa_nr', $korrus)";

if (mysqli_query($yhendus, $paring)) {
    echo json_encode(['edu' => true, 'sonum' => 'Tuba lisatud']);
} else {
    echo json_encode(['edu' => false, 'sonum' => 'Viga: ' . mysqli_error($yhendus)]);
}

mysqli_close($yhendus);
?>