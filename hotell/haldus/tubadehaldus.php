<?php 
// Kontrollime, kas on admin ja kas vormid on saadetud
if (admin()):
    // Uue toa tüübi lisamine
    if (isset($_POST['lisa_toa_tyyp'])) {
        $toa_tyyp = mysqli_real_escape_string($yhendus, $_POST['toa_tyyp']);
        $toa_hind = floatval($_POST['toa_hind']);
        $toa_maht = intval($_POST['toa_maht']);
        
        $sql = "INSERT INTO toa_tyyp (toa_tyyp, toa_hind, toa_maht) 
                VALUES ('$toa_tyyp', $toa_hind, $toa_maht)";
        mysqli_query($yhendus, $sql);
        $teade = "Toa tüüp lisatud!";
    }

    // Toa tüübi muutmine
    if (isset($_POST['muuda_toa_tyyp'])) {
        $id = intval($_POST['id']);
        $toa_tyyp = mysqli_real_escape_string($yhendus, $_POST['toa_tyyp']);
        $toa_hind = floatval($_POST['toa_hind']);
        $toa_maht = intval($_POST['toa_maht']);
        
        $sql = "UPDATE toa_tyyp SET 
                toa_tyyp = '$toa_tyyp',
                toa_hind = $toa_hind,
                toa_maht = $toa_maht
                WHERE id = $id";
        mysqli_query($yhendus, $sql);
        $teade = "Toa tüüp muudetud!";
    }

    // Uue toa lisamine
    if (isset($_POST['lisa_tuba'])) {
        $toa_nr = mysqli_real_escape_string($yhendus, $_POST['toa_nr']);
        $korrus = intval($_POST['korrus']);
        $toa_tyyp = intval($_POST['toa_tyyp']);
        
        $sql = "INSERT INTO toad (toa_id, toa_nr, toa_korrus) 
                VALUES ($toa_tyyp, '$toa_nr', $korrus)";
        mysqli_query($yhendus, $sql);
        $teade = "Tuba lisatud!";
    }

    // Toa muutmine
    if (isset($_POST['muuda_tuba'])) {
        $id = intval($_POST['id']);
        $toa_nr = mysqli_real_escape_string($yhendus, $_POST['toa_nr']);
        $korrus = intval($_POST['korrus']);
        $toa_tyyp = intval($_POST['toa_tyyp']);
        
        $sql = "UPDATE toad SET 
                toa_id = $toa_tyyp,
                toa_nr = '$toa_nr',
                toa_korrus = $korrus
                WHERE id = $id";
        mysqli_query($yhendus, $sql);
        $teade = "Tuba muudetud!";
    }

    // Kustutamise toimingud
    if (isset($_GET['kustuta_toa_tyyp'])) {
        $id = intval($_GET['kustuta_toa_tyyp']);
        mysqli_query($yhendus, "DELETE FROM toa_tyyp WHERE id = $id");
        $teade = "Toa tüüp kustutatud!";
    }

    if (isset($_GET['kustuta_tuba'])) {
        $id = intval($_GET['kustuta_tuba']);
        mysqli_query($yhendus, "DELETE FROM toad WHERE id = $id");
        $teade = "Tuba kustutatud!";
    }
