<?php

// Veateadete seadistamine (arendusrežiimis)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Üldised seaded
define('BASE_URL', 'https://kplaas.ee/hotell');

// E-posti seaded
define('EMAIL_HOST', 'mail.veebimajutus.ee');
define('EMAIL_USERNAME', 'verification@kplaas.ee');
define('EMAIL_PASSWORD', 'Passw0rd');
define('EMAIL_PORT', 465);

// Verifitseerimise e-posti seaded
define('VERIFICATION_EMAIL_FROM', 'verify@kplaas.ee');
define('VERIFICATION_EMAIL_FROM_NAME', 'kplaas Hotell - Verifitseerimine');

// Maksekinnituste e-posti seaded
define('PAYMENT_EMAIL_FROM', 'maksed@kplaas.ee');
define('PAYMENT_EMAIL_FROM_NAME', 'kplaas Hotell - Maksekinnitus');

// Parooli taastamise e-posti seaded
define('PASSWORD_RESET_EMAIL_FROM', 'taastamine@kplaas.ee');
define('PASSWORD_RESET_EMAIL_FROM_NAME', 'kplaas Hotell - Parooli taastamine');

?>