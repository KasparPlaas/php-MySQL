<?php
include("../includes/header.php");

// Suuna kliendi vaatesse, kui sisse logitud
if (isset($_SESSION["kasutaja_id"])) {
    header("Location: ../broneeri/broneeri.php");
    exit();
}

// Vormiväljade eeltäitmine
$eesnimi = $_POST['eesnimi'] ?? '';
$perenimi = $_POST['perenimi'] ?? '';
$isikukood = $_POST['isikukood'] ?? '';
$telefon = $_POST['telefon'] ?? '';
$email = $_POST['email'] ?? '';
$saabumine = $_POST['saabumine'] ?? date('Y-m-d');
$lahkumine = $_POST['lahkumine'] ?? date('Y-m-d', strtotime('+1 day'));
$taiskasvanud = $_POST['taiskasvanud'] ?? 1;
$lapsed = $_POST['lapsed'] ?? 0;
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Külalise broneering</h2>
        </div>
        
        <div class="card-body">
            <form method="post" class="row g-3 needs-validation" novalidate>
                <div class="col-md-6">
                    <label for="eesnimi" class="form-label">Eesnimi</label>
                    <input type="text" id="eesnimi" name="eesnimi" class="form-control" 
                           value="<?= htmlspecialchars($eesnimi) ?>" required>
                    <div class="invalid-feedback">Palun sisesta eesnimi</div>
                </div>
                
                <div class="col-md-6">
                    <label for="perenimi" class="form-label">Perenimi</label>
                    <input type="text" id="perenimi" name="perenimi" class="form-control" 
                           value="<?= htmlspecialchars($perenimi) ?>" required>
                    <div class="invalid-feedback">Palun sisesta perenimi</div>
                </div>
                
                <div class="col-md-6">
                    <label for="isikukood" class="form-label">Isikukood</label>
                    <input type="text" id="isikukood" name="isikukood" class="form-control" 
                           value="<?= htmlspecialchars($isikukood) ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="telefon" class="form-label">Telefon</label>
                    <input type="tel" id="telefon" name="telefon" class="form-control" 
                           value="<?= htmlspecialchars($telefon) ?>" required>
                    <div class="invalid-feedback">Palun sisesta telefoninumber</div>
                </div>
                
                <div class="col-12">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($email) ?>" required>
                    <div class="invalid-feedback">Palun sisesta korrektne email</div>
                </div>
                
                <div class="col-md-4">
                    <label for="saabumine" class="form-label">Saabumine</label>
                    <input type="date" id="saabumine" name="saabumine" class="form-control" 
                           value="<?= htmlspecialchars($saabumine) ?>" required>
                </div>
                
                <div class="col-md-4">
                    <label for="lahkumine" class="form-label">Lahkumine</label>
                    <input type="date" id="lahkumine" name="lahkumine" class="form-control" 
                           value="<?= htmlspecialchars($lahkumine) ?>" required>
                </div>
                
                <div class="col-md-2">
                    <label for="taiskasvanud" class="form-label">Täiskasvanuid</label>
                    <input type="number" id="taiskasvanud" name="taiskasvanud" class="form-control" 
                           min="1" max="10" value="<?= htmlspecialchars($taiskasvanud) ?>" required>
                </div>
                
                <div class="col-md-2">
                    <label for="lapsed" class="form-label">Lapsi (0-17a)</label>
                    <input type="number" id="lapsed" name="lapsed" class="form-control" 
                           min="0" max="10" value="<?= htmlspecialchars($lapsed) ?>">
                </div>
                
                <div class="col-12 mt-4">
                    <button class="btn btn-primary px-4 py-2" type="submit">
                        <i class="bi bi-search me-2"></i>Otsi vabu tube
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $eesnimi = mysqli_real_escape_string($yhendus, $_POST["eesnimi"]);
        $perenimi = mysqli_real_escape_string($yhendus, $_POST["perenimi"]);
        $isikukood = mysqli_real_escape_string($yhendus, $_POST["isikukood"]);
        $telefon = mysqli_real_escape_string($yhendus, $_POST["telefon"]);
        $email = mysqli_real_escape_string($yhendus, $_POST["email"]);
        $saabumine = $_POST["saabumine"];
        $lahkumine = $_POST["lahkumine"];
        $taiskasvanud = intval($_POST["taiskasvanud"]);
        $lapsed = intval($_POST["lapsed"]);
        $kylaliste_arv = $taiskasvanud + $lapsed;

        // Valideerimine
        $vead = [];
        
        if (empty($eesnimi)) $vead[] = "Eesnimi on kohustuslik";
        if (empty($perenimi)) $vead[] = "Perenimi on kohustuslik";
        if (empty($telefon)) $vead[] = "Telefon on kohustuslik";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $vead[] = "Palun sisesta korrektne email";
        if ($lahkumine <= $saabumine) $vead[] = "Lahkumine peab olema hiljem kui saabumine";
        if ($taiskasvanud < 1) $vead[] = "Vähemalt üks täiskasvanu peab olema";

        if (count($vead) > 0) {
            echo '<div class="alert alert-danger mt-4"><ul class="mb-0">';
            foreach ($vead as $viga) {
                echo "<li>$viga</li>";
            }
            echo '</ul></div>';
        } else {
            // Salvesta külaline andmebaasi
            $insert_kylaline = "
                INSERT INTO kylalised (eesnimi, perenimi, isikukood, telefon, email)
                VALUES ('$eesnimi', '$perenimi', '$isikukood', '$telefon', '$email')
            ";
            
            if (!mysqli_query($yhendus, $insert_kylaline)) {
                echo '<div class="alert alert-danger mt-4">Viga külalise salvestamisel: ' . mysqli_error($yhendus) . '</div>';
                exit();
            }

            $kylaline_id = mysqli_insert_id($yhendus);

            // Salvesta broneeringu andmed sessiooni
            $_SESSION["broneering"] = [
                "kylaline_id" => $kylaline_id,
                "saabumine" => $saabumine,
                "lahkumine" => $lahkumine,
                "taiskasvanud" => $taiskasvanud,
                "lapsed" => $lapsed,
                "kylaliste_arv" => $kylaliste_arv,
                "kylalise_andmed" => [
                    "eesnimi" => $eesnimi,
                    "perenimi" => $perenimi,
                    "isikukood" => $isikukood,
                    "telefon" => $telefon,
                    "email" => $email
                ]
            ];


            // Otsi vabu tube
            $sql = "
                SELECT toa_tyyp.id, toa_tyyp.toa_tyyp, toa_tyyp.toa_hind, 
                       toa_tyyp.toa_kirjeldus, toa_tyyp.toa_maht, toa_tyyp.toa_pilt
                FROM toa_tyyp
                WHERE toa_tyyp.toa_maht >= $kylaliste_arv
                  AND toa_tyyp.id IN (
                    SELECT toad.toa_id FROM toad
                    WHERE toad.id NOT IN (
                        SELECT broneeringud.toa_id FROM broneeringud
                        WHERE NOT (broneeringud.lahkumine <= '$saabumine' OR broneeringud.saabumine >= '$lahkumine')
                    )
                  )
            ";

            $tulemus = mysqli_query($yhendus, $sql);

            if (mysqli_num_rows($tulemus) > 0) {
                echo '<div class="card mt-4 shadow-sm">';
                echo '<div class="card-header bg-success text-white">';
                echo '<h4 class="mb-0"><i class="bi bi-door-open me-2"></i>Saadaolevad toad</h4>';
                echo '</div>';
                echo '<div class="card-body">';
                echo '<form method="post" action="../broneeri/broneeri_teenused.php">';
                echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';

                while ($rida = mysqli_fetch_assoc($tulemus)) {
                    echo '<div class="col">';
                    echo '<div class="card h-100 shadow-sm">';
                    
                    if (!empty($rida['toa_pilt'])) {
                        echo '<img src="' . htmlspecialchars($rida['toa_pilt']) . '" class="card-img-top" alt="Toa pilt" style="height: 200px; object-fit: cover;">';
                    } else {
                        echo '<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">';
                        echo '<i class="bi bi-image text-muted" style="font-size: 3rem;"></i>';
                        echo '</div>';
                    }
                    
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title text-primary">' . htmlspecialchars($rida['toa_tyyp']) . '</h5>';
                    echo '<p class="card-text text-muted">' . htmlspecialchars($rida['toa_kirjeldus']) . '</p>';
                    
                    echo '<ul class="list-group list-group-flush mb-3">';
                    echo '<li class="list-group-item"><strong>Hind:</strong> ' . htmlspecialchars($rida['toa_hind']) . ' €/öö</li>';
                    echo '<li class="list-group-item"><strong>Mahutab:</strong> ' . htmlspecialchars($rida['toa_maht']) . ' inimest</li>';
                    echo '</ul>';
                    
                    echo '<button type="submit" name="toa_tyybi_id" value="' . htmlspecialchars($rida['id']) . '" 
                          class="btn btn-success w-100">
                          <i class="bi bi-check-circle me-2"></i>Vali see tuba
                          </button>';
                    echo '</div></div></div>';
                }

                echo '</div></form></div></div>';
            } else {
                echo '<div class="alert alert-warning mt-4">';
                echo '<i class="bi bi-exclamation-triangle me-2"></i>Kahjuks ei leidnud sobivaid tube valitud kuupäevadele.';
                echo '</div>';
            }
        }
    }
    ?>
</div>

<script>
// Bootstrapi vormi valideerimine
(function () {
  'use strict'
  
  const forms = document.querySelectorAll('.needs-validation')
  
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

<?php include("../includes/footer.php"); ?>