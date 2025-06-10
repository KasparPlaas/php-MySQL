<?php
session_start();
include("../includes/header.php");

// Funktsioon kasutaja andmete saamiseks
function kasutaja_andmed($yhendus, $kasutaja_id) {
    $puhver = mysqli_real_escape_string($yhendus, $kasutaja_id);
    $paring = "SELECT kliendid.eesnimi, kliendid.perenimi, kliendid.telefon, kliendid.isikukood, kasutajad.email 
               FROM kliendid 
               JOIN kasutajad ON kliendid.kasutaja_id = kasutajad.id 
               WHERE kliendid.kasutaja_id = '$puhver'";
    $tulemus = mysqli_query($yhendus, $paring);
    return mysqli_fetch_assoc($tulemus);
}

// Kontrollime, kas kasutaja on sisselogitud kliendina
sisse_logitud()
?>

<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Broneeri tuba</h2>
        </div>
        
        <div class="card-body">
            <form method="post" class="row g-3 needs-validation" novalidate>
                <?php
                if (sisse_logitud()) {
                    // Kui kasutaja on sisselogitud, küsime tema andmed
                    $kasutaja_andmed = kasutaja_andmed($yhendus, $_SESSION['kasutaja_id']);
                    
                    // Peidame väljad, kuna andmed on juba teada
                    echo '<input type="hidden" name="eesnimi" value="'.htmlspecialchars($kasutaja_andmed['eesnimi']).'">';
                    echo '<input type="hidden" name="perenimi" value="'.htmlspecialchars($kasutaja_andmed['perenimi']).'">';
                    echo '<input type="hidden" name="telefon" value="'.htmlspecialchars($kasutaja_andmed['telefon']).'">';
                    echo '<input type="hidden" name="isikukood" value="'.htmlspecialchars($kasutaja_andmed['isikukood']).'">';
                    echo '<input type="hidden" name="email" value="'.htmlspecialchars($kasutaja_andmed['email']).'">';
                    
                    // Näitame kasutajale, et tema andmed on automaatselt lisatud
                    echo '<div class="col-12">';
                    echo '<div class="alert alert-info">';
                    echo '<i class="bi bi-person-check me-2"></i>';
                    echo 'Sinu andmed on automaatselt lisatud: ';
                    echo htmlspecialchars($kasutaja_andmed['eesnimi']).' '.htmlspecialchars($kasutaja_andmed['perenimi']);
                    echo '</div></div>';
                } else {
                    // Kui kasutaja pole sisselogitud, näitame väljad täitmiseks
                    ?>
                    <div class="col-md-3">
                        <label for="eesnimi" class="form-label">Eesnimi</label>
                        <input type="text" id="eesnimi" name="eesnimi" class="form-control" required>
                        <div class="invalid-feedback">Palun sisesta oma eesnimi</div>
                    </div>
                    <div class="col-md-3">
                        <label for="perenimi" class="form-label">Perenimi</label>
                        <input type="text" id="perenimi" name="perenimi" class="form-control" required>
                        <div class="invalid-feedback">Palun sisesta oma perenimi</div>
                    </div>
                    <div class="col-md-3">
                        <label for="telefon" class="form-label">Telefon</label>
                        <input type="text" id="telefon" name="telefon" class="form-control" required>
                        <div class="invalid-feedback">Palun sisesta oma telefoninumber</div>
                    </div>
                    <div class="col-md-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                        <div class="invalid-feedback">Palun sisesta korrektne email</div>
                    </div>
                    <div class="col-md-4">
                        <label for="isikukood" class="form-label">Isikukood</label>
                        <input type="text" id="isikukood" name="isikukood" class="form-control">
                    </div>
                    <?php
                }
                ?>

                <!-- Broneerimise kuupäevad -->
                <div class="col-md-4">
                    <label for="saabumine" class="form-label">Saabumise kuupäev</label>
                    <input type="date" id="saabumine" name="saabumine" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label for="lahkumine" class="form-label">Lahkumise kuupäev</label>
                    <input type="date" id="lahkumine" name="lahkumine" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label for="taiskasvanud" class="form-label">Täiskasvanuid</label>
                    <input type="number" id="taiskasvanud" name="taiskasvanud" class="form-control" min="1" value="1" required>
                </div>
                <div class="col-md-2">
                    <label for="lapsed" class="form-label">Lapsi</label>
                    <input type="number" id="lapsed" name="lapsed" class="form-control" min="0" value="0">
                </div>
                
                <div class="col-12">
                    <button class="btn btn-primary px-4 py-2" type="submit">
                        <i class="bi bi-search me-2"></i>Otsi vabu tube
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php
    // Kui vorm on saadetud
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Puhverdame andmed
        $eesnimi = mysqli_real_escape_string($yhendus, $_POST['eesnimi']);
        $perenimi = mysqli_real_escape_string($yhendus, $_POST['perenimi']);
        $telefon = mysqli_real_escape_string($yhendus, $_POST['telefon']);
        $email = mysqli_real_escape_string($yhendus, $_POST['email']);
        $isikukood = mysqli_real_escape_string($yhendus, $_POST['isikukood'] ?? '');
        $saabumine = mysqli_real_escape_string($yhendus, $_POST['saabumine']);
        $lahkumine = mysqli_real_escape_string($yhendus, $_POST['lahkumine']);
        $taiskasvanud = intval($_POST['taiskasvanud']);
        $lapsed = intval($_POST['lapsed']);
        $kylalisi_kokku = $taiskasvanud + $lapsed;
        
        // Kontrollime andmete korrektsust
        $vead = [];
        
        if (empty($eesnimi)) $vead[] = "Eesnimi on kohustuslik";
        if (empty($perenimi)) $vead[] = "Perenimi on kohustuslik";
        if (empty($telefon)) $vead[] = "Telefon on kohustuslik";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $vead[] = "Palun sisesta korrektne email";
        if (strtotime($lahkumine) <= strtotime($saabumine)) $vead[] = "Lahkumine peab olema hiljem kui saabumine";
        if ($taiskasvanud < 1) $vead[] = "Vähemalt üks täiskasvanu peab olema";
        
        // Kui vigu pole
        if (empty($vead)) {
            // Kui kasutaja pole sisselogitud, salvestame külalise andmed
            if (!$kasutaja_sisse_logitud) {
                $paring = "INSERT INTO kylalised (eesnimi, perenimi, isikukood, telefon, email) 
                           VALUES ('$eesnimi', '$perenimi', '$isikukood', '$telefon', '$email')";
                
                if (mysqli_query($yhendus, $paring)) {
                    $_SESSION['kylaline_id'] = mysqli_insert_id($yhendus);
                } else {
                    echo '<div class="alert alert-danger mt-4">Viga külalise salvestamisel: '.mysqli_error($yhendus).'</div>';
                }
            }
            
            // Otsime vabu tube
            $paring = "SELECT id, toa_tyyp, toa_hind, toa_kirjeldus, toa_maht, toa_pilt 
                      FROM toa_tyyp 
                      WHERE toa_maht >= $kylalisi_kokku
                      AND id NOT IN (
                          SELECT toa_id FROM broneeringud 
                          WHERE NOT (lahkumine <= '$saabumine' OR saabumine >= '$lahkumine')
                      )";
            
            $tulemus = mysqli_query($yhendus, $paring);
            
            if (mysqli_num_rows($tulemus) > 0) {
                echo '<div class="card mt-4 shadow-sm">';
                echo '<div class="card-header bg-success text-white">';
                echo '<h4 class="mb-0"><i class="bi bi-door-open me-2"></i>Saadaval toad</h4>';
                echo '</div>';
                echo '<div class="card-body">';
                echo '<form method="post" action="broneeri_teenused.php">';
                echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';
                
                while ($tuba = mysqli_fetch_assoc($tulemus)) {
                    echo '<div class="col">';
                    echo '<div class="card h-100 shadow-sm">';
                    
                    if (!empty($tuba['toa_pilt'])) {
                        echo '<img src="'.htmlspecialchars($tuba['toa_pilt']).'" class="card-img-top" alt="Toa pilt" style="height: 200px; object-fit: cover;">';
                    } else {
                        echo '<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">';
                        echo '<i class="bi bi-image text-muted" style="font-size: 3rem;"></i>';
                        echo '</div>';
                    }
                    
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title text-primary">'.htmlspecialchars($tuba['toa_tyyp']).'</h5>';
                    echo '<p class="card-text text-muted">'.htmlspecialchars($tuba['toa_kirjeldus']).'</p>';
                    
                    echo '<ul class="list-group list-group-flush mb-3">';
                    echo '<li class="list-group-item"><strong>Hind:</strong> '.$tuba['toa_hind'].' € öö kohta</li>';
                    echo '<li class="list-group-item"><strong>Mahutab:</strong> '.$tuba['toa_maht'].' inimest</li>';
                    echo '</ul>';
                    
                    echo '<button type="submit" name="valitud_toa_id" value="'.$tuba['id'].'" class="btn btn-success w-100">';
                    echo '<i class="bi bi-check-circle me-2"></i>Vali see tuba';
                    echo '</button>';
                    
                    echo '</div></div></div>';
                }
                
                echo '</div></form></div></div>';
            } else {
                echo '<div class="alert alert-warning mt-4">';
                echo '<i class="bi bi-exclamation-triangle me-2"></i>Valitud perioodil pole sobivaid tube saadaval.';
                echo '</div>';
            }
        } else {
            // Kuvame veateated
            echo '<div class="alert alert-danger mt-4"><ul class="mb-0">';
            foreach ($vead as $viga) {
                echo "<li>$viga</li>";
            }
            echo '</ul></div>';
        }
    }
    ?>
</div>

<script>
// Vormi valideerimine
(function () {
  'use strict'
  
  const vormid = document.querySelectorAll('.needs-validation')
  
  Array.from(vormid).forEach(vorm => {
    vorm.addEventListener('submit', event => {
      if (!vorm.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      
      vorm.classList.add('was-validated')
    }, false)
  })
})()
</script>

<?php include("../includes/footer.php"); ?>