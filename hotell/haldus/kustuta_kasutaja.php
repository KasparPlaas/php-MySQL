<?php
header('Content-Type: application/json');
include '../includes/session.php';

if (!admin()) {
    echo json_encode(['edu' => false, 'sonum' => 'Puuduvad õigused']);
    exit;
}

$kasutaja_id = $_POST['id'];

// Kustutame kasutaja
$paring = "DELETE FROM kasutajad WHERE id=$kasutaja_id";
if (mysqli_query($yhendus, $paring)) {
    echo json_encode(['edu' => true, 'sonum' => 'Kasutaja kustutatud']);
} else {
    echo json_encode(['edu' => false, 'sonum' => 'Viga: ' . mysqli_error($yhendus)]);
}

mysqli_close($yhendus);
?>