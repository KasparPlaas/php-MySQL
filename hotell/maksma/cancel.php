<?php
include("../includes/header.php");

// Automaatne suunamine 15 sekundi pärast avalehele
header("Refresh: 15; URL=../avaleht/");
?>

<div class="container text-center mt-5">
    <div class="alert alert-warning shadow p-4 rounded">
        <h1 class="mb-3">Makse ebaõnnestus või katkestati</h1>
        <p>Te ei lõpeta maksmist, seega broneering on hetkel ootel.</p>
        <p class="text-muted">Vali, kas soovid proovida uuesti maksta või tühistada broneering.</p>
        
        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="../maksma/maksmine.php" class="btn btn-primary">Proovi uuesti maksta</a>
            <a href="../broneeri/broneering_tyhista.php" class="btn btn-outline-danger">Tühista broneering</a>
        </div>

        <p class="mt-4 text-muted">Teid suunatakse automaatselt avalehele 15 sekundi pärast...</p>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
