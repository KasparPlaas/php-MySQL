<?php
include '../includes/session.php';
$kasutaja = aktiivne_kasutaja();
?>
<!doctype html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title><?= isset($pealkiri) ? htmlspecialchars($pealkiri) : 'Hotell' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .hero-section {
            background: transparent;
            min-height: 80vh;
            display: flex;
            align-items: center;
            padding: 4rem 0;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #6c757d;
        }

        .btn-hero {
            background: #007bff;
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-hero:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .hero-image img {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            height: 350px;
            object-fit: cover;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .section-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 3rem;
        }

        .room-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 100%;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .room-card .card-img-top {
            height: 220px;
            object-fit: cover;
        }

        .price-tag {
            font-size: 1.4rem;
            font-weight: 700;
            color: #28a745;
        }

        .btn-book {
            background: #007bff;
            border: none;
            border-radius: 8px;
            padding: 10px 0;
            font-weight: 600;
        }

        .btn-book:hover {
            background: #0056b3;
        }

        .services-section {
            background: #f8f9fa;
        }

        .service-card {
            border: none;
            border-radius: 12px;
            padding: 2rem;
            height: 100%;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .service-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.8rem;
            color: white;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.8rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .contact-item i {
            font-size: 1.3rem;
            color: #007bff;
            margin-right: 1rem;
            width: 25px;
        }

        .btn-send {
            background: #007bff;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-send:hover {
            background: #0056b3;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }
            .section-title {
                font-size: 2rem;
            }
        }

        .hover-bg-primary:hover {
            background-color: #0d6efd;
            transition: all 0.3s ease;
        }

        .hover-text-white:hover {
            color: white !important;
        }
        
        .dropdown-item:hover {
            background-color: #0d6efd;
            color: white !important;
        }

        .text-gradient {
            background: linear-gradient(90deg, #0d6efd, #20c997);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .avatar {
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #0d6efd !important;
        }
        
        .dropdown-item.text-danger:hover {
            color: #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.1);
        }
        
        .navbar {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
        }
</style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <!-- Brand logo with hotel icon -->
        <a class="navbar-brand fw-bold d-flex align-items-center" href="../avaleht">
            <i class="fas fa-hotel text-primary me-2"></i>
            <span class="text-gradient">Hotell</span>
        </a>
        
        <!-- Toggler button -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#peamenyy">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Main menu -->
        <div class="collapse navbar-collapse" id="peamenyy">
            <ul class="navbar-nav me-auto"></ul>
            
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Book button -->
                <li class="nav-item me-2">
                    <a class="btn btn-primary rounded-pill px-3 shadow-sm" href="<?= $kasutaja ? '../broneeri/broneeri_klient.php' : '../broneeri/broneeri_kylalised.php' ?>">
                        <i class="fas fa-calendar-check me-1"></i> Broneerima
                    </a>
                </li>

                <!-- Guest/User section -->
                <?php if (!$kasutaja): ?>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-primary rounded-pill px-3" href="../autentimine/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Logi sisse
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary rounded-pill px-3 shadow-sm" href="../autentimine/register.php">
                            <i class="fas fa-user-plus me-1"></i> Registreeru
                        </a>
                    </li>
                <?php else: ?>
                    <!-- User dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="kontoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <?= strtoupper(substr($kasutaja['eesnimi'] ?? 'K', 0, 1)) ?>
                            </div>
                            <?= htmlspecialchars($kasutaja['eesnimi'] ?? 'Konto') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3" aria-labelledby="kontoDropdown">
                            <?php if (klient()): ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#broneeringud">
                                        <i class="fas fa-calendar-alt me-2 text-muted"></i> Minu broneeringud
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#konto">
                                    <i class="fas fa-cog me-2 text-muted"></i> Konto sätted
                                </a>
                            </li>
                            
                            <?php if (admin() || tootaja()): ?>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><h6 class="dropdown-header small text-uppercase text-muted">Haldus</h6></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#broneeringud">
                                        <i class="fas fa-tasks me-2 text-muted"></i> Broneeringute haldus
                                    </a>
                                </li>
                                <?php if (admin()): ?>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#kasutajad">
                                            <i class="fas fa-users-cog me-2 text-muted"></i> Kasutaja haldus
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#toad">
                                            <i class="fas fa-door-open me-2 text-muted"></i> Tubade haldus
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider my-1"></li>
                            <?php endif; ?>
                            
                            <li>
                                <a class="dropdown-item d-flex align-items-center text-danger" href="../autentimine/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logi välja
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Sidebar toggle -->
                <li class="nav-item ms-2">
                    <button class="btn btn-light rounded-pill border px-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                        <i class="fas fa-bars me-1"></i> Menüü
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- SIDEBAR MENÜÜ -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="sidebarMenu">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title text-primary fw-bold">Menüü</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Sulge"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="list-unstyled mb-0">
            <li class="border-bottom">
                <a href="../avaleht" class="nav-link px-4 py-3 d-block text-dark hover-bg-primary hover-text-white">
                    <i class="fas fa-home me-2"></i>Avaleht
                </a>
            </li>
            <li class="border-bottom">
                <a href="<?= klient() ? '../broneeri/broneeri_klient.php' : '../broneeri/broneeri_kylalised.php' ?>" class="nav-link px-4 py-3 d-block text-dark hover-bg-primary hover-text-white">
                    <i class="fas fa-calendar-check me-2"></i>Broneeri
                </a>
            </li>
            <li class="border-bottom">
                <a href="../kontakt" class="nav-link px-4 py-3 d-block text-dark hover-bg-primary hover-text-white">
                    <i class="fas fa-envelope me-2"></i>Kontakt
                </a>
            </li>
            <li class="border-bottom">
                <a href="../meist" class="nav-link px-4 py-3 d-block text-dark hover-bg-primary hover-text-white">
                    <i class="fas fa-info-circle me-2"></i>Meist
                </a>
            </li>
            <li class="border-bottom">
                <a href="../asukoht" class="nav-link px-4 py-3 d-block text-dark hover-bg-primary hover-text-white">
                    <i class="fas fa-map-marker-alt me-2"></i>Asukoht
                </a>
            </li>
            <li class="dropdown border-bottom">
                <a class="nav-link px-4 py-3 d-block text-dark dropdown-toggle hover-bg-primary hover-text-white" href="#" id="veelDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-h me-2"></i>Veel
                </a>
                <ul class="dropdown-menu w-100 rounded-0 border-0 shadow-sm" aria-labelledby="veelDropdown">
                    <li>
                        <a class="dropdown-item py-2 hover-bg-primary hover-text-white" href="../galerii">
                            <i class="fas fa-images me-2"></i>Galerii
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 hover-bg-primary hover-text-white" href="../tingimused">
                            <i class="fas fa-file-alt me-2"></i>Tingimused
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>

<?php if ($kasutaja): ?>
<!-- Konto sätted modal -->
<div class="modal fade" id="konto" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="../kasutaja/konto.php" method="post" onsubmit="return validateAccountForm()">
            <div class="modal-header">
                <h5 class="modal-title">Konto sätted</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Basic Account Info -->
                <div class="mb-3">
                    <label for="kasutajanimi" class="form-label">Kasutajanimi</label>
                    <input type="text" class="form-control" id="kasutajanimi" name="kasutajanimi" 
                           value="<?= htmlspecialchars($kasutaja['kasutajanimi'] ?? '') ?>" 
                           readonly>
                </div>
                
                <div class="mb-3">
                    <label for="eesnimi" class="form-label">Eesnimi*</label>
                    <input type="text" class="form-control" id="eesnimi" name="eesnimi" 
                           value="<?= htmlspecialchars($kasutaja['eesnimi'] ?? '') ?>" 
                           pattern="[A-Za-zÕÄÖÜõäöüšŽž'-]{2,50}" 
                           title="Eesnimi peab sisaldama vähemalt 2 tähemärki" required>
                </div>
                <div class="mb-3">
                    <label for="perenimi" class="form-label">Perekonnanimi*</label>
                    <input type="text" class="form-control" id="perenimi" name="perenimi" 
                           value="<?= htmlspecialchars($kasutaja['perenimi'] ?? '') ?>" 
                           pattern="[A-Za-zÕÄÖÜõäöüšŽž'-]{2,50}" 
                           title="Perekonnanimi peab sisaldama vähemalt 2 tähemärki" required>
                </div>
                <div class="mb-3">
                    <label for="telefon" class="form-label">Telefon*</label>
                    <input type="tel" class="form-control" id="telefon" name="telefon" 
                           value="<?= htmlspecialchars($kasutaja['telefon'] ?? '') ?>" 
                           pattern="[\+]{0,1}[0-9]{7,12}" 
                           title="Telefoninumber peab koosnema 7-12 numbrist (võib alata +-märgiga)" required>
                </div>
                <div class="mb-3">
                    <label for="isikukood" class="form-label">Isikukood*</label>
                    <input type="text" class="form-control" id="isikukood" name="isikukood" 
                           value="<?= htmlspecialchars($kasutaja['isikukood'] ?? '') ?>" 
                           pattern="[0-9]{11}" 
                           title="Isikukood peab koosnema täpselt 11 numbrist" 
                           minlength="11" maxlength="11" required>
                </div>
                
                <!-- Contact Info -->
                <div class="mb-3">
                    <label for="email" class="form-label">E-post*</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($kasutaja['email'] ?? '') ?>" 
                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                           title="Palun sisesta korrektne e-posti aadress" required>
                </div>
                
                <!-- Password Change -->
                <div class="card mb-3">
                    <div class="card-header">Parooli muutmine</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="vana_parool" class="form-label">Praegune parool</label>
                            <input type="password" class="form-control" id="vana_parool" name="vana_parool">
                        </div>
                        <div class="mb-3">
                            <label for="uus_parool" class="form-label">Uus parool</label>
                            <input type="password" class="form-control" id="uus_parool" name="uus_parool" 
                                   minlength="8" 
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                   title="Parool peab sisaldama vähemalt 8 tähemärki, sh vähemalt üks suur täht, üks väike täht ja üks number">
                        </div>
                        <div class="mb-3">
                            <label for="parool_kinnitus" class="form-label">Kinnita uus parool</label>
                            <input type="password" class="form-control" id="parool_kinnitus" name="parool_kinnitus" 
                                   minlength="8">
                        </div>
                    </div>
                </div>
                
                <!-- Email verification status -->
                <?php if (!$kasutaja['email_kinnitatud']): ?>
                <div class="alert alert-warning">
                    Sinu e-posti aadress pole veel kinnitatud. 
                    <a href="../kasutaja/kinnita_email.php" class="alert-link">Kinnita nüüd</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                <button type="submit" class="btn btn-primary">Salvesta muudatused</button>
            </div>
        </form>
    </div>
</div>

<script>
function validateAccountForm() {
    // Password validation
    const oldPassword = document.getElementById('vana_parool').value;
    const newPassword = document.getElementById('uus_parool').value;
    const confirmPassword = document.getElementById('parool_kinnitus').value;
    
    // If any password field is filled, all must be filled
    if (oldPassword || newPassword || confirmPassword) {
        if (!oldPassword) {
            alert('Palun sisesta praegune parool!');
            return false;
        }
        if (!newPassword) {
            alert('Palun sisesta uus parool!');
            return false;
        }
        if (!confirmPassword) {
            alert('Palun kinnita uus parool!');
            return false;
        }
        if (newPassword !== confirmPassword) {
            alert('Uus parool ja kinnitusparool ei kattu!');
            return false;
        }
        if (!/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(newPassword)) {
            alert('Parool peab sisaldama vähemalt 8 tähemärki, sh vähemalt üks suur täht, üks väike täht ja üks number!');
            return false;
        }
    }
    
    // Validate Estonian personal ID code for clients
    <?php if ($kasutaja['roll'] === 'klient'): ?>
    const isikukood = document.getElementById('isikukood').value;
    if (!validateIsikukood(isikukood)) {
        alert('Palun sisesta korrektne isikukood!');
        return false;
    }
    <?php endif; ?>
    
    // Validate phone number for clients
    <?php if ($kasutaja['roll'] === 'klient'): ?>
    const telefon = document.getElementById('telefon').value;
    if (!/^[\+]?[0-9]{7,12}$/.test(telefon)) {
        alert('Palun sisesta korrektne telefoninumber!');
        return false;
    }
    <?php endif; ?>
    
    return true;
}

function validateIsikukood(ik) {
    // Simple Estonian ID code validation
    if (!/^[0-9]{11}$/.test(ik)) return false;
    
    // Extract birth century from first digit
    const century = parseInt(ik[0]);
    if (century < 1 || century > 6) return false;
    
    // Extract birth date parts
    const year = parseInt(ik.substr(1, 2));
    const month = parseInt(ik.substr(3, 2));
    const day = parseInt(ik.substr(5, 2));
    
    // Validate date (simplified check)
    if (month < 1 || month > 12 || day < 1 || day > 31) return false;
    
    return true;
}

// Auto-format phone number
document.getElementById('telefon')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9+]/g, '');
});

