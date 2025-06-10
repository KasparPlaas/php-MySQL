<?php
include("../includes/header.php");

// Kontrollime, kas eelnev info on olemas
if (!isset($_SESSION["broneering"])) {
    header("Location: ../broneeri/broneeri.php");
    exit;
}

// Kui kasutaja tuli POST-ga, salvestame toa_tyybi_id (mõlemad vormid annavad erineva nime)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["valitud_toa_tyyp"])) {
        $_SESSION["broneering"]["toa_tyyp_id"] = intval($_POST["valitud_toa_tyyp"]);
    } elseif (isset($_POST["toa_tyybi_id"])) {
        $_SESSION["broneering"]["toa_tyyp_id"] = intval($_POST["toa_tyybi_id"]);
    }
}

// Kontrollime veel kord, et toa_tyyp_id on olemas
if (!isset($_SESSION["broneering"]["toa_tyyp_id"])) {
    echo '<div class="container mt-5">
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Toa tüüpi ei leitud. Palun vali tuba enne jätkamist.
            </div>
            <a href="../broneeri/broneeri.php" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Tagasi broneerimisele
            </a>
          </div>';
    include("../includes/footer.php");
    exit;
}

// Teenuste küsimine
$teenused = mysqli_query($yhendus, "SELECT * FROM teenused ORDER BY teenus");
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Vali lisateenused (soovi korral)</h2>
        </div>
        
        <div class="card-body">
            <form method="post" action="../broneeri/broneeri_ylevaade.php">
                <?php if (mysqli_num_rows($teenused) > 0): ?>
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        <?php while ($rida = mysqli_fetch_assoc($teenused)): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="teenused[]" 
                                                   value="<?= intval($rida['id']) ?>" 
                                                   id="teenus_<?= $rida['id'] ?>">
                                            <label class="form-check-label w-100" for="teenus_<?= $rida['id'] ?>">
                                                <h5 class="card-title text-primary">
                                                    <?= htmlspecialchars($rida['teenus']) ?>
                                                    <span class="badge bg-success float-end">
                                                        <?= number_format($rida['hind'], 2, ',', ' ') ?> €
                                                    </span>
                                                </h5>
                                                <?php if (!empty($rida['kirjeldus'])): ?>
                                                    <p class="card-text text-muted">
                                                        <?= htmlspecialchars($rida['kirjeldus']) ?>
                                                    </p>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Praegu ei ole lisateenuseid saadaval.
                    </div>
                <?php endif; ?>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="../broneeri/broneeri.php" class="btn btn-outline-secondary me-md-2">
                        <i class="bi bi-arrow-left me-2"></i>Tagasi
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-2"></i>Jätka ülevaatele
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>