?>
<div class="modal fade" id="toad" tabindex="-1" aria-labelledby="toadLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tubade haldus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sulge"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($teade)): ?>
                    <div class="alert alert-success"><?= $teade ?></div>
                <?php endif; ?>
                
                <!-- Toa tüübid -->
                <div class="mb-4">
                    <h4>Toa tüübid</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#lisaToaTyypModal">Lisa uus toa tüüp</button>
                    
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Toa tüüp</th>
                                    <th>Hind</th>
                                    <th>Maht</th>
                                    <th>Tegevused</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tyybid = mysqli_query($yhendus, "SELECT * FROM toa_tyyp ORDER BY toa_tyyp");
                                while ($tyyp = mysqli_fetch_assoc($tyybid)):
                                ?>
                                    <tr>
                                        <td><?= $tyyp['id'] ?></td>
                                        <td><?= htmlspecialchars($tyyp['toa_tyyp']) ?></td>
                                        <td><?= number_format($tyyp['toa_hind'], 2) ?> €</td>
                                        <td><?= $tyyp['toa_maht'] ?> inimest</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" 
                                                onclick="document.getElementById('muudaToaTyypId').value='<?= $tyyp['id'] ?>';
                                                         document.getElementById('muudaToaTyyp').value='<?= htmlspecialchars($tyyp['toa_tyyp']) ?>';
                                                         document.getElementById('muudaToaHind').value='<?= $tyyp['toa_hind'] ?>';
                                                         document.getElementById('muudaToaMaht').value='<?= $tyyp['toa_maht'] ?>';
                                                         new bootstrap.Modal(document.getElementById('muudaToaTyypModal')).show();">
                                                Muuda
                                            </button>
                                            <a href="?kustuta_toa_tyyp=<?= $tyyp['id'] ?>" class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Kas olete kindel, et soovite selle toa tüübi kustutada?')">
                                                Kustuta
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Toad -->
                <div class="mb-4">
                    <h4>Toad</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#lisaTubaModal">Lisa uus tuba</button>
                    
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Toa number</th>
                                    <th>Korrus</th>
                                    <th>Toa tüüp</th>
                                    <th>Hind</th>
                                    <th>Maht</th>
                                    <th>Tegevused</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $toad = mysqli_query($yhendus, "
                                    SELECT t.id, t.toa_nr, t.toa_korrus, tt.id as tyyp_id, tt.toa_tyyp, tt.toa_hind, tt.toa_maht
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
                                            <button class="btn btn-sm btn-warning" 
                                                onclick="document.getElementById('muudaTubaId').value='<?= $tuba['id'] ?>';
                                                         document.getElementById('muudaToaNr').value='<?= htmlspecialchars($tuba['toa_nr']) ?>';
                                                         document.getElementById('muudaKorrus').value='<?= $tuba['toa_korrus'] ?>';
                                                         document.getElementById('muudaToaTyyp').value='<?= $tuba['tyyp_id'] ?>';
                                                         new bootstrap.Modal(document.getElementById('muudaTubaModal')).show();">
                                                Muuda
                                            </button>
                                            <a href="?kustuta_tuba=<?= $tuba['id'] ?>" class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Kas olete kindel, et soovite selle toa kustutada?')">
                                                Kustuta
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulge</button>
            </div>
        </div>
    </div>
</div>

<!-- Uue toa tüübi lisamise modal -->
<div class="modal fade" id="lisaToaTyypModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lisa uus toa tüüp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lisaToaTyyp" class="form-label">Toa tüüp</label>
                        <input type="text" class="form-control" id="lisaToaTyyp" name="toa_tyyp" required>
                    </div>
                    <div class="mb-3">
                        <label for="lisaToaHind" class="form-label">Hind (€)</label>
                        <input type="number" class="form-control" id="lisaToaHind" name="toa_hind" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="lisaToaMaht" class="form-label">Inimeste arv</label>
                        <input type="number" class="form-control" id="lisaToaMaht" name="toa_maht" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" name="lisa_toa_tyyp" class="btn btn-primary">Lisa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toa tüübi muutmise modal -->
<div class="modal fade" id="muudaToaTyypModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Muuda toa tüüpi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" id="muudaToaTyypId" name="id">
                    <div class="mb-3">
                        <label for="muudaToaTyyp" class="form-label">Toa tüüp</label>
                        <input type="text" class="form-control" id="muudaToaTyyp" name="toa_tyyp" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaToaHind" class="form-label">Hind (€)</label>
                        <input type="number" class="form-control" id="muudaToaHind" name="toa_hind" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="muudaToaMaht" class="form-label">Inimeste arv</label>
                        <input type="number" class="form-control" id="muudaToaMaht" name="toa_maht" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" name="muuda_toa_tyyp" class="btn btn-primary">Salvesta</button>
                </div>
            </form>
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
            <form method="post">
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
                    <button type="submit" name="lisa_tuba" class="btn btn-primary">Lisa</button>
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
            <form method="post">
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
                    <button type="submit" name="muuda_tuba" class="btn btn-primary">Salvesta</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>