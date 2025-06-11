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