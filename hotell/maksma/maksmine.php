<?php
require_once('../includes/stripe.php');
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Kontrolli, et broneering on olemas sessioonis
if (!isset($_SESSION['broneering'])) {
    die("Broneering puudub!");
}

$broneering = $_SESSION['broneering'];
$toa_tyyp_id = intval($broneering["toa_tyyp_id"]);

$saabumine = $broneering["saabumine"];
$lahkumine = $broneering["lahkumine"];

// Leia toa info
$toa_info = mysqli_fetch_assoc(mysqli_query($yhendus, "SELECT * FROM toa_tyyp WHERE id = $toa_tyyp_id"));
if (!$toa_info) die("Toa tüüp ei leitud.");

$paevade_arv = max(1, (strtotime($lahkumine) - strtotime($saabumine)) / 86400);
$hind_toast = $toa_info["toa_hind"] * $paevade_arv;

// Teenuste hind ja nimed
$teenuste_summa = 0;
$teenuste_nimed = [];

if (!empty($broneering["teenused"]) && is_array($broneering["teenused"])) {
    $teenused_id = implode(",", array_map('intval', $broneering["teenused"]));
    $teenused_query = mysqli_query($yhendus, "SELECT * FROM teenused WHERE id IN ($teenused_id)");
    while ($teenus = mysqli_fetch_assoc($teenused_query)) {
        $teenuste_summa += $teenus["hind"];
        $teenuste_nimed[] = $teenus["teenus"];
    }
}

$summa_kokku = $hind_toast + $teenuste_summa;
$km = round($summa_kokku * 0.20, 2);
$lopp_summa = $summa_kokku + $km;

// Broneerija andmed
$eesnimi = mysqli_real_escape_string($yhendus, $broneering["eesnimi"]);
$perenimi = mysqli_real_escape_string($yhendus, $broneering["perenimi"]);
$telefon = mysqli_real_escape_string($yhendus, $broneering["telefon"]);
$isikukood = mysqli_real_escape_string($yhendus, $broneering["isikukood"]);
$email = mysqli_real_escape_string($yhendus, $broneering["email"]);

// Kas tegemist on kliendi või külalisega?
$is_klient = isset($_SESSION['kasutaja_id']); // Näiteks kui kasutaja on sisse loginud

// Määra õige success leht
$success_url = $is_klient ? 'success.php' : 'kylaline_success.php';

try {
    // Leia vaba tuba
    $vaba_tuba = mysqli_fetch_assoc(mysqli_query($yhendus, "
        SELECT id FROM toad
        WHERE toa_id = $toa_tyyp_id AND id NOT IN (
            SELECT toa_id FROM broneeringud
            WHERE NOT (lahkumine <= '$saabumine' OR saabumine >= '$lahkumine')
        )
        LIMIT 1
    "));
    if (!$vaba_tuba) die("Vaba tuba ei leitud!");

    $toa_id = $vaba_tuba["id"];

    // Lisa klient või külaline
    $klient_id = null;
    $kylaline_id = null;

    if ($is_klient) {
        // Kliendi kontroll või lisamine
        $kas_olemas = mysqli_fetch_assoc(mysqli_query($yhendus, "
            SELECT id FROM kliendid WHERE isikukood = '$isikukood' LIMIT 1
        "));
        if ($kas_olemas) {
            $klient_id = $kas_olemas["id"];
        } else {
            mysqli_query($yhendus, "
                INSERT INTO kliendid (eesnimi, perenimi, telefon, isikukood, kasutaja_id)
                VALUES ('$eesnimi', '$perenimi', '$telefon', '$isikukood', " . intval($_SESSION['kasutaja_id']) . ")
            ");
            $klient_id = mysqli_insert_id($yhendus);
        }
    } else {
        // Külalise lisamine
        mysqli_query($yhendus, "
            INSERT INTO kylalised (eesnimi, perenimi, telefon, isikukood, email)
            VALUES ('$eesnimi', '$perenimi', '$telefon', '$isikukood', '$email')
        ");
        $kylaline_id = mysqli_insert_id($yhendus);
    }

    // Lisa broneering
    mysqli_query($yhendus, "
        INSERT INTO broneeringud (klient_id, kylaline_id, toa_id, saabumine, lahkumine)
        VALUES (" . ($klient_id ?? 'NULL') . ", " . ($kylaline_id ?? 'NULL') . ", $toa_id, '$saabumine', '$lahkumine')
    ");
    $broneering_id = mysqli_insert_id($yhendus);

    // Lisa teenused
    if (!empty($broneering["teenused"])) {
        foreach ($broneering["teenused"] as $teenus_id) {
            $teenus_id = intval($teenus_id);
            $hind_t = mysqli_fetch_assoc(mysqli_query($yhendus, "SELECT hind FROM teenused WHERE id = $teenus_id"))["hind"];
            mysqli_query($yhendus, "
                INSERT INTO broneeringu_teenused (broneering_id, teenus_id, hind)
                VALUES ($broneering_id, $teenus_id, $hind_t)
            ");
        }
    }

    // Lisa makse
    $tahtaeg = date("Y-m-d", strtotime("+1 day"));
    mysqli_query($yhendus, "
        INSERT INTO maksed (broneering_id, summa, staatus, makseviis, stripe_id, tahtaeg)
        VALUES ($broneering_id, $lopp_summa, 'ootel', 'krediitkaart', '', '$tahtaeg')
    ");
    $makse_id = mysqli_insert_id($yhendus);

    // Stripe makse sessioon
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Hotelli broneering',
                    'description' => 'Tuba: ' . $toa_info["toa_tyyp"] . ($teenuste_nimed ? ', Teenused: ' . implode(", ", $teenuste_nimed) : '')
                ],
                'unit_amount' => intval(round($lopp_summa * 100)), // sentides
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'https://' . $_SERVER['HTTP_HOST'] . "/hotell/maksma/" . $success_url . "?session_id={CHECKOUT_SESSION_ID}",
        'cancel_url' => 'https://' . $_SERVER['HTTP_HOST'] . "/hotell/maksma/cancel.php",
    ]);

    // Uuenda stripe_id
    mysqli_query($yhendus, "
        UPDATE maksed SET stripe_id = '{$session->id}' WHERE id = $makse_id
    ");

    header("Location: " . $session->url);
    exit;

} catch (Exception $e) {
    echo "Stripe viga: " . htmlspecialchars($e->getMessage());
}