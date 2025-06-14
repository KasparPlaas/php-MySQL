<?php
// Ühendame vajalikud failid
ob_start();
require_once('../includes/header.php');

// Kontrollime, kas kasutaja on sisselogitud kliendina
if (!klient()) {
    header("Location: ../autentimine/login.php");
    exit;
}


// Funktsioon kasutaja andmete saamiseks
function kasutaja_andmed($kasutaja_id) {
    global $yhendus;
    
    $puhver = mysqli_real_escape_string($yhendus, $kasutaja_id);
    $paring = "SELECT kliendid.eesnimi, kliendid.perenimi, kliendid.telefon, kliendid.isikukood, kasutajad.email 
               FROM kliendid 
               JOIN kasutajad ON kliendid.kasutaja_id = kasutajad.id 
               WHERE kliendid.kasutaja_id = '$puhver'";
    
    $tulemus = mysqli_query($yhendus, $paring);
    return mysqli_fetch_assoc($tulemus);
}

// Vormi töötlemine
$vead = array();
$toad = array();
$teenused = array();
$naita_toad_modal = false;
$naita_teenused_modal = false;
$naita_ylevaade_modal = false;
// Saame kasutaja andmed
$kasutaja_andmed = kasutaja_andmed($_SESSION['kasutaja_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Puhverdame andmed
    $saabumine = mysqli_real_escape_string($yhendus, $_POST['saabumine']);
    $lahkumine = mysqli_real_escape_string($yhendus, $_POST['lahkumine']);
    $taiskasvanud = intval($_POST['taiskasvanud']);
    $lapsed = intval($_POST['lapsed']);
    $kylalisi_kokku = $taiskasvanud + $lapsed;
    
    // Kontrollime andmete korrektsust
    if (empty($saabumine)) {
        $vead[] = "Saabumise kuupäev on kohustuslik";
    }
    if (empty($lahkumine)) {
        $vead[] = "Lahkumise kuupäev on kohustuslik";
    }
    if (strtotime($saabumine) < strtotime(date('Y-m-d'))) {
        $vead[] = "Saabumine peab olema tulevikus";
    }
    if (strtotime($lahkumine) <= strtotime($saabumine)) {
        $vead[] = "Lahkumine peab olema hiljem kui saabumine";
    }
    if ($taiskasvanud < 1) {
        $vead[] = "Vähemalt üks täiskasvanu peab olema";
    }
    
    // Kui vigu pole, otsime vabu tube
    if (empty($vead) && isset($_POST['otsi_toad'])) {
        $paring = "SELECT toa_tyyp.id, toa_tyyp.toa_tyyp, toa_tyyp.toa_hind, toa_tyyp.toa_kirjeldus, toa_tyyp.toa_maht 
                  FROM toa_tyyp 
                  WHERE toa_tyyp.toa_maht >= $kylalisi_kokku
                  AND toa_tyyp.id NOT IN (
                      SELECT DISTINCT broneeringud.toa_id FROM broneeringud 
                      WHERE NOT (broneeringud.lahkumine <= '$saabumine' OR broneeringud.saabumine >= '$lahkumine')
                      AND broneeringud.toa_id IS NOT NULL
                  )
                  ORDER BY toa_tyyp.toa_hind ASC";
        
        $toad_tulemus = mysqli_query($yhendus, $paring);
        if ($toad_tulemus) {
            while ($rida = mysqli_fetch_assoc($toad_tulemus)) {
                $toad[] = $rida;
            }
        }
        
        $naita_toad_modal = true;
    }
    
    // Kui valiti tuba, siis küsime teenused
    if (isset($_POST['valitud_toa_id']) && empty($vead)) {
        $valitud_toa_id = intval($_POST['valitud_toa_id']);
        $teenused_tulemus = mysqli_query($yhendus, "SELECT * FROM teenused ORDER BY teenus");
        if ($teenused_tulemus) {
            while ($rida = mysqli_fetch_assoc($teenused_tulemus)) {
                $teenused[] = $rida;
            }
        }
        
        // Salvestame sessiooni broneeringu andmed
        $_SESSION['broneering'] = array(
            'saabumine' => $saabumine,
            'lahkumine' => $lahkumine,
            'taiskasvanud' => $taiskasvanud,
            'lapsed' => $lapsed,
            'toa_tyyp_id' => $valitud_toa_id,
            'kasutaja_id' => $_SESSION['kasutaja_id'],
            'eesnimi' => $kasutaja_andmed['eesnimi'],
            'perenimi' => $kasutaja_andmed['perenimi'],
            'telefon' => $kasutaja_andmed['telefon'],
            'isikukood' => $kasutaja_andmed['isikukood'],
            'email' => $kasutaja_andmed['email']
        );
        
        $naita_teenused_modal = true;
    }
    
    // Kui valiti teenused, siis näitame ülevaadet
    if (isset($_POST['jatka_teenustega']) && empty($vead)) {
        $valitud_teenused = array();
        if (isset($_POST['teenused']) && is_array($_POST['teenused'])) {
            foreach ($_POST['teenused'] as $teenus_id) {
                $valitud_teenused[] = intval($teenus_id);
            }
        }
        $_SESSION['broneering']['teenused'] = $valitud_teenused;
        
        // Arvutame hinnad
        $paevade_arv = (strtotime($lahkumine) - strtotime($saabumine)) / 86400;
        $toa_tyyp_id = $_SESSION['broneering']['toa_tyyp_id'];
        $toa_info_tulemus = mysqli_query($yhendus, "SELECT * FROM toa_tyyp WHERE id=$toa_tyyp_id");
        $toa_info = mysqli_fetch_assoc($toa_info_tulemus);
        $hind = $toa_info["toa_hind"] * $paevade_arv;
        
        $teenuste_hind = 0;
        $teenuste_nimed = array();
        
        if (!empty($valitud_teenused)) {
            $id_str = implode(",", $valitud_teenused);
            $tulemus = mysqli_query($yhendus, "SELECT * FROM teenused WHERE id IN ($id_str)");
            while ($rida = mysqli_fetch_assoc($tulemus)) {
                $teenuste_hind += $rida["hind"];
                $teenuste_nimed[] = $rida["teenus"];
            }
        }
        
        $km = 0.24 * ($hind + $teenuste_hind);
        $kokku = $hind + $teenuste_hind + $km;
        
        $_SESSION['broneering']['kokku'] = $kokku;
        $_SESSION['broneering']['teenuste_nimed'] = $teenuste_nimed;
        
        $naita_ylevaade_modal = true;
    }
    
    // Kui kinnitati broneering, suuname maksmisele
    if (isset($_POST['kinnita_broneering'])) {
        // Kontrolli, et kõik vajalikud andmed on olemas
        if (!empty($_SESSION['broneering']['toa_tyyp_id']) && 
            !empty($_SESSION['broneering']['saabumine']) && 
            !empty($_SESSION['broneering']['lahkumine'])) {
            
            // Andmed sessioonist
            $broneering = $_SESSION['broneering'];
            $toa_tyyp_id = intval($broneering['toa_tyyp_id']);
            $kasutaja_id = intval($broneering['kasutaja_id']);
            
            // Leia vaba tuba
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
                
                // Lisa broneering andmebaasi
                $lisamine = mysqli_query($yhendus, "
                    INSERT INTO broneeringud (klient_id, toa_id, saabumine, lahkumine, staatus)
                    VALUES (
                        (SELECT id FROM kliendid WHERE kasutaja_id = $kasutaja_id),
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
                    
                    // Lisa teenused, kui need on valitud
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
                    
                    // Arvuta makse summa
                    $paevade_arv = (strtotime($broneering['lahkumine']) - strtotime($broneering['saabumine'])) / 86400;
                    $toa_hind = mysqli_fetch_assoc(mysqli_query($yhendus, 
                        "SELECT toa_hind FROM toa_tyyp WHERE id = $toa_tyyp_id"))['toa_hind'];
                    $toa_summa = $paevade_arv * $toa_hind;
                    
                    $teenuste_summa = 0;
                    if (!empty($broneering['teenused'])) {
                        $id_str = implode(",", $broneering['teenused']);
                        $tulemus = mysqli_query($yhendus, "SELECT SUM(hind) AS summa FROM teenused WHERE id IN ($id_str)");
                        $teenuste_summa = mysqli_fetch_assoc($tulemus)['summa'];
                    }
                    
                    $km = 0.24 * ($toa_summa + $teenuste_summa);
                    $kokku = $toa_summa + $teenuste_summa + $km;
                    
                    // Lisa makse andmebaasi
                    $tahtaeg = date("Y-m-d", strtotime("+1 day"));
                    mysqli_query($yhendus, "
                        INSERT INTO maksed (broneering_id, summa, staatus, makseviis, tahtaeg)
                        VALUES ($broneering_id, $kokku, 'ootel', 'krediitkaart', '$tahtaeg')
                    ");
                    
                    // Salvesta broneeringu ID sessiooni
                    $_SESSION['broneering']['id'] = $broneering_id;
                    
                    // Suuna maksmisele
                    header("Location: ../maksma/maksmine.php");
                    exit;
                }
            }
        } else {
            $vead[] = "Broneeringu andmed on puudulikud. Palun alusta uuesti.";
        }
    }
}



?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Broneeri tuba</h2>
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
            
            <form method="post" class="row g-3 needs-validation" novalidate>
                <!-- Kasutaja andmed (automaatselt täidetud) -->
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-person-check me-2"></i>
                        Sinu andmed on automaatselt lisatud: 
                        <?= htmlspecialchars($kasutaja_andmed['eesnimi']).' '.htmlspecialchars($kasutaja_andmed['perenimi']) ?>
                    </div>
                </div>
                
                <!-- Broneerimise kuupäevad -->
                <div class="col-md-4">
                    <label for="saabumine" class="form-label">Saabumine</label>
                    <input type="date" id="saabumine" name="saabumine" class="form-control" 
                           min="<?= date('Y-m-d') ?>" required 
                           value="<?= isset($_POST['saabumine']) ? htmlspecialchars($_POST['saabumine']) : '' ?>">
                    <div class="invalid-feedback">Palun vali saabumise kuupäev</div>
                </div>
                
                <div class="col-md-4">
                    <label for="lahkumine" class="form-label">Lahkumine</label>
                    <input type="date" id="lahkumine" name="lahkumine" class="form-control" required 
                           value="<?= isset($_POST['lahkumine']) ? htmlspecialchars($_POST['lahkumine']) : '' ?>">
                    <div class="invalid-feedback">Palun vali lahkumise kuupäev</div>
                </div>
                
                <div class="col-md-2">
                    <label for="taiskasvanud" class="form-label">Täiskasvanuid</label>
                    <input type="number" id="taiskasvanud" name="taiskasvanud" class="form-control" 
                           min="1" value="<?= isset($_POST['taiskasvanud']) ? intval($_POST['taiskasvanud']) : 1 ?>" required>
                </div>
                
                <div class="col-md-2">
                    <label for="lapsed" class="form-label">Lapsi</label>
                    <input type="number" id="lapsed" name="lapsed" class="form-control" 
                           min="0" value="<?= isset($_POST['lapsed']) ? intval($_POST['lapsed']) : 0 ?>">
                </div>
                
                <div class="col-12">
                    <button type="submit" name="otsi_toad" value="1" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Otsi vabu tube
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal tubade valimiseks -->
<div class="modal fade" id="toadModal" tabindex="-1" aria-labelledby="toadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="toadModalLabel">Vali tuba</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sulge"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($toad)): ?>
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        <?php foreach ($toad as $tuba): ?>
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($tuba['toa_tyyp']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($tuba['toa_kirjeldus']) ?></p>
                                        <ul class="list-group list-group-flush mb-3">
                                            <li class="list-group-item"><strong>Hind:</strong> <?= htmlspecialchars($tuba['toa_hind']) ?> € öö kohta</li>
                                            <li class="list-group-item"><strong>Mahutab:</strong> <?= htmlspecialchars($tuba['toa_maht']) ?> inimest</li>
                                        </ul>
                                        <form method="post">
                                            <input type="hidden" name="saabumine" value="<?= htmlspecialchars($_POST['saabumine']) ?>">
                                            <input type="hidden" name="lahkumine" value="<?= htmlspecialchars($_POST['lahkumine']) ?>">
                                            <input type="hidden" name="taiskasvanud" value="<?= htmlspecialchars($_POST['taiskasvanud']) ?>">
                                            <input type="hidden" name="lapsed" value="<?= htmlspecialchars($_POST['lapsed']) ?>">
                                            <button type="submit" name="valitud_toa_id" value="<?= htmlspecialchars($tuba['id']) ?>" class="btn btn-success w-100">
                                                <i class="bi bi-check-circle me-2"></i>Vali see tuba
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Valitud perioodil pole sobivaid tube saadaval.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulge</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal teenuste valimiseks -->
<div class="modal fade" id="teenusedModal" tabindex="-1" aria-labelledby="teenusedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="teenusedModalLabel">Vali lisateenused</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sulge"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($teenused)): ?>
                    <form method="post" id="teenusedForm">
                        <input type="hidden" name="saabumine" value="<?= isset($_POST['saabumine']) ? htmlspecialchars($_POST['saabumine']) : '' ?>">
                        <input type="hidden" name="lahkumine" value="<?= isset($_POST['lahkumine']) ? htmlspecialchars($_POST['lahkumine']) : '' ?>">
                        <input type="hidden" name="taiskasvanud" value="<?= isset($_POST['taiskasvanud']) ? htmlspecialchars($_POST['taiskasvanud']) : '' ?>">
                        <input type="hidden" name="lapsed" value="<?= isset($_POST['lapsed']) ? htmlspecialchars($_POST['lapsed']) : '' ?>">
                        <input type="hidden" name="valitud_toa_id" value="<?= isset($_SESSION['broneering']['toa_tyyp_id']) ? htmlspecialchars($_SESSION['broneering']['toa_tyyp_id']) : '' ?>">
                        
                        <div class="row row-cols-1 row-cols-md-2 g-4">
                            <?php foreach ($teenused as $teenus): ?>
                                <div class="col">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="teenused[]" 
                                                    value="<?= htmlspecialchars($teenus['id']) ?>" 
                                                    id="teenus_<?= htmlspecialchars($teenus['id']) ?>">
                                                <label class="form-check-label w-100" for="teenus_<?= htmlspecialchars($teenus['id']) ?>">
                                                    <h5 class="card-title">
                                                        <?= htmlspecialchars($teenus['teenus']) ?>
                                                        <span class="badge bg-success float-end">
                                                            <?= number_format($teenus['hind'], 2, ',', ' ') ?> €
                                                        </span>
                                                    </h5>
                                                    <?php if (!empty($teenus['kirjeldus'])): ?>
                                                        <p class="card-text"><?= htmlspecialchars($teenus['kirjeldus']) ?></p>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="button" class="btn btn-outline-secondary me-md-2" data-bs-dismiss="modal">
                                <i class="bi bi-arrow-left me-2"></i>Tagasi
                            </button>
                            <button type="submit" name="jatka_teenustega" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Jätka
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Praegu ei ole lisateenuseid saadaval.
                    </div>
                    <form method="post">
                        <input type="hidden" name="jatka_teenustega" value="1">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-2"></i>Jätka ilma teenusteta
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal broneeringu ülevaade -->
<div class="modal fade" id="ylevaadeModal" tabindex="-1" aria-labelledby="ylevaadeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="ylevaadeModalLabel">Broneeringu ülevaade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($_SESSION['broneering'])): ?>
                    <?php 
                    $broneering = $_SESSION['broneering'];
                    $toa_tyyp_id = $broneering['toa_tyyp_id'];
                    $toa_info = mysqli_fetch_assoc(mysqli_query($yhendus, "SELECT * FROM toa_tyyp WHERE id=$toa_tyyp_id"));
                    $paevade_arv = (strtotime($broneering['lahkumine']) - strtotime($broneering['saabumine'])) / 86400;
                    $hind = $toa_info['toa_hind'] * $paevade_arv;
                    
                    $teenuste_hind = 0;
                    $teenuste_nimed = isset($broneering['teenuste_nimed']) ? $broneering['teenuste_nimed'] : [];
                    
                    if (!empty($broneering['teenused'])) {
                        $id_str = implode(",", $broneering['teenused']);
                        $tulemus = mysqli_query($yhendus, "SELECT * FROM teenused WHERE id IN ($id_str)");
                        while ($rida = mysqli_fetch_assoc($tulemus)) {
                            $teenuste_hind += $rida['hind'];
                            if (!in_array($rida['teenus'], $teenuste_nimed)) {
                                $teenuste_nimed[] = $rida['teenus'];
                            }
                        }
                    }
                    
                    $km = 0.24 * ($hind + $teenuste_hind);
                    $kokku = $hind + $teenuste_hind + $km;
                    ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4><i class="bi bi-house-door me-2"></i>Toa info</h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Tüüp:</strong> <?= htmlspecialchars($toa_info['toa_tyyp']) ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Ööde arv:</strong> <?= $paevade_arv ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Külalised:</strong> <?= $broneering['taiskasvanud'] ?> täisk., <?= $broneering['lapsed'] ?> last
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h4><i class="bi bi-calendar-event me-2"></i>Kuupäevad</h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Saabumine:</strong> <?= htmlspecialchars($broneering['saabumine']) ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Lahkumine:</strong> <?= htmlspecialchars($broneering['lahkumine']) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h4><i class="bi bi-plus-circle me-2"></i>Lisateenused</h4>
                        <?php if (!empty($teenuste_nimed)): ?>
                            <ul class="list-group">
                                <?php foreach ($teenuste_nimed as $teenus): ?>
                                    <li class="list-group-item"><?= htmlspecialchars($teenus) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Lisa teenuseid ei valitud
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h4><i class="bi bi-cash-stack me-2"></i>Hinna kalkulatsioon</h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Toa hind (<?= $paevade_arv ?> ööd):</span>
                                    <span><?= number_format($hind, 2, ',', ' ') ?> €</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Lisateenused:</span>
                                    <span><?= number_format($teenuste_hind, 2, ',', ' ') ?> €</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>KM (24%):</span>
                                    <span><?= number_format($km, 2, ',', ' ') ?> €</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center fw-bold fs-5">
                                    <span>Kokku:</span>
                                    <span class="text-success"><?= number_format($kokku, 2, ',', ' ') ?> €</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <form method="post">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-outline-secondary me-md-2" data-bs-dismiss="modal">
                                <i class="bi bi-arrow-left me-2"></i>Tagasi
                            </button>
                            <button type="submit" name="kinnita_broneering" class="btn btn-success">
                                <i class="bi bi-credit-card me-2"></i>Maksma
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Kuupäevade kontroll ja modalite automaatne kuvamine
    document.addEventListener('DOMContentLoaded', function() {
        const saabumineInput = document.getElementById('saabumine');
        const lahkumineInput = document.getElementById('lahkumine');
        
        // Seadista lahkumise minimaalne kuupäev vastavalt saabumisele
        function uuendaLahkumiseMin() {
            if (saabumineInput.value) {
                const saabumine = new Date(saabumineInput.value);
                saabumine.setDate(saabumine.getDate() + 1);
                
                const minLahkumine = saabumine.toISOString().split('T')[0];
                lahkumineInput.min = minLahkumine;
                
                // Kui praegu valitud lahkumine on liiga varajane, tühjenda see
                if (lahkumineInput.value && new Date(lahkumineInput.value) <= new Date(saabumineInput.value)) {
                    lahkumineInput.value = '';
                }
            }
        }
        
        // Kuula saabumise kuupäeva muutusi
        saabumineInput.addEventListener('change', uuendaLahkumiseMin);
        
        // Kontrolli kohe laadimisel, kui on juba väärtused
        uuendaLahkumiseMin();
        
        // Vormi valideerimine
        const vorm = document.querySelector('.needs-validation');
        if (vorm) {
            vorm.addEventListener('submit', function(event) {
                if (!vorm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                vorm.classList.add('was-validated');
            }, false);
        }
        
        // Automaatne modalite kuvamine vastavalt protsessi sammu
        <?php if ($naita_toad_modal): ?>
            // Kuva tubade valik
            setTimeout(function() {
                const toadModal = new bootstrap.Modal(document.getElementById('toadModal'));
                toadModal.show();
            }, 100);
        <?php endif; ?>
        
        <?php if ($naita_teenused_modal): ?>
            // Kuva teenuste valik
            setTimeout(function() {
                const teenusedModal = new bootstrap.Modal(document.getElementById('teenusedModal'));
                teenusedModal.show();
            }, 100);
        <?php endif; ?>
        
        <?php if ($naita_ylevaade_modal): ?>
            // Kuva broneeringu ülevaade
            setTimeout(function() {
                const ylevaadeModal = new bootstrap.Modal(document.getElementById('ylevaadeModal'));
                ylevaadeModal.show();
            }, 100);
        <?php endif; ?>
    });
</script>

<?php include('../includes/footer.php'); ?>