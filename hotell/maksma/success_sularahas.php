<?php
require_once('../includes/andmebaas.php');

// Check if booking_id is provided
if (!isset($_GET['broneering_id']) || !is_numeric($_GET['broneering_id'])) {
    header("Location: ../broneeri/broneeri_klient.php");
    exit;
}

$broneering_id = intval($_GET['broneering_id']);

// Get booking details
$broneering_query = mysqli_query($yhendus, "
    SELECT b.*, t.toa_tyyp, t.toa_hind, k.eesnimi, k.perenimi, k.email
    FROM broneeringud b
    JOIN toad r ON b.toa_id = r.id
    JOIN toa_tyyp t ON r.toa_id = t.id
    JOIN kliendid k ON b.klient_id = k.id
    WHERE b.id = $broneering_id
");

if (mysqli_num_rows($broneering_query) == 0) {
    header("Location: ../broneeri/broneeri_klient.php");
    exit;
}

$broneering = mysqli_fetch_assoc($broneering_query);

// Get services for this booking
$teenused_query = mysqli_query($yhendus, "
    SELECT t.teenus, bt.hind
    FROM broneeringu_teenused bt
    JOIN teenused t ON bt.teenus_id = t.id
    WHERE bt.broneering_id = $broneering_id
");

$teenused = array();
$teenuste_kokku = 0;
while ($teenus = mysqli_fetch_assoc($teenused_query)) {
    $teenused[] = $teenus;
    $teenuste_kokku += $teenus['hind'];
}

// Calculate totals
$paevade_arv = (strtotime($broneering['lahkumine']) - strtotime($broneering['saabumine'])) / 86400;
$toa_hind_kokku = $broneering['toa_hind'] * $paevade_arv;
$km = 0.24 * ($toa_hind_kokku + $teenuste_kokku);
$kokku = $toa_hind_kokku + $teenuste_kokku + $km;
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broneering kinnitatud | Hotell</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .success-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .success-card {
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .success-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 1.5rem;
        }
        .receipt {
            border: 1px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline:before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #28a745;
        }
        .timeline-step {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-step:last-child {
            margin-bottom: 0;
        }
        .timeline-step:before {
            content: '';
            position: absolute;
            left: -30px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #28a745;
            border: 3px solid white;
        }
        .btn-download {
            background: linear-gradient(135deg, #17a2b8, #138496);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
        }
        .btn-download:hover {
            background: linear-gradient(135deg, #138496, #117a8b);
        }
    </style>
</head>
<body>
    <div class="container py-5 success-container">
        <div class="success-card mb-4">
            <div class="success-header text-center">
                <h2><i class="bi bi-check-circle"></i> Broneering kinnitatud!</h2>
                <p class="mb-0">Broneering #<?= $broneering_id ?> on edukalt loodud</p>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h5><i class="bi bi-info-circle"></i> Oluline teave</h5>
                            <p>Olete valinud sularahamakse. Broneering kinnitatakse lõplikult alles siis, kui olete kohapeal sularahas tasunud.</p>
                            <p class="mb-0">Saadame teile kinnituse meilile aadressile <strong><?= htmlspecialchars($broneering['email']) ?></strong>.</p>
                        </div>
                        
                        <h4 class="mb-3"><i class="bi bi-clock-history"></i> Edasised sammud</h4>
                        
                        <div class="timeline">
                            <div class="timeline-step">
                                <h5>1. Saate meilile kinnituse</h5>
                                <p class="text-muted">Broneeringu üksikasjad saadetakse teie meilile kohe peale seda lehte.</p>
                            </div>
                            <div class="timeline-step">
                                <h5>2. Kohapeal tasumine</h5>
                                <p class="text-muted">Palume tasuda broneeringu eest kohapeal vastuvõtus enne kella 18:00.</p>
                            </div>
                            <div class="timeline-step">
                                <h5>3. Broneeringu kinnitamine</h5>
                                <p class="text-muted">Pärast makse laekumist saadame teile lõpliku kinnituse.</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="../index.php" class="btn btn-success">
                                <i class="bi bi-house"></i> Tagasi avalehele
                            </a>
                            <button class="btn btn-download ms-2" onclick="window.print()">
                                <i class="bi bi-download"></i> Laadi alla PDF
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="receipt">
                            <h4 class="text-center mb-4"><i class="bi bi-receipt"></i> Broneeringu kviitung</h4>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Broneeringu nr:</span>
                                <strong>#<?= $broneering_id ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Kuupäev:</span>
                                <strong><?= date('d.m.Y H:i') ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Klient:</span>
                                <strong><?= htmlspecialchars($broneering['eesnimi'] . ' ' . $broneering['perenimi']) ?></strong>
                            </div>
                            
                            <hr>
                            
                            <h5 class="mb-3">Broneeringu andmed</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tuba:</span>
                                <strong><?= htmlspecialchars($broneering['toa_tyyp']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Saabumine:</span>
                                <strong><?= date('d.m.Y', strtotime($broneering['saabumine'])) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Lahkumine:</span>
                                <strong><?= date('d.m.Y', strtotime($broneering['lahkumine'])) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ööde arv:</span>
                                <strong><?= $paevade_arv ?></strong>
                            </div>
                            
                            <?php if (!empty($teenused)): ?>
                                <hr>
                                <h5 class="mb-3">Lisateenused</h5>
                                <?php foreach ($teenused as $teenus): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><?= htmlspecialchars($teenus['teenus']) ?>:</span>
                                        <strong><?= number_format($teenus['hind'], 2, ',', ' ') ?> €</strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Toa hind kokku:</span>
                                <strong><?= number_format($toa_hind_kokku, 2, ',', ' ') ?> €</strong>
                            </div>
                            <?php if (!empty($teenused)): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Lisateenused kokku:</span>
                                    <strong><?= number_format($teenuste_kokku, 2, ',', ' ') ?> €</strong>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>KM (24%):</span>
                                <strong><?= number_format($km, 2, ',', ' ') ?> €</strong>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>KOKKU:</span>
                                <span class="text-success"><?= number_format($kokku, 2, ',', ' ') ?> €</span>
                            </div>
                            
                            <hr>
                            
                            <div class="text-center text-muted small mt-3">
                                <p class="mb-1">Broneeringu staatus: OOTEL</p>
                                <p class="mb-0">Makseviis: SULARAHA (tasuda kohapeal)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <p class="text-muted">Kui teil on küsimusi, võtke meiega ühendust aadressil <a href="mailto:info@hotell.ee">info@hotell.ee</a> või telefonil <a href="tel:+3721234567">+372 123 4567</a>.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Send email confirmation (this would typically be done server-side in a real application)
        function sendConfirmationEmail() {
            // In a real app, this would be an AJAX call to a server-side script
            console.log('Email confirmation would be sent to <?= $broneering["email"] ?>');
        }
        
        // Call the function when page loads
        window.onload = sendConfirmationEmail;
    </script>
</body>
</html>