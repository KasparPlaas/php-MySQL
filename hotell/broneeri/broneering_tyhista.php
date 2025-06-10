<?php
require_once("../includes/andmebaas.php");
session_start();

if (!isset($_SESSION['broneering'])) {
    header("Location: ../avaleht/");
    exit;
}

$broneering = $_SESSION['broneering'];
$stripe_id = mysqli_real_escape_string($yhendus, $broneering['stripe_id'] ?? '');

if ($stripe_id) {
    mysqli_query($yhendus, "
        DELETE FROM maksed WHERE stripe_id = '$stripe_id';
    ");
    mysqli_query($yhendus, "
        DELETE FROM broneeringud 
        WHERE id IN (SELECT broneering_id FROM maksed WHERE stripe_id = '$stripe_id')
    ");
}

// Puhasta sessioon
unset($_SESSION['broneering']);

header("Location: ../avaleht/");
exit;
