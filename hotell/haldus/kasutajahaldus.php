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
                        // Kasutajate uuendamine
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_kasutaja'])) {
                            $id = mysqli_real_escape_string($yhendus, $_POST['id']);
                            $kasutajanimi = mysqli_real_escape_string($yhendus, $_POST['kasutajanimi']);
                            $email = mysqli_real_escape_string($yhendus, $_POST['email']);
                            $roll = mysqli_real_escape_string($yhendus, $_POST['roll']);
                            $eesnimi = mysqli_real_escape_string($yhendus, $_POST['eesnimi']);
                            $perenimi = mysqli_real_escape_string($yhendus, $_POST['perenimi']);
                            $telefon = mysqli_real_escape_string($yhendus, $_POST['telefon']);
                            $isikukood = mysqli_real_escape_string($yhendus, $_POST['isikukood']);
                            
                            // Uuenda kasutaja tabelis
                            mysqli_query($yhendus, "UPDATE kasutajad SET 
                                kasutajanimi = '$kasutajanimi',
                                email = '$email',
                                roll = '$roll'
                                WHERE id = $id");
                            
                            // Uuenda või lisa klient
                            $klient_check = mysqli_query($yhendus, "SELECT * FROM kliendid WHERE kasutaja_id = $id");
                            if (mysqli_num_rows($klient_check)) {
                                mysqli_query($yhendus, "UPDATE kliendid SET 
                                    eesnimi = '$eesnimi',
                                    perenimi = '$perenimi',
                                    telefon = '$telefon',
                                    isikukood = '$isikukood'
                                    WHERE kasutaja_id = $id");
                            } else {
                                mysqli_query($yhendus, "INSERT INTO kliendid 
                                    (kasutaja_id, eesnimi, perenimi, telefon, isikukood)
                                    VALUES ($id, '$eesnimi', '$perenimi', '$telefon', '$isikukood')");
                            }
                            
                            echo '<div class="alert alert-success">Kasutaja andmed uuendatud!</div>';
                        }
                        
                        // Kasutajate kuvamine
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
                                            <form method="post" action="">
                                                <td><?= $kasutaja['id'] ?></td>
                                                <td>
                                                    <input type="hidden" name="id" value="<?= $kasutaja['id'] ?>">
                                                    <input type="text" class="form-control form-control-sm" name="kasutajanimi" value="<?= htmlspecialchars($kasutaja['kasutajanimi']) ?>" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="eesnimi" value="<?= htmlspecialchars($kasutaja['eesnimi'] ?? '') ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="perenimi" value="<?= htmlspecialchars($kasutaja['perenimi'] ?? '') ?>">
                                                </td>
                                                <td>
                                                    <input type="email" class="form-control form-control-sm" name="email" value="<?= htmlspecialchars($kasutaja['email']) ?>" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="telefon" value="<?= htmlspecialchars($kasutaja['telefon'] ?? '') ?>">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="roll">
                                                        <option value="admin" <?= $kasutaja['roll'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                        <option value="töötaja" <?= $kasutaja['roll'] == 'töötaja' ? 'selected' : '' ?>>Töötaja</option>
                                                        <option value="klient" <?= $kasutaja['roll'] == 'klient' ? 'selected' : '' ?>>Klient</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="isikukood" value="<?= htmlspecialchars($kasutaja['isikukood'] ?? '') ?>">
                                                    <button type="submit" name="update_kasutaja" class="btn btn-sm btn-primary">Salvesta</button>
                                                    <a href="?kustuta_kasutaja=<?= $kasutaja['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Kas olete kindel?')">Kustuta</a>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Külaliste vaade -->
                    <div class="tab-pane fade" id="kylalised-content" role="tabpanel">
                        <?php
                        // Külaliste uuendamine
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_kylaline'])) {
                            $id = mysqli_real_escape_string($yhendus, $_POST['id']);
                            $eesnimi = mysqli_real_escape_string($yhendus, $_POST['eesnimi']);
                            $perenimi = mysqli_real_escape_string($yhendus, $_POST['perenimi']);
                            $isikukood = mysqli_real_escape_string($yhendus, $_POST['isikukood']);
                            $telefon = mysqli_real_escape_string($yhendus, $_POST['telefon']);
                            $email = mysqli_real_escape_string($yhendus, $_POST['email']);
                            
                            mysqli_query($yhendus, "UPDATE kylalised SET 
                                eesnimi = '$eesnimi',
                                perenimi = '$perenimi',
                                isikukood = '$isikukood',
                                telefon = '$telefon',
                                email = '$email'
                                WHERE id = $id");
                            
                            echo '<div class="alert alert-success">Külalise andmed uuendatud!</div>';
                        }
                        
                        // Külaliste kustutamine
                        if (isset($_GET['kustuta_kylaline'])) {
                            $id = mysqli_real_escape_string($yhendus, $_GET['kustuta_kylaline']);
                            mysqli_query($yhendus, "DELETE FROM kylalised WHERE id = $id");
                            echo '<div class="alert alert-success">Külaline kustutatud!</div>';
                        }
                        
                        // Külaliste kuvamine
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
                                            <form method="post" action="">
                                                <td><?= $kylaline['id'] ?></td>
                                                <td>
                                                    <input type="hidden" name="id" value="<?= $kylaline['id'] ?>">
                                                    <input type="text" class="form-control form-control-sm" name="eesnimi" value="<?= htmlspecialchars($kylaline['eesnimi']) ?>" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="perenimi" value="<?= htmlspecialchars($kylaline['perenimi']) ?>" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="isikukood" value="<?= htmlspecialchars($kylaline['isikukood']) ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="telefon" value="<?= htmlspecialchars($kylaline['telefon']) ?>">
                                                </td>
                                                <td>
                                                    <input type="email" class="form-control form-control-sm" name="email" value="<?= htmlspecialchars($kylaline['email']) ?>" required>
                                                </td>
                                                <td><?= date('d.m.Y H:i', strtotime($kylaline['loodud'])) ?></td>
                                                <td>
                                                    <button type="submit" name="update_kylaline" class="btn btn-sm btn-primary">Salvesta</button>
                                                    <a href="?kustuta_kylaline=<?= $kylaline['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Kas olete kindel?')">Kustuta</a>
                                                </td>
                                            </form>
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

<?php
// Kasutajate kustutamine
if (isset($_GET['kustuta_kasutaja'])) {
    $id = mysqli_real_escape_string($yhendus, $_GET['kustuta_kasutaja']);
    mysqli_query($yhendus, "DELETE FROM kasutajad WHERE id = $id");
    echo '<div class="alert alert-success">Kasutaja kustutatud!</div>';
}
?>
<?php endif; ?>