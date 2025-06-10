<?php
header('Content-Type: application/json');
include '../includes/session.php';

if (!admin()) {
    echo json_encode(['edu' => false, 'sonum' => 'Puuduvad õigused']);
    exit;
}

$tuba_id = $_POST['id'];

// Kustutame toa
$paring = "DELETE FROM toad WHERE id=$tuba_id";
if (mysqli_query($yhendus, $paring)) {
    echo json_encode(['edu' => true, 'sonum' => 'Tuba kustutatud']);
} else {
    echo json_encode(['edu' => false, 'sonum' => 'Viga: ' . mysqli_error($yhendus)]);
}

mysqli_close($yhendus);
?>