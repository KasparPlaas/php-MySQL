<?php
header('Content-Type: application/json');
include '../includes/session.php';

// Kontrollime kas on admin või töötaja
if (!admin() && !tootaja()) {
    echo json_encode(['edu' => false, 'sonum' => 'Puuduvad õigused']);
    exit;
}

// Võtame sisendid
$broneering_id = $_POST['id'];
$saabumine = $_POST['saabumine'];
$lahkumine = $_POST['lahkumine'];
$staatus = $_POST['staatus'];

// Uuendame broneeringut
$paring = "UPDATE broneeringud 
           SET saabumine='$saabumine', lahkumine='$lahkumine', staatus='$staatus' 
           WHERE id=$broneering_id";

if (mysqli_query($yhendus, $paring)) {
    echo json_encode(['edu' => true, 'sonum' => 'Broneering muudetud']);
} else {
    echo json_encode(['edu' => false, 'sonum' => 'Viga: ' . mysqli_error($yhendus)]);
}

mysqli_close($yhendus);
?>