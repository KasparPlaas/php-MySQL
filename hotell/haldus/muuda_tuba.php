<?php
header('Content-Type: application/json');
include '../includes/session.php';

if (!admin()) {
    echo json_encode(['edu' => false, 'sonum' => 'Puuduvad õigused']);
    exit;
}

// Võtame andmed
$tuba_id = $_POST['id'];
$toa_nr = $_POST['toa_nr'];
$korrus = $_POST['korrus'];
$toa_tyyp_id = $_POST['toa_tyyp'];

// Uuendame tuba
$paring = "UPDATE toad 
           SET toa_id=$toa_tyyp_id, toa_nr='$toa_nr', toa_korrus=$korrus 
           WHERE id=$tuba_id";

if (mysqli_query($yhendus, $paring)) {
    echo json_encode(['edu' => true, 'sonum' => 'Toa andmed uuendatud']);
} else {
    echo json_encode(['edu' => false, 'sonum' => 'Viga: ' . mysqli_error($yhendus)]);
}

mysqli_close($yhendus);
?>