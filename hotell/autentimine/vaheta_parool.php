<?php
include '../includes/header.php';
require '../autentimine/vendor/autoload.php';

$veateade = "";
$onnestus = "";

if (!isset($_GET['kood'])) {
    $veateade = "Link puudub või on vigane.";
} else {
    $kood = mysqli_real_escape_string($yhendus, $_GET['kood']);

    $kysitav = mysqli_query($yhendus, "
        SELECT id, reset_kood_aeg 
        FROM kasutajad 
        WHERE reset_kood = '$kood'
    ");

    if ($rida = mysqli_fetch_assoc($kysitav)) {
        $aeg = strtotime($rida['reset_kood_aeg']);
        if ((time() - $aeg) > 3600) { // aegunud üle 1h
            $veateade = "Taastelink on aegunud.";
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $uus_parool = $_POST['parool'] ?? '';
            $kinnita = $_POST['kinnita'] ?? '';

            if (strlen($uus_parool) < 6) {
                $veateade = "Parool peab olema vähemalt 6 tähemärki.";
            } elseif ($uus_parool !== $kinnita) {
                $veateade = "Paroolid ei ühti.";
            } else {
                $hash = password_hash($uus_parool, PASSWORD_DEFAULT);
                $id = $rida['id'];

                mysqli_query($yhendus, "
                    UPDATE kasutajad 
                    SET parool = '$hash', reset_kood = NULL, reset_kood_aeg = NULL 
                    WHERE id = $id
                ");

                $onnestus = "Parool edukalt uuendatud. <a href='../autentimine/login.php'>Logi sisse</a>";
            }
        }
    } else {
        $veateade = "Kehtetu taastelink.";
    }
}
?>

<div class="container mt-5" style="max-width: 500px;">
    <?php if ($veateade): ?>
        <div class="alert alert-danger"><?= $veateade ?></div>
    <?php elseif ($onnestus): ?>
        <div class="alert alert-success"><?= $onnestus ?></div>
    <?php elseif (isset($kood)): ?>
        <h2>Uus parool</h2>
        <form method="post">
            <div class="form-group">
                <label for="parool">Uus parool:</label>
                <input type="password" name="parool" id="parool" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="kinnita">Kinnita parool:</label>
                <input type="password" name="kinnita" id="kinnita" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Muuda parool</button>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
