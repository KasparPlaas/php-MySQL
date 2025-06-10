<?php
header('Content-Type: application/json');
include '../includes/session.php';

if (!admin()) {
    echo json_encode(['edu' => false, 'sonum' => 'Puuduvad õigused']);
    exit;
}

$kylaline_id = $_POST['id'];

// Kustutame külalise
$paring = "DELETE FROM kylalised WHERE id=$kylaline_id";
if (mysqli_query($yhendus, $paring)) {
    echo json_encode(['edu' => true, 'sonum' => 'Külaline kustutatud']);
} else {
    echo json_encode(['edu' => false, 'sonum' => 'Viga: ' . mysqli_error($yhendus)]);
}

mysqli_close($yhendus);
?>