<?php
include("../includes/header.php");

if (!isset($_SESSION["broneering"])) {
    header("Location: ../broneeri/broneeri.php");
    exit;
}

$broneering = $_SESSION["broneering"];
$teenuste_id = $_POST["teenused"] ?? [];
$_SESSION["broneering"]["teenused"] = $teenuste_id;

$paevade_arv = (strtotime($broneering["lahkumine"]) - strtotime($broneering["saabumine"])) / 86400;

$toa_tyyp_id = $broneering["toa_tyyp_id"];
$toa_info = mysqli_fetch_assoc(mysqli_query($yhendus, "SELECT * FROM toa_tyyp WHERE id=$toa_tyyp_id"));
$hind = $toa_info["toa_hind"] * $paevade_arv;

$teenuste_hind = 0;
$teenuste_nimed = [];

if (!empty($teenuste_id)) {
    $id_str = implode(",", array_map('intval', $teenuste_id));
    $tulemus = mysqli_query($yhendus, "SELECT * FROM teenused WHERE id IN ($id_str)");
    while ($rida = mysqli_fetch_assoc($tulemus)) {
        $teenuste_hind += $rida["hind"];
        $teenuste_nimed[] = $rida["teenus"] . " (" . number_format($rida["hind"], 2, ',', ' ') . " €)";
    }
}

$km = 0.24 * ($hind + $teenuste_hind);
$kokku = $hind + $teenuste_hind + $km;
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="bi bi-list-check me-2"></i>Broneeringu ülevaade</h2>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4 class="text-primary"><i class="bi bi-house-door me-2"></i>Toa info</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Tüüp:</strong> <?= htmlspecialchars($toa_info["toa_tyyp"]) ?></span>
                            <span class="badge bg-primary"><?= number_format($toa_info["toa_hind"], 2, ',', ' ') ?> €/öö</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Ööde arv:</strong></span>
                            <span class="badge bg-primary"><?= $paevade_arv ?></span>
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-6">
                    <h4 class="text-primary"><i class="bi bi-calendar-event me-2"></i>Kuupäevad</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Saabumine:</strong> <?= htmlspecialchars($broneering["saabumine"]) ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Lahkumine:</strong> <?= htmlspecialchars($broneering["lahkumine"]) ?>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="mb-4">
                <h4 class="text-primary"><i class="bi bi-plus-circle me-2"></i>Lisateenused</h4>
                <?php if (!empty($teenuste_nimed)): ?>
                    <div class="list-group">
                        <?php foreach ($teenuste_nimed as $teenus): ?>
                            <div class="list-group-item"><?= htmlspecialchars($teenus) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Lisa teenuseid ei valitud
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h4 class="text-primary"><i class="bi bi-cash-stack me-2"></i>Hinna kalkulatsioon</h4>
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
            
            <form method="post" action="../maksma/maksmine.php">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="../broneeri/broneeri_teenused.php" class="btn btn-outline-secondary me-md-2">
                        <i class="bi bi-arrow-left me-2"></i>Tagasi
                    </a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-credit-card me-2"></i>Maksma
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>