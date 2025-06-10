<?php
// Andmebaasi ühenduse seaded
$server = 'localhost';
$andmebaas = 'vhost137852s1';
$kasutaja = 'vhost137852s1';
$parool = '';

// Loome ühenduse andmebaasiga
$yhendus = new mysqli($server, $kasutaja, $parool, $andmebaas);

// Kontrollime ühendust
if ($yhendus->connect_error) {
    die("Ühenduse viga: " . $yhendus->connect_error);
}

// Määrame märgistikuks UTF-8
$yhendus->set_charset("utf8mb4");

?>