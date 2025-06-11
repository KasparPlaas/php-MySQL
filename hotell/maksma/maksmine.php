<?php
require_once('../includes/stripe.php');

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Check if booking exists in session
if (!isset($_SESSION['broneering'])) {
    header("Location: ../broneeri/broneeri_klient.php");
    exit;
}

$broneering = $_SESSION['broneering'];
$vead = array();

// Calculate payment amount
$paevade_arv = (strtotime($broneering['lahkumine']) - strtotime($broneering['saabumine'])) / 86400;
$toa_tyyp_id = $broneering['toa_tyyp_id'];
$toa_info = mysqli_fetch_assoc(mysqli_query($yhendus, "SELECT * FROM toa_tyyp WHERE id=$toa_tyyp_id"));
$hind = $toa_info["toa_hind"] * $paevade_arv;

$teenuste_hind = 0;
$teenuste_nimed = isset($broneering['teenuste_nimed']) ? $broneering['teenuste_nimed'] : [];

if (!empty($broneering['teenused'])) {
    $id_str = implode(",", $broneering['teenused']);
    $tulemus = mysqli_query($yhendus, "SELECT * FROM teenused WHERE id IN ($id_str)");
    while ($rida = mysqli_fetch_assoc($tulemus)) {
        $teenuste_hind += $rida["hind"];
        if (!in_array($rida["teenus"], $teenuste_nimed)) {
            $teenuste_nimed[] = $rida["teenus"];
        }
    }
}

$km = 0.24 * ($hind + $teenuste_hind);
$kokku = $hind + $teenuste_hind + $km;

