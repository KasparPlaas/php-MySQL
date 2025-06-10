<?php
include '../includes/header.php';
?>

<div class="container mt-5" style="max-width: 500px;">
    <h2>Unustasid parooli?</h2>
    <p>Sisesta oma e-posti aadress, kuhu soovid parooli taastamise linki saada.</p>

    <form action="../autentimine/unustasin_email.php" method="get">
        <div class="form-group">
            <label for="email">E-posti aadress:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Saada taastelink</button>
    </form>

    <div class="mt-3">
        <a href="../autentimine/login.php">Tagasi sisselogimise juurde</a>
    </div>
</div>

<?php
include '../includes/footer.php';
?>