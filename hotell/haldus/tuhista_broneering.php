<?php
header('Content-Type: application/json');
include '../includes/session.php';

$broneering_id = $_POST['id'];

// Kontrollime õigusi
if (admin() || tootaja()) {
    // Admin/töötaja saab tühistada
    $paring = "UPDATE broneeringud SET staatus='tühistatud' WHERE id=$broneering_id";
} else {
    // Klient saab tühistada ainult oma broneeringuid
    $kasutaja_id = $_SESSION['kasutaja_id'];
    $paring = "UPDATE broneeringud b
               JOIN kliendid k ON b.klient_id=k.id
               SET b.staatus='tühistatud'
               WHERE b.id=$broneering_id AND k.kasutaja_id=$kasutaja_id";
}

if (mysqli_query($yhendus, $paring)) {
    echo json_encode(['edu' => true, 'sonum' => 'Broneering tühistatud']);
} else {
    echo json_encode(['edu' => false, 'sonum' => 'Viga: ' . mysqli_error($yhendus)]);
}

mysqli_close($yhendus);
?>