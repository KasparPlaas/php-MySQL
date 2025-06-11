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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>

        .invalid-feedback {
             display: none; 
        }

        .was-validated .form-control:invalid ~ .invalid-feedback,
        .was-validated .form-control:invalid ~ .invalid-tooltip {
            display: block;
        }

        .date-invalid {
            border-color: #dc3545;
        }


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
                 <?php if (!admin() || !tootaja()): ?>
                <li class="nav-item me-2">
                    <a class="btn btn-primary rounded-pill px-3 shadow-sm" href="<?= $kasutaja ? '../broneeri/broneeri_klient.php' : '../broneeri/broneeri_kylalised.php' ?>">
                        <i class="fas fa-calendar-check me-1"></i> Broneerima
                    </a>
                </li>
                <?php endif; ?>

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
            <?php if (!admin() || !tootaja()): ?>
            <li class="border-bottom">
                <a href="<?= klient() ? '../broneeri/broneeri_klient.php' : '../broneeri/broneeri_kylalised.php' ?>" class="nav-link px-4 py-3 d-block text-dark hover-bg-primary hover-text-white">
                    <i class="fas fa-calendar-check me-2"></i>Broneeri
                </a>
            </li>
            <?php endif; ?>
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
    <?php include '../haldus/konto.php'; ?>
<?php endif; ?>

<?php if (admin() || tootaja() || klient()): ?>
    <?php include '../haldus/broneeringud.php'; ?>
<?php endif; ?>

<?php if (admin()): ?>
    <?php include '../haldus/kasutajahaldus.php'; ?>
<?php endif; ?>

<?php if (admin()): ?>
    <?php include '../haldus/tubadehaldus.php'; ?>
<?php endif; ?>