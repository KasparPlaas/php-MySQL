<?php
include '../includes/session.php';

// Logime kasutaja välja
session_start();
session_unset();
session_destroy();

// Suuname tagasi sisse logimise lehele
header("Location: ../autentimine/login.php");
exit;
