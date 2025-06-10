<?php include '../includes/header.php'; ?>
<!-- Hero Section -->
<section class="hero-section justify-content-center align-items-center text-center py-5 ">
    <div class="container ">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content mb-5 mt-3">
                    <h1 class="hero-title">
                        <i class="bi bi-house-heart me-3"></i>
                        Tere tulemast hotelli!
                    </h1>
                    <p class="hero-subtitle">
                        <i class="bi bi-star-fill me-2"></i>
                        Pakume parimat puhkuskogemust Eestis koos meeldejäävate hetkedega
                    </p>
                    <a href="#toad" class="btn btn-hero btn-lg">
                        <i class="bi bi-arrow-down-circle me-2"></i>
                        Avasta meie tube
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image">
                    <img src="../pildid/hotell.jpg" alt="Meie luksushotell" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rooms Section -->
<section class="py-5" id="toad">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="section-title">
                    <i class="bi bi-building me-3"></i>
                    Meie eksklusiivsed toad
                </h2>
                <p class="section-subtitle">
                    <i class="bi bi-gem me-2"></i>
                    Vali endale sobiv luksuslik puhkepaik
                </p>
            </div>
        </div>
        
        <div class="row">
            <?php
            // Päring toa tüüpide kohta
            $päring = "SELECT * FROM toa_tyyp";
            $tulemus = $yhendus->query($päring);
            
            if ($tulemus->num_rows > 0) {
                while ($tuba = $tulemus->fetch_assoc()) {
                    echo '
                    <div class="col-lg-4 col-md-6 mb-5">
                        <div class="card room-card shadow">
                            <div class="position-relative overflow-hidden">
                                <img src="'.$tuba['toa_pilt'].'" class="card-img-top" alt="'.$tuba['toa_tyyp'].'">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">'.$tuba['toa_tyyp'].'</h5>
                                <p class="card-text">'.$tuba['toa_kirjeldus'].'</p>
                                <ul class="room-features list-unstyled">
                                    <li>
                                        <i class="bi bi-people-fill"></i>
                                        Kuni '.$tuba['toa_maht'].' inimest
                                    </li>
                                    <li>
                                        <i class="bi bi-wifi"></i>
                                        Tasuta WiFi
                                    </li>
                                    <li>
                                        <i class="bi bi-tv"></i>
                                        Smart TV
                                    </li>
                                    <li>
                                        <i class="bi bi-cup-hot"></i>
                                        Minibaar
                                    </li>
                                </ul>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price-tag">'.number_format($tuba['toa_hind'], 0).'€</div>
                                    <small class="text-muted">öö kohta</small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="broneeri.php?tyyp_id='.$tuba['id'].'" class="btn btn-book btn-primary w-100">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    Broneeri kohe
                                </a>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="col-12"><div class="alert alert-info text-center">Toad hetkel puuduvad.</div></div>';
            }
            ?>
        </div>
    </div>
</section>


<!-- Contact Section -->
<section class="contact-section mt-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="section-title">
                    <i class="bi bi-chat-dots me-3"></i>
                    Võta meiega ühendust
                </h2>
                <p class="section-subtitle">
                    <i class="bi bi-telephone-fill me-2"></i>
                    Oleme alati valmis sind aitama
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="contact-info">
                    <h4 class="mb-4 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>
                        Kontaktandmed
                    </h4>
                    
                    <div class="contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>
                            <h6 class="mb-1">Aadress</h6>
                            <p class="mb-0">Hotelli 1, Tallinn, Eesti</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <i class="bi bi-telephone-fill"></i>
                        <div>
                            <h6 class="mb-1">Telefon</h6>
                            <p class="mb-0">+372 1234 5678</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <i class="bi bi-envelope-fill"></i>
                        <div>
                            <h6 class="mb-1">E-post</h6>
                            <p class="mb-0">info@meiehotell.ee</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <i class="bi bi-clock-fill"></i>
                        <div>
                            <h6 class="mb-1">Vastuvõtt</h6>
                            <p class="mb-0">24/7 teenindus</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="contact-form">
                    <h4 class="mb-4 fw-bold">
                        <i class="bi bi-envelope-paper me-2"></i>
                        Saada meile sõnum
                    </h4>
                    <form method="post" action="saada_kiri.php">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="text" class="form-control" name="nimi" placeholder="Sinu nimi" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="email" class="form-control" name="email" placeholder="E-posti aadress" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="teema" placeholder="Sõnumi teema">
                        </div>
                        <div class="mb-4">
                            <textarea class="form-control" name="kiri" rows="5" placeholder="Sinu sõnum või küsimus..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-send btn-primary">
                            <i class="bi bi-send me-2"></i>
                            Saada sõnum
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include '../includes/footer.php';
?>