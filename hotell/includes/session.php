<?php
session_start();
include '../includes/andmebaas.php';

// Aktiivne kasutaja (registreeritud kasutaja)
function aktiivne_kasutaja() {
    global $yhendus;

    if (!isset($_SESSION['kasutaja_id'])) {
        return null;
    }

    $kasutaja_id = intval($_SESSION['kasutaja_id']);
    $kasutaja = null;

    $paring = mysqli_query($yhendus, "
    SELECT kasutajad.*, kliendid.eesnimi, kliendid.perenimi, kliendid.telefon, kliendid.isikukood 
    FROM kasutajad 
    LEFT JOIN kliendid ON kasutajad.id = kliendid.kasutaja_id 
    WHERE kasutajad.id = $kasutaja_id
    ");
    if ($paring && mysqli_num_rows($paring) > 0) {
        $kasutaja = mysqli_fetch_assoc($paring);

        // Kui klient, lisa ka kliendi andmed
        if ($kasutaja['roll'] === 'klient') {
            $kliendi_paring = mysqli_query($yhendus, "SELECT eesnimi, perenimi, telefon, isikukood FROM kliendid WHERE kasutaja_id = $kasutaja_id");
            if ($kliendi_paring && mysqli_num_rows($kliendi_paring) > 0) {
                $kliendi = mysqli_fetch_assoc($kliendi_paring);
                $kasutaja = array_merge($kasutaja, $kliendi);
                
                // Salvesta kliendiandmed sessiooni
                $_SESSION['kasutaja_andmed'] = [
                    'eesnimi' => $kliendi['eesnimi'],
                    'perenimi' => $kliendi['perenimi'],
                    'telefon' => $kliendi['telefon'],
                    'isikukood' => $kliendi['isikukood']
                ];
            }
        }

        // Salvesta põhiandmed sessiooni
        $_SESSION['kasutaja_pohiandmed'] = [
            'id' => $kasutaja['id'],
            'kasutajanimi' => $kasutaja['kasutajanimi'],
            'email' => $kasutaja['email'],
            'roll' => $kasutaja['roll']
        ];
    }

    return $kasutaja;
}

// Valideeri isikukood
function valideeri_isikukood($isikukood) {
    if (!preg_match('/^[0-9]{11}$/', $isikukood)) {
        return false;
    }
    
    $sugu_ja_sajand = (int)$isikukood[0];
    $aasta = (int)substr($isikukood, 1, 2);
    $kuu = (int)substr($isikukood, 3, 2);
    $paev = (int)substr($isikukood, 5, 2);
    
    // Kontrolli kuupäeva kehtivust
    if ($kuu < 1 || $kuu > 12 || $paev < 1 || $paev > 31) {
        return false;
    }
    
    return true;
}

// Valideeri telefoninumber
function valideeri_telefon($telefon) {
    return preg_match('/^[+]{0,1}[0-9]{7,12}$/', $telefon);
}

// Valideeri ees- ja perekonnanimi
function valideeri_nimi($nimi) {
    return preg_match('/^[A-Za-zÕÄÖÜõäöüšŽž\'-]{2,50}$/u', $nimi);
}

// Valideeri parool
function valideeri_parool($parool) {
    return strlen($parool) >= 8 && 
           preg_match('/[A-Z]/', $parool) && 
           preg_match('/[a-z]/', $parool) && 
           preg_match('/[0-9]/', $parool);
}

// Kontroll kas kasutaja on sisse logitud
function sisse_logitud() {
    return isset($_SESSION['kasutaja_id']);
}

// Rolli põhised kontrollid
function admin() {
    return sisse_logitud() && ($_SESSION['kasutaja_pohiandmed']['roll'] ?? '') === 'admin';
}

function tootaja() {
    return sisse_logitud() && in_array($_SESSION['kasutaja_pohiandmed']['roll'] ?? '', ['admin', 'töötaja']);
}

function klient() {
    return sisse_logitud() && ($_SESSION['kasutaja_pohiandmed']['roll'] ?? '') === 'klient';
}

function kasutajad() {
    return sisse_logitud() && in_array($_SESSION['kasutaja_pohiandmed']['roll'] ?? '', ['admin', 'töötaja', 'klient']);
}

function kylaline_sessioonis() {
    return isset($_SESSION['kylaline_id']) && !isset($_SESSION['kasutaja_id']);
}

// Funktsioon kasutajaandmete uuendamiseks sessioonis
function uuenda_kasutaja_sessioonis($andmed) {
    if (isset($_SESSION['kasutaja_andmed'])) {
        $_SESSION['kasutaja_andmed'] = array_merge($_SESSION['kasutaja_andmed'], $andmed);
    }
    
    if (isset($_SESSION['kasutaja_pohiandmed']) && isset($andmed['email'])) {
        $_SESSION['kasutaja_pohiandmed']['email'] = $andmed['email'];
    }
}
?>