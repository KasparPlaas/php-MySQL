<?php
$veateade = "";
include '../includes/andmebaas.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kasutaja = trim($_POST["kasutaja"] ?? '');
    $parool = $_POST["parool"] ?? '';
    $meelespea = isset($_POST["meelespea"]) ? 1 : 0;

    if (empty($kasutaja) || empty($parool)) {
        $veateade = "Palun täida kõik väljad.";
    } else {
        $kasutaja_puhas = mysqli_real_escape_string($yhendus, $kasutaja);
        $paring = mysqli_query($yhendus, "
            SELECT id, kasutajanimi, email, parool, email_kinnitatud, email_kinnituskood, email_koodi_aeg 
            FROM kasutajad 
            WHERE kasutajanimi = '$kasutaja_puhas' OR email = '$kasutaja_puhas'
        ");

        if ($row = mysqli_fetch_assoc($paring)) {
            if (password_verify($parool, $row['parool'])) {
                if (!$row['email_kinnitatud']) {
                    $hetke_aeg = time();
                    $viimane = strtotime($row['email_koodi_aeg'] ?? '1970-01-01 00:00:00');
                    $aeg_mootas = ($hetke_aeg - $viimane) >= 300;

                    $email_encoded = urlencode($row["email"]);
                    $link = $aeg_mootas
                        ? "<a href='saada_kood.php?email=$email_encoded'>Saada uus kood</a>"
                        : "Proovi mõne minuti pärast uuesti.";

                    $veateade = "Sinu konto ei ole veel kinnitatud. $link";
                } else {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION["kasutaja_id"] = $row["id"];
                    $_SESSION["kasutajanimi"] = $row["kasutajanimi"] ?? $row["email"];
                    
                    // Küpsise seadistamine kui "Meelespea" on valitud
                    if ($meelespea) {
                        $cookie_value = json_encode([
                            'id' => $row['id'],
                            'token' => bin2hex(random_bytes(16))
                        ]);
                        setcookie('rememberme', $cookie_value, time() + (86400 * 30), "/"); // 30 päeva
                    }
                    
                    header("Location: ../avaleht");
                    exit;
                }
            } else {
                $veateade = "Vale parool.";
            }
        } else {
            $veateade = "Kasutajat ei leitud.";
        }
    }
}
include '../includes/header.php';
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4" style="width: 100%; max-width: 450px; border-radius: 15px; border: none;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <div class="icon-circle bg-primary mb-3 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" class="bi bi-person-fill" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                    </svg>
                </div>
                <h2 class="card-title mb-2" style="color: #2c3e50;">Tere tulemast tagasi</h2>
                <p class="text-muted">Logi sisse, et jätkata</p>
            </div>
            
            <?php if (!empty($veateade)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-exclamation-circle-fill me-2" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                        </svg>
                        <?= htmlspecialchars($veateade) ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" class="needs-validation" novalidate>
                <div class="mb-4">
                    <label for="kasutaja" class="form-label fw-medium" style="color: #495057;">Kasutajanimi või e-post</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#6c757d" class="bi bi-person-fill" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                            </svg>
                        </span>
                        <input type="text" class="form-control py-2" id="kasutaja" name="kasutaja" placeholder="Sisesta kasutajanimi või e-post" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="parool" class="form-label fw-medium" style="color: #495057;">Parool</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#6c757d" class="bi bi-lock-fill" viewBox="0 0 16 16">
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                            </svg>
                        </span>
                        <input type="password" class="form-control py-2" id="parool" name="parool" placeholder="Sisesta parool" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="meelespea" name="meelespea">
                        <label class="form-check-label" for="meelespea" style="color: #495057;">Jäta mind meelde</label>
                    </div>
                    <a href="../autentimine/unustasin.php" class="text-decoration-none" style="color: #0d6efd;">Unustasid parooli?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-3" style="border-radius: 8px; background: linear-gradient(135deg, #0d6efd, #0b5ed7); border: none;">
                    Logi sisse
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-1" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                    </svg>
                </button>
                <div class="text-center mt-3">
                    <p class="text-muted">Pole veel kontot? <a href="../autentimine/register.php" class="text-decoration-none fw-medium" style="color: #0d6efd;">Registreeri</a></p>
                </div>
            </form>
        </div>
    </div>
</div>


<?php include '../includes/footer.php'; ?>