// Auto-format personal ID code
document.getElementById('isikukood')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 11) {
        this.value = this.value.substring(0, 11);
    }
});
</script>
<?php endif; ?>

<?php if (admin() || tootaja() || klient()): ?>
<div class="modal fade" id="broneeringud" tabindex="-1" aria-labelledby="broneeringudLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="broneeringudModalLabel">
                    <?= (admin() || tootaja()) ? 'Kõik broneeringud' : 'Minu broneeringud' ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Sulge"></button>
            </div>
            <div class="modal-body">
                <?php
                // Päringud erinevatele kasutajarollidele
                if (admin() || tootaja()) {
                    // Admin/töötaja päring kõikide broneeringute jaoks
                    $päring = "
                        SELECT 
                            broneeringud.id, 
                            broneeringud.saabumine, 
                            broneeringud.lahkumine, 
                            broneeringud.staatus,
                            toad.toa_nr, 
                            toa_tyyp.toa_tyyp,
                            COALESCE(kliendid.eesnimi, kylalised.eesnimi) AS eesnimi,
                            COALESCE(kliendid.perenimi, kylalised.perenimi) AS perenimi,
                            CASE 
                                WHEN kliendid.id IS NOT NULL THEN 'Klient'
                                ELSE 'Külaline'
                            END AS kasutaja_tyyp
                        FROM broneeringud
                        JOIN toad ON broneeringud.toa_id = toad.id
                        JOIN toa_tyyp ON toad.toa_id = toa_tyyp.id
                        LEFT JOIN kliendid ON broneeringud.klient_id = kliendid.id
                        LEFT JOIN kylalised ON broneeringud.kylaline_id = kylalised.id
                        ORDER BY broneeringud.saabumine DESC
                    ";
                } elseif (klient() && isset($_SESSION['kasutaja_id'])) {
                    // Kliendi päring ainult tema enda broneeringute jaoks
                    $kasutaja_id = intval($_SESSION['kasutaja_id']);
                    $päring = "
                        SELECT 
                            broneeringud.id, 
                            broneeringud.saabumine, 
                            broneeringud.lahkumine, 
                            broneeringud.staatus,
                            toad.toa_nr, 
                            toa_tyyp.toa_tyyp
                        FROM broneeringud
                        JOIN toad ON broneeringud.toa_id = toad.id
                        JOIN toa_tyyp ON toad.toa_id = toa_tyyp.id
                        JOIN kliendid ON broneeringud.klient_id = kliendid.id
                        WHERE kliendid.kasutaja_id = $kasutaja_id
                        ORDER BY broneeringud.saabumine DESC
                    ";
                }
                
                $broneeringud = mysqli_query($yhendus, $päring);
                
                if ($broneeringud && mysqli_num_rows($broneeringud) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <?php if (admin() || tootaja()): ?><th>ID</th><?php endif; ?>
                                    <?php if (admin() || tootaja()): ?><th>Klient</th><?php endif; ?>
                                    <th>Toa tüüp</th>
                                    <th>Toa nr</th>
                                    <th>Saabumine</th>
                                    <th>Lahkumine</th>
                                    <th>Staatus</th>
                                    <th>Tegevused</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($broneering = mysqli_fetch_assoc($broneeringud)): ?>
                                    <?php
                                        $saabumine = new DateTime($broneering['saabumine']);
                                        $tana = new DateTime();
                                        $vahe = $tana->diff($saabumine)->days;
                                        $tulevikus = $saabumine > $tana;
                                        $saabTuhistada = ($vahe >= 3 && $tulevikus && $broneering['staatus'] === 'kinnitatud');
                                    ?>
                                    <tr class="<?= $broneering['staatus'] === 'tühistatud' ? 'table-secondary' : '' ?>">
                                        <?php if (admin() || tootaja()): ?>
                                            <td><?= $broneering['id'] ?></td>
                                        <?php endif; ?>
                                        
                                        <?php if (admin() || tootaja()): ?>
                                            <td>
                                                <?= htmlspecialchars($broneering['eesnimi'] . ' ' . $broneering['perenimi']) ?>
                                                <small class="text-muted d-block">(<?= $broneering['kasutaja_tyyp'] ?>)</small>
                                            </td>
                                        <?php endif; ?>
                                        
                                        <td><?= htmlspecialchars($broneering['toa_tyyp']) ?></td>
                                        <td><?= htmlspecialchars($broneering['toa_nr']) ?></td>
                                        <td><?= date('d.m.Y', strtotime($broneering['saabumine'])) ?></td>
                                        <td><?= date('d.m.Y', strtotime($broneering['lahkumine'])) ?></td>
                                        <td>
                                            <span class="badge 
                                                <?= $broneering['staatus'] === 'kinnitatud' ? 'bg-success' : '' ?>
                                                <?= $broneering['staatus'] === 'ootel' ? 'bg-warning text-dark' : '' ?>
                                                <?= $broneering['staatus'] === 'tühistatud' ? 'bg-secondary' : '' ?>
                                                <?= $broneering['staatus'] === 'lõpetatud' ? 'bg-info' : '' ?>
                                            ">
                                                <?= ucfirst($broneering['staatus']) ?>
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            <?php if (admin() || tootaja()): ?>
                                                <!-- Admin/töötaja saab muuta ja tühistada -->
                                                <button class="btn btn-sm btn-warning muuda-btn" 
                                                    data-id="<?= $broneering['id'] ?>"
                                                    data-toatyyp="<?= htmlspecialchars($broneering['toa_tyyp']) ?>"
                                                    data-toanr="<?= htmlspecialchars($broneering['toa_nr']) ?>"
                                                    data-saabumine="<?= $broneering['saabumine'] ?>"
                                                    data-lahkumine="<?= $broneering['lahkumine'] ?>"
                                                    data-staatus="<?= $broneering['staatus'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#muudaBroneeringModal"
                                                    title="Muuda broneeringut">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <?php if ($broneering['staatus'] !== 'tühistatud'): ?>
                                                    <button class="btn btn-sm btn-danger tuhista-btn" 
                                                        data-id="<?= $broneering['id'] ?>"
                                                        title="Tühista broneering">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php elseif ($saabTuhistada): ?>
                                                <!-- Klient saab ainult tühistada -->
                                                <button class="btn btn-sm btn-danger tuhista-btn" 
                                                    data-id="<?= $broneering['id'] ?>"
                                                    title="Tühista broneering">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php elseif ($broneering['staatus'] !== 'tühistatud'): ?>
                                                <!-- Kliendi tühistamise nupp, kui on liiga hilja -->
                                                <button class="btn btn-sm btn-secondary" 
                                                    disabled
                                                    title="Tühistamiseks on liiga hilja (peab olema vähemalt 3 päeva enne saabumist)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Broneeringuid ei leitud.</div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulge</button>
            </div>
        </div>
    </div>
</div>

<!-- Broneeringu muutmise modal (ainult admin/töötaja jaoks) -->
<?php if (admin() || tootaja()): ?>
<div class="modal fade" id="muudaBroneeringModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Muuda broneeringut</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="muudaBroneeringForm" method="post" action="../haldus/muuda_broneering.php">
                <div class="modal-body">
                    <input type="hidden" id="muudaBroneeringId" name="id">
                    <div class="mb-3">
                        <label for="muudaToaTyyp" class="form-label">Toa tüüp</label>
                        <input type="text" class="form-control" id="muudaToaTyyp" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="muudaToaNr" class="form-label">Toa number</label>
                        <input type="text" class="form-control" id="muudaToaNr" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="muudaSaabumine" class="form-label">Saabumine</label>
                            <input type="date" class="form-control" id="muudaSaabumine" name="saabumine" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="muudaLahkumine" class="form-label">Lahkumine</label>
                            <input type="date" class="form-control" id="muudaLahkumine" name="lahkumine" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="muudaStaatus" class="form-label">Staatus</label>
                        <select class="form-select" id="muudaStaatus" name="staatus">
                            <option value="ootel">Ootel</option>
                            <option value="kinnitatud">Kinnitatud</option>
                            <option value="tühistatud">Tühistatud</option>
                            <option value="lõpetatud">Lõpetatud</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" class="btn btn-primary">Salvesta muudatused</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// JavaScript broneeringute tühistamiseks ja muutmiseks
document.addEventListener('DOMContentLoaded', function() {
    // Tühistamise nupud
    document.querySelectorAll('.tuhista-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const broneeringId = this.getAttribute('data-id');
            if (confirm('Kas olete kindel, et soovite broneeringu tühistada?')) {
                fetch('../haldus/tyhista_broneering.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + broneeringId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Broneering tühistati edukalt!');
                        location.reload();
                    } else {
                        alert('Tühistamine ebaõnnestus: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Tekkis viga broneeringu tühistamisel');
                });
            }
        });
    });
    
    <?php if (admin() || tootaja()): ?>
    // Muutmise modal'i täitmine (ainult admin/töötaja jaoks)
    document.querySelectorAll('.muuda-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('muudaBroneeringId').value = this.getAttribute('data-id');
            document.getElementById('muudaToaTyyp').value = this.getAttribute('data-toatyyp');
            document.getElementById('muudaToaNr').value = this.getAttribute('data-toanr');
            document.getElementById('muudaSaabumine').value = this.getAttribute('data-saabumine');
            document.getElementById('muudaLahkumine').value = this.getAttribute('data-lahkumine');
            document.getElementById('muudaStaatus').value = this.getAttribute('data-staatus');
        });
    });
    
    // Kuupäevade kontroll muutmisvormis
    const saabumineInput = document.getElementById('muudaSaabumine');
    const lahkumineInput = document.getElementById('muudaLahkumine');
    
    if (saabumineInput && lahkumineInput) {
        saabumineInput.addEventListener('change', validateDates);
        lahkumineInput.addEventListener('change', validateDates);
    }
    
    function validateDates() {
        const saabumine = new Date(saabumineInput.value);
        const lahkumine = new Date(lahkumineInput.value);
        
        if (lahkumine <= saabumine) {
            alert('Lahkumise kuupäev peab olema hilisem kui saabumise kuupäev!');
            lahkumineInput.value = '';
        }
    }
    <?php endif; ?>
});
</script>

