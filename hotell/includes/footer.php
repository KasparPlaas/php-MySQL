<?php
/**
 * Footer fail hotelli broneerimissüsteemi jaoks
 * Sisaldab lehe jalust, autoriõiguste teavet ja Bootstrapi JS-i
 */
?>

<!-- 404 lehe kontrolli skript -->
<script>
    /**
     * Kontrollib, kas praegune URL on kehtivate lehtede hulgas
     * NB! See on simulatsioon - päris rakenduses peaks kasutama serveripoolset kontrolli
     */
    const kehtivadUrlid = [
        '../avaleht',
        '../kontakt',
        '../tooted',
        '../teenused'
    ];

    const praeguneTeekond = window.location.pathname;

    if (kehtivadUrlid.includes(praeguneTeekond)) {
        // Suuname kasutaja õigele lehele, kui see eksisteerib
        window.location.href = praeguneTeekond;
    } else {
        // Logime veateate konsooli, kui lehte ei leitud
        console.log('404 Viga: Lehte ei leitud:', praeguneTeekond);
    }
</script>


<script>
    // Parooli näitamise/peitmise funktsionaalsus
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('parool');
        const icon = this.querySelector('svg');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.setAttribute('fill', '#0d6efd');
        } else {
            passwordInput.type = 'password';
            icon.setAttribute('fill', 'currentColor');
        }
    });
    
    // Vormi valideerimine
    (function() {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<!-- Modaalakende ja AJAX päringute skriptid -->
<script>
    /**
     * DOM-i laadimise lõpetamisel käivitatavad funktsioonid
     * Sisaldab kõiki modaalakende ja kustutamise funktsioone
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Broneeringu muutmise modaali seadistamine
        const muudaBroneeringNupud = document.querySelectorAll('.muuda-btn');
        muudaBroneeringNupud.forEach(nupp => {
            nupp.addEventListener('click', function() {
                // Täidame modaali väljad broneeringu andmetega
                document.getElementById('muudaBroneeringId').value = this.dataset.id;
                document.getElementById('muudaToaTyyp').value = this.dataset.toatyyp;
                document.getElementById('muudaToaNr').value = this.dataset.toanr;
                document.getElementById('muudaSaabumine').value = this.dataset.saabumine;
                document.getElementById('muudaLahkumine').value = this.dataset.lahkumine;
                document.getElementById('muudaStaatus').value = this.dataset.staatus;
            });
        });

        // Kasutaja muutmise modaali seadistamine
        const muudaKasutajaNupud = document.querySelectorAll('.muudaKasutajaBtn');
        muudaKasutajaNupud.forEach(nupp => {
            nupp.addEventListener('click', function() {
                // Täidame modaali väljad kasutaja andmetega
                document.getElementById('muudaKasutajaId').value = this.dataset.id;
                document.getElementById('muudaKasutajanimi').value = this.dataset.kasutajanimi;
                document.getElementById('muudaEmail').value = this.dataset.email;
                document.getElementById('muudaRoll').value = this.dataset.roll;
                document.getElementById('muudaEesnimi').value = this.dataset.eesnimi;
                document.getElementById('muudaPerenimi').value = this.dataset.perenimi;
                document.getElementById('muudaTelefon').value = this.dataset.telefon;
            });
        });

        // Toa muutmise modaali seadistamine
        const muudaTubaNupud = document.querySelectorAll('.muudaTubaBtn');
        muudaTubaNupud.forEach(nupp => {
            nupp.addEventListener('click', function() {
                // Täidame modaali väljad toa andmetega
                document.getElementById('muudaTubaId').value = this.dataset.id;
                document.getElementById('muudaToaNr').value = this.dataset.toanr;
                document.getElementById('muudaKorrus').value = this.dataset.korrus;
                
                // Valime õige toatüübi rippmenüüst
                const toaTyypSelect = document.getElementById('muudaToaTyyp');
                for (let i = 0; i < toaTyypSelect.options.length; i++) {
                    if (toaTyypSelect.options[i].text.includes(this.dataset.toatyyp)) {
                        toaTyypSelect.selectedIndex = i;
                        break;
                    }
                }
            });
        });

        /**
         * AJAX päringute funktsioonid kustutamiseks
         * Iga funktsioon küsib kasutajalt kinnitust enne toimingu sooritamist
         */

        // Broneeringu tühistamise funktsioon
        const tuhistaNupud = document.querySelectorAll('.tuhista-btn');
        tuhistaNupud.forEach(nupp => {
            nupp.addEventListener('click', function() {
                if (confirm('Kas olete kindel, et soovite broneeringu tühistada?')) {
                    const broneeringId = this.dataset.id;
                    fetch('../haldus/tuhista_broneering.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${broneeringId}`
                    })
                    .then(vastus => vastus.json())
                    .then(andmed => {
                        if (andmed.success) {
                            alert('Broneering tühistatud!');
                            location.reload();
                        } else {
                            alert('Viga: ' + andmed.message);
                        }
                    })
                    .catch(viga => {
                        console.error('Viga broneeringu tühistamisel:', viga);
                        alert('Tekkis tõrge broneeringu tühistamisel');
                    });
                }
            });
        });

        // Kasutaja kustutamise funktsioon
        const kustutaKasutajaNupud = document.querySelectorAll('.kustutaKasutajaBtn');
        kustutaKasutajaNupud.forEach(nupp => {
            nupp.addEventListener('click', function() {
                if (confirm('Kas olete kindel, et soovite kasutaja kustutada?')) {
                    const kasutajaId = this.dataset.id;
                    fetch('../haldus/kustuta_kasutaja.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${kasutajaId}`
                    })
                    .then(vastus => vastus.json())
                    .then(andmed => {
                        if (andmed.success) {
                            alert('Kasutaja kustutatud!');
                            location.reload();
                        } else {
                            alert('Viga: ' + andmed.message);
                        }
                    })
                    .catch(viga => {
                        console.error('Viga kasutaja kustutamisel:', viga);
                        alert('Tekkis tõrge kasutaja kustutamisel');
                    });
                }
            });
        });

        // Külalise kustutamise funktsioon
        const kustutaKylalineNupud = document.querySelectorAll('.kustutaKylalineBtn');
        kustutaKylalineNupud.forEach(nupp => {
            nupp.addEventListener('click', function() {
                if (confirm('Kas olete kindel, et soovite külalise kustutada?')) {
                    const kylalineId = this.dataset.id;
                    fetch('../haldus/kustuta_kylaline.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${kylalineId}`
                    })
                    .then(vastus => vastus.json())
                    .then(andmed => {
                        if (andmed.success) {
                            alert('Külaline kustutatud!');
                            location.reload();
                        } else {
                            alert('Viga: ' + andmed.message);
                        }
                    })
                    .catch(viga => {
                        console.error('Viga külalise kustutamisel:', viga);
                        alert('Tekkis tõrge külalise kustutamisel');
                    });
                }
            });
        });

        // Toa kustutamise funktsioon
        const kustutaTubaNupud = document.querySelectorAll('.kustutaTubaBtn');
        kustutaTubaNupud.forEach(nupp => {
            nupp.addEventListener('click', function() {
                if (confirm('Kas olete kindel, et soovite toa kustutada?')) {
                    const tubaId = this.dataset.id;
                    fetch('../haldus/kustuta_tuba.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${tubaId}`
                    })
                    .then(vastus => vastus.json())
                    .then(andmed => {
                        if (andmed.success) {
                            alert('Tuba kustutatud!');
                            location.reload();
                        } else {
                            alert('Viga: ' + andmed.message);
                        }
                    })
                    .catch(viga => {
                        console.error('Viga toa kustutamisel:', viga);
                        alert('Tekkis tõrge toa kustutamisel');
                    });
                }
            });
        });
    });
