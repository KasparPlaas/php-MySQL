<?php
include '../includes/andmebaas.php';

$teade = "";

if (isset($_GET["kood"])) {
    $kood = mysqli_real_escape_string($yhendus, $_GET["kood"]);

    $kontroll = mysqli_query($yhendus, "
        SELECT id FROM kasutajad 
        WHERE email_kinnituskood = '$kood' 
          AND email_kinnitatud = 0
    ");

    if (mysqli_num_rows($kontroll) === 1) {
        mysqli_query($yhendus, "
            UPDATE kasutajad 
            SET email_kinnitatud = 1, email_kinnituskood = NULL 
            WHERE email_kinnituskood = '$kood'
        ");
        $teade = "E-post kinnitatud! Suuname sind sisselogimisele...";
        header("refresh:3; url=/hotell/autentimine/login.php");
    } else {
        $teade = "Kood on vigane või juba kasutatud.";
    }
} else {
    $teade = "Kinnituskood puudub.";
}
include '../includes/header.php';
?>

<div class="container mt-5" style="max-width: 500px;">
    <div class="alert alert-info">
        <?= htmlspecialchars($teade) ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