// Handle payment confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Find available room
        $vaba_tuba = mysqli_fetch_assoc(mysqli_query($yhendus, "
            SELECT id FROM toad
            WHERE toa_id = $toa_tyyp_id AND id NOT IN (
                SELECT toa_id FROM broneeringud
                WHERE NOT (lahkumine <= '{$broneering['saabumine']}' OR saabumine >= '{$broneering['lahkumine']}')
            )
            LIMIT 1
        "));
        
        if (!$vaba_tuba) {
            $vead[] = "Valitud perioodil pole vabu tube saadaval. Palun valige muu periood.";
        } else {
            $toa_id = $vaba_tuba['id'];
            
            // Add booking to database
            $lisamine = mysqli_query($yhendus, "
                INSERT INTO broneeringud (klient_id, toa_id, saabumine, lahkumine, staatus)
                VALUES (
                    (SELECT id FROM kliendid WHERE kasutaja_id = " . intval($broneering['kasutaja_id']) . "),
                    $toa_id,
                    '{$broneering['saabumine']}',
                    '{$broneering['lahkumine']}',
                    'ootel'
                )
            ");
            
            if (!$lisamine) {
                $vead[] = "Broneeringu lisamine ebaõnnestus: " . mysqli_error($yhendus);
            } else {
                $broneering_id = mysqli_insert_id($yhendus);
                
                // Add services if selected
                if (!empty($broneering['teenused'])) {
                    foreach ($broneering['teenused'] as $teenus_id) {
                        $teenus_id = intval($teenus_id);
                        $hind = mysqli_fetch_assoc(mysqli_query($yhendus, 
                            "SELECT hind FROM teenused WHERE id = $teenus_id"))['hind'];
                        
                        mysqli_query($yhendus, "
                            INSERT INTO broneeringu_teenused (broneering_id, teenus_id, hind)
                            VALUES ($broneering_id, $teenus_id, $hind)
                        ");
                    }
                }
                
                // Handle payment based on selected method
                if (isset($_POST['kinnita_makse'])) {
                    $makseviis = $_POST['makseviis'];
                    $tahtaeg = date("Y-m-d", strtotime("+1 day"));
                    
                    if ($makseviis == 'krediitkaart') {
                        // Add payment to database for card payment
                        mysqli_query($yhendus, "
                            INSERT INTO maksed (broneering_id, summa, staatus, makseviis, tahtaeg)
                            VALUES ($broneering_id, $kokku, 'ootel', 'krediitkaart', '$tahtaeg')
                        ");
                        $makse_id = mysqli_insert_id($yhendus);
                        
                        // Create Stripe payment session
                        $session = \Stripe\Checkout\Session::create([
                            'payment_method_types' => ['card'],
                            'line_items' => [[
                                'price_data' => [
                                    'currency' => 'eur',
                                    'product_data' => [
                                        'name' => 'Hotelli broneering #' . $broneering_id,
                                        'description' => 'Tuba: ' . $toa_info['toa_tyyp'] . 
                                                       (!empty($teenuste_nimed) ? ', Teenused: ' . implode(", ", $teenuste_nimed) : '')
                                    ],
                                    'unit_amount' => intval(round($kokku * 100)), // in cents
                                ],
                                'quantity' => 1,
                            ]],
                            'mode' => 'payment',
                            'success_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/hotell/maksma/success_pangakaardiga.php?session_id={CHECKOUT_SESSION_ID}',
                            'cancel_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/hotell/maksma/cancel.php',
                            'customer_email' => $broneering['email'],
                            'metadata' => [
                                'broneering_id' => $broneering_id,
                                'makse_id' => $makse_id
                            ],
                        ]);
                        
                        // Update stripe_id in database
                        mysqli_query($yhendus, "
                            UPDATE maksed 
                            SET stripe_id = '" . mysqli_real_escape_string($yhendus, $session->id) . "'
                            WHERE id = $makse_id
                        ");
                        
                        // Redirect to Stripe
                        header("Location: " . $session->url);
                        exit;
                    } elseif ($makseviis == 'sularaha') {
                        // Add payment to database for cash payment
                        mysqli_query($yhendus, "
                            INSERT INTO maksed (broneering_id, summa, staatus, makseviis, tahtaeg)
                            VALUES ($broneering_id, $kokku, 'ootel', 'sularaha', '$tahtaeg')
                        ");
                        
                        // Redirect to cash payment success page
                        header("Location: success_sularahas.php?broneering_id=$broneering_id");
                        exit;
                    }
                }
            }
        }
    } catch (Exception $e) {
        $vead[] = "Tekkis viga makse protsessi käivitamisel: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makse | Hotell</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .payment-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .payment-card {
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .payment-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 1.5rem;
        }
        .price-badge {
            font-size: 1.5rem;
            font-weight: 700;
            background-color: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }
        .btn-pay {
            background: linear-gradient(135deg, #635bff, #4a42d4);
            border: none;
            padding: 12px 24px;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-pay:hover {
            background: linear-gradient(135deg, #4a42d4, #3a32c4);
            transform: translateY(-2px);
        }
        .btn-cash {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            padding: 12px 24px;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-cash:hover {
            background: linear-gradient(135deg, #495057, #343a40);
            transform: translateY(-2px);
        }
        .payment-method {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .payment-method.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .payment-method input[type="radio"] {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container py-5 payment-container">
        <div class="payment-card mb-4">
            <div class="payment-header text-center">
                <h2><i class="bi bi-credit-card"></i> Makse</h2>
                <p class="mb-0">Broneeringu kinnitamine</p>
            </div>
            
            <div class="card-body">
                <?php if (!empty($vead)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($vead as $viga): ?>
                                <li><?= htmlspecialchars($viga) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-4"><i class="bi bi-journal-text"></i> Broneeringu üksikasjad</h4>
                        
                        <div class="mb-4">
                            <h5><?= htmlspecialchars($toa_info['toa_tyyp']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($toa_info['toa_kirjeldus']) ?></p>
                            <div class="d-flex justify-content-between">
                                <span>Ööde arv:</span>
                                <strong><?= $paevade_arv ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Hind öö kohta:</span>
                                <strong><?= number_format($toa_info['toa_hind'], 2, ',', ' ') ?> €</strong>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5><i class="bi bi-calendar-event"></i> Kuupäevad</h5>
                            <div class="d-flex justify-content-between">
                                <span>Saabumine:</span>
                                <strong><?= date('d.m.Y', strtotime($broneering['saabumine'])) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Lahkumine:</span>
                                <strong><?= date('d.m.Y', strtotime($broneering['lahkumine'])) ?></strong>
                            </div>
                        </div>
                        
                        <?php if (!empty($teenuste_nimed)): ?>
                            <h5><i class="bi bi-plus-circle"></i> Lisateenused</h5>
                            <ul class="list-group mb-4">
                                <?php foreach ($teenuste_nimed as $teenus): ?>
                                    <li class="list-group-item"><?= htmlspecialchars($teenus) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="sticky-top" style="top: 20px;">
                            <h4 class="mb-4"><i class="bi bi-receipt"></i> Makse kokkuvõte</h4>
                            
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Toa hind:</span>
                                        <span><?= number_format($hind, 2, ',', ' ') ?> €</span>
                                    </div>
                                    
                                    <?php if (!empty($teenuste_nimed)): ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Lisateenused:</span>
                                            <span><?= number_format($teenuste_hind, 2, ',', ' ') ?> €</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>KM (24%):</span>
                                        <span><?= number_format($km, 2, ',', ' ') ?> €</span>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between fw-bold fs-5">
                                        <span>Kokku:</span>
                                        <span class="text-success"><?= number_format($kokku, 2, ',', ' ') ?> €</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-credit-card"></i> Makseviis</h5>
                                    
                                    <form method="post" id="paymentForm">
                                        <div class="mb-4">
                                            <div class="payment-method">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="makseviis" id="cardPayment" value="krediitkaart" checked>
                                                    <label class="form-check-label fw-bold" for="cardPayment">
                                                        Pangakaardiga makse
                                                    </label>
                                                </div>
                                                <p class="text-muted mt-2 mb-0">Makse tehakse turvalise Stripe maksekeskuse kaudu.</p>
                                                <div class="d-flex justify-content-center mt-2">
                                                    <img src="https://stripe.com/img/v3/payments/overview/photos/cards.jpg" alt="Maksekaardid" style="height: 30px;">
                                                </div>
                                            </div>
                                            
                                            <div class="payment-method">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="makseviis" id="cashPayment" value="sularaha">
                                                    <label class="form-check-label fw-bold" for="cashPayment">
                                                        Sularahas makse
                                                    </label>
                                                </div>
                                                <p class="text-muted mt-2 mb-0">Broneering kinnitatakse alles siis, kui olete kohapeal sularahas tasunud.</p>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" name="kinnita_makse" class="btn btn-pay w-100 py-3">
                                            <i class="bi bi-lock-fill"></i> Kinnita broneering
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <a href="../broneeri/broneeri_klient.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Tagasi broneeringu juurde
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Highlight selected payment method
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('selected');
                });
                this.classList.add('selected');
            });
            
            // Initialize selected state
            if (method.querySelector('input[type="radio"]:checked')) {
                method.classList.add('selected');
            }
        });
        
        // Change button text based on payment method
        document.getElementById('paymentForm').addEventListener('change', function(e) {
            const submitBtn = document.querySelector('button[name="kinnita_makse"]');
            if (e.target.id === 'cashPayment') {
                submitBtn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Kinnita sularahamakse';
                submitBtn.className = 'btn btn-cash w-100 py-3';
            } else if (e.target.id === 'cardPayment') {
                submitBtn.innerHTML = '<i class="bi bi-lock-fill"></i> Maksa pangakaardiga';
                submitBtn.className = 'btn btn-pay w-100 py-3';
            }
        });
    </script>
</body>
</html>

<?php include('../includes/footer.php'); ?>