<?php endif; ?>

<?php if (admin()): ?>
<div class="modal fade" id="kasutajad" tabindex="-1" aria-labelledby="kasutajadLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kasutajate ja klientide haldus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sulge"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="kasutajadTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="kasutajad-tab" data-bs-toggle="tab" data-bs-target="#kasutajad-content" type="button">Kasutajad</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="kylalised-tab" data-bs-toggle="tab" data-bs-target="#kylalised-content" type="button">Külalised</button>
                    </li>
                </ul>
                
                <div class="tab-content mt-3" id="kasutajadTabsContent">
                    <!-- Kasutajate vaade -->
                    <div class="tab-pane fade show active" id="kasutajad-content" role="tabpanel">
                        <?php
                        $kasutajad = mysqli_query($yhendus, "
                            SELECT k.id, k.kasutajanimi, k.email, k.roll, 
                                   kl.eesnimi, kl.perenimi, kl.telefon, kl.isikukood
                            FROM kasutajad k
                            LEFT JOIN kliendid kl ON k.id = kl.kasutaja_id
                            ORDER BY k.loomis_aeg DESC
                        ");
                        ?>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Kasutajanimi</th>
                                        <th>Eesnimi</th>
                                        <th>Perekonnanimi</th>
                                        <th>E-post</th>
                                        <th>Telefon</th>
                                        <th>Roll</th>
                                        <th>Tegevused</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($kasutaja = mysqli_fetch_assoc($kasutajad)): ?>
                                        <tr>
                                            <td><?= $kasutaja['id'] ?></td>
                                            <td><?= htmlspecialchars($kasutaja['kasutajanimi']) ?></td>
                                            <td><?= htmlspecialchars($kasutaja['eesnimi'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($kasutaja['perenimi'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($kasutaja['email']) ?></td>
                                            <td><?= htmlspecialchars($kasutaja['telefon'] ?? '') ?></td>
                                            <td><?= ucfirst($kasutaja['roll']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning muudaKasutajaBtn" 
                                                    data-id="<?= $kasutaja['id'] ?>"
                                                    data-kasutajanimi="<?= htmlspecialchars($kasutaja['kasutajanimi']) ?>"
                                                    data-email="<?= htmlspecialchars($kasutaja['email']) ?>"
                                                    data-roll="<?= $kasutaja['roll'] ?>"
                                                    data-eesnimi="<?= htmlspecialchars($kasutaja['eesnimi'] ?? '') ?>"
                                                    data-perenimi="<?= htmlspecialchars($kasutaja['perenimi'] ?? '') ?>"
                                                    data-telefon="<?= htmlspecialchars($kasutaja['telefon'] ?? '') ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#muudaKasutajaModal">
                                                    Muuda
                                                </button>
                                                <button class="btn btn-sm btn-danger kustutaKasutajaBtn" data-id="<?= $kasutaja['id'] ?>">Kustuta</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Külaliste vaade -->
                    <div class="tab-pane fade" id="kylalised-content" role="tabpanel">
                        <?php
                        $kylalised = mysqli_query($yhendus, "SELECT * FROM kylalised ORDER BY loodud DESC");
                        ?>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Eesnimi</th>
                                        <th>Perekonnanimi</th>
                                        <th>Isikukood</th>
                                        <th>Telefon</th>
                                        <th>E-post</th>
                                        <th>Loodud</th>
                                        <th>Tegevused</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($kylaline = mysqli_fetch_assoc($kylalised)): ?>
                                        <tr>
                                            <td><?= $kylaline['id'] ?></td>
                                            <td><?= htmlspecialchars($kylaline['eesnimi']) ?></td>
                                            <td><?= htmlspecialchars($kylaline['perenimi']) ?></td>
                                            <td><?= htmlspecialchars($kylaline['isikukood']) ?></td>
                                            <td><?= htmlspecialchars($kylaline['telefon']) ?></td>
                                            <td><?= htmlspecialchars($kylaline['email']) ?></td>
                                            <td><?= $kylaline['loodud'] ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-danger kustutaKylalineBtn" data-id="<?= $kylaline['id'] ?>">Kustuta</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulge</button>
            </div>
        </div>
    </div>
</div>

<!-- Kasutaja muutmise modal -->
<div class="modal fade" id="muudaKasutajaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Muuda kasutajat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="muudaKasutajaForm" method="post" action="../haldus/muuda_kasutaja.php">
                <div class="modal-body">
                    <input type="hidden" id="muudaKasutajaId" name="id">
                    <div class="mb-3">
                        <label for="muudaKasutajanimi" class="form-label">Kasutajanimi</label>
                        <input type="text" class="form-control" id="muudaKasutajanimi" name="kasutajanimi" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaEmail" class="form-label">E-post</label>
                        <input type="email" class="form-control" id="muudaEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaRoll" class="form-label">Roll</label>
                        <select class="form-select" id="muudaRoll" name="roll">
                            <option value="admin">Admin</option>
                            <option value="töötaja">Töötaja</option>
                            <option value="klient">Klient</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="muudaEesnimi" class="form-label">Eesnimi</label>
                        <input type="text" class="form-control" id="muudaEesnimi" name="eesnimi">
                    </div>
                    <div class="mb-3">
                        <label for="muudaPerenimi" class="form-label">Perekonnanimi</label>
                        <input type="text" class="form-control" id="muudaPerenimi" name="perenimi">
                    </div>
                    <div class="mb-3">
                        <label for="muudaTelefon" class="form-label">Telefon</label>
                        <input type="text" class="form-control" id="muudaTelefon" name="telefon">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" class="btn btn-primary">Salvesta</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (admin()): ?>
<div class="modal fade" id="toad" tabindex="-1" aria-labelledby="toadLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tubade haldus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sulge"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#lisaTubaModal">Lisa uus tuba</button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Toa number</th>
                                <th>Korrus</th>
                                <th>Toa tüüp</th>
                                <th>Hind</th>
                                <th>Maht</th>
                                <th>Staatus</th>
                                <th>Tegevused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $toad = mysqli_query($yhendus, "
                                SELECT t.id, t.toa_nr, t.toa_korrus, tt.toa_tyyp, tt.toa_hind, tt.toa_maht
                                FROM toad t
                                JOIN toa_tyyp tt ON t.toa_id = tt.id
                                ORDER BY t.toa_korrus, t.toa_nr
                            ");
                            
                            while ($tuba = mysqli_fetch_assoc($toad)):
                            ?>
                                <tr>
                                    <td><?= $tuba['id'] ?></td>
                                    <td><?= htmlspecialchars($tuba['toa_nr']) ?></td>
                                    <td><?= $tuba['toa_korrus'] ?></td>
                                    <td><?= htmlspecialchars($tuba['toa_tyyp']) ?></td>
                                    <td><?= number_format($tuba['toa_hind'], 2) ?> €</td>
                                    <td><?= $tuba['toa_maht'] ?> inimest</td>
                                    <td>
                                        <span class="badge bg-success">Aktiivne</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning muudaTubaBtn" 
                                            data-id="<?= $tuba['id'] ?>"
                                            data-toanr="<?= htmlspecialchars($tuba['toa_nr']) ?>"
                                            data-korrus="<?= $tuba['toa_korrus'] ?>"
                                            data-toatyyp="<?= htmlspecialchars($tuba['toa_tyyp']) ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#muudaTubaModal">
                                            Muuda
                                        </button>
                                        <button class="btn btn-sm btn-danger kustutaTubaBtn" data-id="<?= $tuba['id'] ?>">Kustuta</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulge</button>
            </div>
        </div>
    </div>
</div>

<!-- Uue toa lisamise modal -->
<div class="modal fade" id="lisaTubaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lisa uus tuba</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="lisaTubaForm" method="post" action="../haldus/lisa_tuba.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lisaToaNr" class="form-label">Toa number</label>
                        <input type="text" class="form-control" id="lisaToaNr" name="toa_nr" required>
                    </div>
                    <div class="mb-3">
                        <label for="lisaKorrus" class="form-label">Korrus</label>
                        <input type="number" class="form-control" id="lisaKorrus" name="korrus" min="1" max="10" required>
                    </div>
                    <div class="mb-3">
                        <label for="lisaToaTyyp" class="form-label">Toa tüüp</label>
                        <select class="form-select" id="lisaToaTyyp" name="toa_tyyp" required>
                            <?php
                            $toa_tyybid = mysqli_query($yhendus, "SELECT * FROM toa_tyyp ORDER BY toa_tyyp");
                            while ($tyyp = mysqli_fetch_assoc($toa_tyybid)):
                            ?>
                                <option value="<?= $tyyp['id'] ?>">
                                    <?= htmlspecialchars($tyyp['toa_tyyp']) ?> (<?= number_format($tyyp['toa_hind'], 2) ?> €)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" class="btn btn-primary">Lisa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toa muutmise modal -->
<div class="modal fade" id="muudaTubaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Muuda tuba</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="muudaTubaForm" method="post" action="../haldus/muuda_tuba.php">
                <div class="modal-body">
                    <input type="hidden" id="muudaTubaId" name="id">
                    <div class="mb-3">
                        <label for="muudaToaNr" class="form-label">Toa number</label>
                        <input type="text" class="form-control" id="muudaToaNr" name="toa_nr" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaKorrus" class="form-label">Korrus</label>
                        <input type="number" class="form-control" id="muudaKorrus" name="korrus" min="1" max="10" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaToaTyyp" class="form-label">Toa tüüp</label>
                        <select class="form-select" id="muudaToaTyyp" name="toa_tyyp" required>
                            <?php
                            $toa_tyybid = mysqli_query($yhendus, "SELECT * FROM toa_tyyp ORDER BY toa_tyyp");
                            while ($tyyp = mysqli_fetch_assoc($toa_tyybid)):
                            ?>
                                <option value="<?= $tyyp['id'] ?>">
                                    <?= htmlspecialchars($tyyp['toa_tyyp']) ?> (<?= number_format($tyyp['toa_hind'], 2) ?> €)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" class="btn btn-primary">Salvesta</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>