</script>

<!-- Lehe jalus -->
<footer class="bg-light text-center text-lg-start mt-auto">
    <div class="container p-4">
        <div class="row">
            
            <!-- Vasakpoolne veerg - logo ja autoriõigused -->
            <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-3"><?= htmlspecialchars($hotelliNimi ?? 'Meie Hotell'); ?></h5>
                <p class="mb-1">
                    <i class="bi bi-geo-alt-fill me-2"></i> <?= htmlspecialchars($aadress ?? 'Näite tänav 123, Tallinn'); ?>
                </p>
                <p class="mb-1">
                    <i class="bi bi-telephone-fill me-2"></i> <?= htmlspecialchars($telefon ?? '+372 1234 5678'); ?>
                </p>
                <p class="mb-0">
                    &copy; <?= date('Y'); ?> Kõik õigused kaitstud.
                </p>
            </div>

            <!-- Keskmine veerg - kiirlingid -->
            <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-3">Kiirlingid</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="/" class="text-body text-decoration-none">
                            <i class="bi bi-house-door me-2"></i> Avaleht
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="/broneeri.php" class="text-body text-decoration-none">
                            <i class="bi bi-calendar-check me-2"></i> Broneeri tuba
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="/meist.php" class="text-body text-decoration-none">
                            <i class="bi bi-info-circle me-2"></i> Meist
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="/kontakt.php" class="text-body text-decoration-none">
                            <i class="bi bi-envelope me-2"></i> Kontakt
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Parempoolne veerg - sotsiaalmeedia -->
            <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-3">Jälgi meid</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#" class="text-body text-decoration-none">
                            <i class="bi bi-facebook me-2"></i> Facebook
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-body text-decoration-none">
                            <i class="bi bi-instagram me-2"></i> Instagram
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-body text-decoration-none">
                            <i class="bi bi-linkedin me-2"></i> LinkedIn
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-body text-decoration-none">
                            <i class="bi bi-twitter-x me-2"></i> Twitter/X
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Alumine riba - lisateave -->
    <div class="text-center p-3 bg-body-tertiary">
        <small>
            <a href="/privaatsuspoliitika.php" class="text-body text-decoration-none me-3">Privaatsuspoliitika</a>
            <a href="/tingimused.php" class="text-body text-decoration-none me-3">Kasutustingimused</a>
            <a href="/küsimustik.php" class="text-body text-decoration-none">Tagasiside</a>
        </small>
    </div>
</footer>

<!-- Bootstrapi JS ja ikoonid -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" 
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.js"></script>

</body>
</html>