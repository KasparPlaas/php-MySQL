<?php
// Kontrollime, kas kasutaja on sisse logitud
if (admin() || tootaja() || klient()): 
?>
<div class="modal fade" id="broneeringud" tabindex="-1" aria-labelledby="broneeringudLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="broneeringudModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <?= (admin() || tootaja()) ? 'Kõik broneeringud' : 'Minu broneeringud' ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Sulge"></button>
            </div>
            <div class="modal-body p-0">
                <?php
                // Kui vorm on saadetud (uuendamiseks)
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['broneering_id'])) {
                    $broneering_id = intval($_POST['broneering_id']);
                    $staatus = mysqli_real_escape_string($yhendus, $_POST['staatus']);
                    $toa_nr = mysqli_real_escape_string($yhendus, $_POST['toa_nr']);
                    $saabumine = mysqli_real_escape_string($yhendus, $_POST['saabumine']);
                    $lahkumine = mysqli_real_escape_string($yhendus, $_POST['lahkumine']);
                    
                    // Uuenda broneeringu andmeid
                    $update_sql = "UPDATE broneeringud SET 
                        staatus = '$staatus',
                        toa_id = (SELECT id FROM toad WHERE toa_nr = '$toa_nr'),
                        saabumine = '$saabumine',
                        lahkumine = '$lahkumine'
                    WHERE id = $broneering_id";
                    
                    if (mysqli_query($yhendus, $update_sql)) {
                        echo '<div class="alert alert-success">Broneeringu andmed uuendatud!</div>';
                    } else {
                        echo '<div class="alert alert-danger">Viga broneeringu uuendamisel: '.mysqli_error($yhendus).'</div>';
                    }
                }
                
                // Koostame SQL päringu vastavalt kasutaja rollile
                if (admin() || tootaja()) {
                    $sql = "SELECT 
                        broneeringud.id AS broneering_id,
                        toa_tyyp.toa_tyyp,
                        toad.toa_nr,
                        broneeringud.saabumine,
                        broneeringud.lahkumine,
                        maksed.summa,
                        maksed.tahtaeg,
                        maksed.makseviis,
                        broneeringud.staatus,
                        COALESCE(kliendid.eesnimi, kylalised.eesnimi) AS eesnimi,
                        COALESCE(kliendid.perenimi, kylalised.perenimi) AS perenimi,
                        CASE 
                            WHEN kliendid.id IS NOT NULL THEN 'Klient'
                            ELSE 'Külaline'
                        END AS kasutaja_tyyp
                    FROM broneeringud
                    JOIN toad ON broneeringud.toa_id = toad.id
                    JOIN toa_tyyp ON toad.toa_id = toa_tyyp.id
                    LEFT JOIN maksed ON maksed.broneering_id = broneeringud.id
                    LEFT JOIN kliendid ON broneeringud.klient_id = kliendid.id
                    LEFT JOIN kylalised ON broneeringud.kylaline_id = kylalised.id
                    ORDER BY broneeringud.saabumine DESC";
                } else {
                    $kasutaja_id = intval($_SESSION['kasutaja_id']);
                    $sql = "SELECT 
                        broneeringud.id AS broneering_id,
                        toa_tyyp.toa_tyyp,
                        toad.toa_nr,
                        broneeringud.saabumine,
                        broneeringud.lahkumine,
                        maksed.summa,
                        maksed.tahtaeg,
                        maksed.makseviis,
                        broneeringud.staatus
                    FROM broneeringud
                    JOIN toad ON broneeringud.toa_id = toad.id
                    JOIN toa_tyyp ON toad.toa_id = toa_tyyp.id
                    LEFT JOIN maksed ON maksed.broneering_id = broneeringud.id
                    JOIN kliendid ON broneeringud.klient_id = kliendid.id
                    WHERE kliendid.kasutaja_id = $kasutaja_id
                    ORDER BY broneeringud.saabumine DESC";
                }
                
                $tulemus = mysqli_query($yhendus, $sql);
                
                if ($tulemus && mysqli_num_rows($tulemus) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <?php if (admin() || tootaja()): ?>
                                        <th class="text-nowrap">ID</th>
                                        <th class="text-nowrap">Klient</th>
                                    <?php endif; ?>
                                    <th class="text-nowrap">Toa tüüp</th>
                                    <th class="text-nowrap">Toa nr</th>
                                    <th class="text-nowrap">Saabumine</th>
                                    <th class="text-nowrap">Lahkumine</th>
                                    <th class="text-nowrap">Summa</th>
                                    <th class="text-nowrap">Makseviis</th>
                                    <?php if (admin() || tootaja()): ?>
                                        <th class="text-nowrap">Tähtaeg</th>
                                    <?php endif; ?>
                                    <th class="text-nowrap">Staatus</th>
                                    <th class="text-nowrap text-end">Tegevused</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($broneering = mysqli_fetch_assoc($tulemus)): ?>
                                    <tr class="<?= $broneering['staatus'] == 'tühistatud' ? 'text-muted' : '' ?>">
                                        <?php if (admin() || tootaja()): ?>
                                            <td class="fw-bold">#<?= $broneering['broneering_id'] ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <?= htmlspecialchars($broneering['eesnimi'] . ' ' . $broneering['perenimi']) ?>
                                                        <small class="d-block text-muted"><?= $broneering['kasutaja_tyyp'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                        
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-bed me-1 text-primary"></i>
                                                <?= htmlspecialchars($broneering['toa_tyyp']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold"><?= htmlspecialchars($broneering['toa_nr']) ?></td>
                                        <td>
                                            <i class="far fa-calendar-alt me-1 text-primary"></i>
                                            <?= date('d.m.Y', strtotime($broneering['saabumine'])) ?>
                                        </td>
                                        <td>
                                            <i class="far fa-calendar-alt me-1 text-primary"></i>
                                            <?= date('d.m.Y', strtotime($broneering['lahkumine'])) ?>
                                        </td>
                                        <td class="fw-bold text-success">
                                            <?= $broneering['summa'] ? number_format($broneering['summa'], 2) . ' €' : '-' ?>
                                        </td>
                                        <td>
                                            <?php if ($broneering['makseviis']): ?>
                                                <span class="badge bg-light text-dark">
                                                    <?= $broneering['makseviis'] == 'pangaülekanne' ? 
                                                        '<i class="fas fa-university me-1"></i>' : 
                                                        '<i class="fas fa-credit-card me-1"></i>' ?>
                                                    <?= $broneering['makseviis'] ?>
                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        
                                        <?php if (admin() || tootaja()): ?>
                                            <td>
                                                <?php if ($broneering['tahtaeg'] && $broneering['makseviis'] == 'pangaülekanne'): ?>
                                                    <span class="badge <?= strtotime($broneering['tahtaeg']) < time() ? 'bg-danger' : 'bg-light text-dark' ?>">
                                                        <i class="far fa-clock me-1"></i>
                                                        <?= date('d.m.Y', strtotime($broneering['tahtaeg'])) ?>
                                                    </span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                        
                                        <td>
                                            <span class="badge rounded-pill 
                                                <?= $broneering['staatus'] == 'kinnitatud' ? 'bg-success' : '' ?>
                                                <?= $broneering['staatus'] == 'ootel' ? 'bg-warning text-dark' : '' ?>
                                                <?= $broneering['staatus'] == 'tühistatud' ? 'bg-secondary' : '' ?>
                                                <?= $broneering['staatus'] == 'lõpetatud' ? 'bg-info' : '' ?>
                                            ">
                                                <?= ucfirst($broneering['staatus']) ?>
                                            </span>
                                        </td>
                                        
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#broneeringDetailid"
                                                    data-id="<?= $broneering['broneering_id'] ?>"
                                                    title="Näita detaile">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                                
                                                <?php if (admin() || tootaja()): ?>
                                                    <button class="btn btn-outline-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#muudaBroneering"
                                                        data-id="<?= $broneering['broneering_id'] ?>"
                                                        title="Muuda broneeringut">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="far fa-calendar-times display-5 text-muted mb-3"></i>
                        <h5 class="text-muted">Broneeringuid ei leitud</h5>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Sulge
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Broneeringu detailide modal -->
<div class="modal fade" id="broneeringDetailid" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Broneeringu detailid</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Sulge"></button>
            </div>
            <div class="modal-body">
                <?php
                if (isset($_GET['broneering_id'])) {
                    $broneering_id = intval($_GET['broneering_id']);
                    $sql = "SELECT 
                        broneeringud.*,
                        toa_tyyp.toa_tyyp,
                        toa_tyyp.toa_hind,
                        toa_tyyp.toa_maht,
                        toa_tyyp.toa_kirjeldus,
                        toad.toa_nr,
                        toad.toa_korrus,
                        COALESCE(kliendid.eesnimi, kylalised.eesnimi) AS eesnimi,
                        COALESCE(kliendid.perenimi, kylalised.perenimi) AS perenimi,
                        COALESCE(kliendid.telefon, kylalised.telefon) AS telefon,
                        COALESCE(kliendid.isikukood, kylalised.isikukood) AS isikukood,
                        kylalised.email,
                        maksed.summa,
                        maksed.staatus AS makse_staatus,
                        maksed.makseviis,
                        maksed.tahtaeg
                    FROM broneeringud
                    JOIN toad ON broneeringud.toa_id = toad.id
                    JOIN toa_tyyp ON toad.toa_id = toa_tyyp.id
                    LEFT JOIN maksed ON maksed.broneering_id = broneeringud.id
                    LEFT JOIN kliendid ON broneeringud.klient_id = kliendid.id
                    LEFT JOIN kylalised ON broneeringud.kylaline_id = kylalised.id
                    WHERE broneeringud.id = $broneering_id";
                    
                    $tulemus = mysqli_query($yhendus, $sql);
                    if ($tulemus && mysqli_num_rows($tulemus) > 0) {
                        $row = mysqli_fetch_assoc($tulemus);
                        ?>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="mb-3">Broneeringu andmed</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Broneeringu ID</span>
                                        <span class="fw-bold">#<?= $row['id'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Toa tüüp</span>
                                        <span class="fw-bold"><?= $row['toa_tyyp'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Toa number</span>
                                        <span class="fw-bold"><?= $row['toa_nr'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Korrus</span>
                                        <span class="fw-bold"><?= $row['toa_korrus'] ?></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Ajad</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Saabumine</span>
                                        <span class="fw-bold"><?= date('d.m.Y', strtotime($row['saabumine'])) ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Lahkumine</span>
                                        <span class="fw-bold"><?= date('d.m.Y', strtotime($row['lahkumine'])) ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Ööde arv</span>
                                        <span class="fw-bold">
                                            <?= date_diff(
                                                new DateTime($row['saabumine']), 
                                                new DateTime($row['lahkumine'])
                                            )->format('%a') ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Staatus</span>
                                        <span class="badge rounded-pill 
                                            <?= $row['staatus'] == 'kinnitatud' ? 'bg-success' : '' ?>
                                            <?= $row['staatus'] == 'ootel' ? 'bg-warning text-dark' : '' ?>
                                            <?= $row['staatus'] == 'tühistatud' ? 'bg-secondary' : '' ?>
                                            <?= $row['staatus'] == 'lõpetatud' ? 'bg-info' : '' ?>
                                        ">
                                            <?= ucfirst($row['staatus']) ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="mb-3">Kliendi andmed</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Nimi</span>
                                        <span class="fw-bold"><?= $row['eesnimi'] ?> <?= $row['perenimi'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Telefon</span>
                                        <span class="fw-bold"><?= $row['telefon'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>E-post</span>
                                        <span class="fw-bold"><?= $row['email'] ?? '-' ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Isikukood</span>
                                        <span class="fw-bold"><?= $row['isikukood'] ?? '-' ?></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Makseinfo</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Makse staatus</span>
                                        <span class="badge rounded-pill 
                                            <?= $row['makse_staatus'] == 'tehtud' ? 'bg-success' : '' ?>
                                            <?= $row['makse_staatus'] == 'ootel' ? 'bg-warning text-dark' : '' ?>
                                            <?= $row['makse_staatus'] == 'tühistatud' ? 'bg-secondary' : '' ?>
                                        ">
                                            <?= ucfirst($row['makse_staatus'] ?? 'ootel') ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Makseviis</span>
                                        <span class="fw-bold"><?= $row['makseviis'] ?? '-' ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Summa</span>
                                        <span class="fw-bold text-success"><?= $row['summa'] ? number_format($row['summa'], 2) . ' €' : '-' ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Makse tähtaeg</span>
                                        <span class="fw-bold"><?= $row['tahtaeg'] ? date('d.m.Y', strtotime($row['tahtaeg'])) : '-' ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <h5 class="mb-3">Toa kirjeldus</h5>
                        <div class="card mb-4">
                            <div class="card-body">
                                <?= nl2br($row['toa_kirjeldus']) ?>
                            </div>
                        </div>
                        
                        <h5 class="mb-3">Broneeringu teenused</h5>
                        <?php
                        $teenused_sql = "SELECT 
                            teenused.teenus,
                            teenused.hind,
                            broneeringu_teenused.kogus,
                            (teenused.hind * broneeringu_teenused.kogus) AS summa
                        FROM broneeringu_teenused
                        JOIN teenused ON broneeringu_teenused.teenus_id = teenused.id
                        WHERE broneeringu_teenused.broneering_id = $broneering_id";
                        
                        $teenused_tulemus = mysqli_query($yhendus, $teenused_sql);
                        if ($teenused_tulemus && mysqli_num_rows($teenused_tulemus) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Teenus</th>
                                            <th class="text-end">Hind</th>
                                            <th class="text-end">Kogus</th>
                                            <th class="text-end">Summa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($teenus = mysqli_fetch_assoc($teenused_tulemus)): ?>
                                            <tr>
                                                <td><?= $teenus['teenus'] ?></td>
                                                <td class="text-end"><?= number_format($teenus['hind'], 2) ?> €</td>
                                                <td class="text-end"><?= $teenus['kogus'] ?></td>
                                                <td class="text-end"><?= number_format($teenus['summa'], 2) ?> €</td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Broneeringul pole lisatud teenuseid</div>
                        <?php endif; ?>
                        <?php
                    } else {
                        echo '<div class="alert alert-danger">Broneeringut ei leitud</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger">Broneeringu ID puudub</div>';
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulge</button>
            </div>
        </div>
    </div>
</div>

<?php if (admin() || tootaja()): ?>
<div class="modal fade" id="muudaBroneering" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Broneeringu muutmine</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Sulge"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <?php
                    if (isset($_GET['muuda_id'])) {
                        $broneering_id = intval($_GET['muuda_id']);
                        $sql = "SELECT 
                            broneeringud.*,
                            toa_tyyp.toa_tyyp,
                            toa_tyyp.id AS toa_tyyp_id,
                            toad.toa_nr,
                            COALESCE(kliendid.eesnimi, kylalised.eesnimi) AS eesnimi,
                            COALESCE(kliendid.perenimi, kylalised.perenimi) AS perenimi,
                            COALESCE(kliendid.telefon, kylalised.telefon) AS telefon,
                            COALESCE(kliendid.isikukood, kylalised.isikukood) AS isikukood,
                            kylalised.email,
                            CASE 
                                WHEN kliendid.id IS NOT NULL THEN 'Klient'
                                ELSE 'Külaline'
                            END AS kasutaja_tyyp
                        FROM broneeringud
                        JOIN toad ON broneeringud.toa_id = toad.id
                        JOIN toa_tyyp ON toad.toa_id = toa_tyyp.id
                        LEFT JOIN kliendid ON broneeringud.klient_id = kliendid.id
                        LEFT JOIN kylalised ON broneeringud.kylaline_id = kylalised.id
                        WHERE broneeringud.id = $broneering_id";
                        
                        $tulemus = mysqli_query($yhendus, $sql);
                        if ($tulemus && mysqli_num_rows($tulemus) > 0) {
                            $row = mysqli_fetch_assoc($tulemus);
                            ?>
                            <input type="hidden" name="broneering_id" value="<?= $row['id'] ?>">
                            
                            <!-- Kliendi info -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Kliendi info</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Kasutaja tüüp</label>
                                            <input type="text" class="form-control" value="<?= $row['kasutaja_tyyp'] ?>" readonly>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Eesnimi</label>
                                            <input type="text" class="form-control" value="<?= $row['eesnimi'] ?>" readonly>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Perenimi</label>
                                            <input type="text" class="form-control" value="<?= $row['perenimi'] ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">E-post</label>
                                            <input type="text" class="form-control" value="<?= $row['email'] ?? '-' ?>" readonly>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Telefon</label>
                                            <input type="text" class="form-control" value="<?= $row['telefon'] ?>" readonly>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Isikukood</label>
                                            <input type="text" class="form-control" value="<?= $row['isikukood'] ?? '-' ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Broneeringu info -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Broneeringu andmed</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Broneeringu ID</label>
                                            <input type="text" class="form-control" value="#<?= $row['id'] ?>" readonly>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Loomise kuupäev</label>
                                            <input type="text" class="form-control" value="<?= date('d.m.Y H:i', strtotime($row['loomis_aeg'])) ?>" readonly>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="staatus" class="form-label">Staatus</label>
                                            <select class="form-select" id="staatus" name="staatus">
                                                <option value="ootel" <?= $row['staatus'] == 'ootel' ? 'selected' : '' ?>>Ootel</option>
                                                <option value="kinnitatud" <?= $row['staatus'] == 'kinnitatud' ? 'selected' : '' ?>>Kinnitatud</option>
                                                <option value="tühistatud" <?= $row['staatus'] == 'tühistatud' ? 'selected' : '' ?>>Tühistatud</option>
                                                <option value="lõpetatud" <?= $row['staatus'] == 'lõpetatud' ? 'selected' : '' ?>>Lõpetatud</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="toa_tyyp" class="form-label">Toa tüüp</label>
                                            <select class="form-select" id="toa_tyyp" name="toa_tyyp" required>
                                                <?php
                                                $toatyypid = mysqli_query($yhendus, "SELECT * FROM toa_tyyp");
                                                while ($tyyp = mysqli_fetch_assoc($toatyypid)) {
                                                    $selected = $tyyp['id'] == $row['toa_tyyp_id'] ? 'selected' : '';
                                                    echo "<option value='{$tyyp['id']}' $selected>{$tyyp['toa_tyyp']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="toa_nr" class="form-label">Toa number</label>
                                            <select class="form-select" id="toa_nr" name="toa_nr" required>
                                                <option value="<?= $row['toa_nr'] ?>" selected><?= $row['toa_nr'] ?></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="saabumine" class="form-label">Saabumine</label>
                                            <input type="date" class="form-control" id="saabumine" name="saabumine" 
                                                value="<?= $row['saabumine'] ?>" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="lahkumine" class="form-label">Lahkumine</label>
                                            <input type="date" class="form-control" id="lahkumine" name="lahkumine" 
                                                value="<?= $row['lahkumine'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } else {
                            echo '<div class="alert alert-danger">Broneeringut ei leitud</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger">Broneeringu ID puudub</div>';
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Tühista
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Salvesta muudatused
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>