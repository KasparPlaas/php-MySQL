<?php
include '../includes/session.php';

if (!sisse_logitud()) {
    http_response_code(403);
    exit('Lubamatu');
}

$broneering_id = intval($_POST['broneering_id'] ?? 0);

if ($broneering_id > 0) {
    $uuendus = mysqli_query($yhendus, "
        UPDATE broneeringud
        SET staatus = 'tühistatud'
        WHERE id = $broneering_id
        LIMIT 1
    ");
    if ($uuendus) {
        echo 'OK';
    } else {
        http_response_code(500);
        echo 'Andmebaasiviga';
    }
} else {
    http_response_code(400);
    echo 'Vigane ID';
}
