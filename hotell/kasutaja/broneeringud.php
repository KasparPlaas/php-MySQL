<?php
include '../includes/session.php';

if (!sisse_logitud()) {
    header("Location: ../autentimine/login.php");
    exit();
}

$kasutaja_id = intval($_SESSION['kasutaja_id']);
$kliendi_tulemus = mysqli_query($yhendus, "SELECT id FROM kliendid WHERE kasutaja_id = $kasutaja_id");
$kliendi_andmed = mysqli_fetch_assoc($kliendi_tulemus);
$klient_id = $kliendi_andmed['id'] ?? 0;

$broneeringud = mysqli_query($yhendus, "
    SELECT broneeringud.id, broneeringud.saabumine, broneeringud.lahkumine, broneeringud.staatus,
           toad.toa_nr, toa_tyyp.toa_tyyp
    FROM broneeringud
    JOIN toad ON broneeringud.toa_id = toad.id
    JOIN toa_tyyp ON toad.toa_id = toa_tyyp.id
    WHERE broneeringud.klient_id = $klient_id
    ORDER BY broneeringud.saabumine DESC
");
?>

<!-- MODAL -->
<div class="modal fade" id="broneeringudModal" tabindex="-1" aria-labelledby="broneeringudModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="broneeringudModalLabel">Minu broneeringud</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sulge"></button>
      </div>
      <div class="modal-body">
        <?php if (mysqli_num_rows($broneeringud) > 0): ?>
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Toa tüüp</th>
                <th>Toa number</th>
                <th>Saabumine</th>
                <th>Lahkumine</th>
                <th>Staatus</th>
                <th>Tegevus</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($broneering = mysqli_fetch_assoc($broneeringud)): ?>
                <?php
                  $saabumine = new DateTime($broneering['saabumine']);
                  $tana = new DateTime();
                  $vahe = $tana->diff($saabumine)->days;
                  $tulevikus = $saabumine > $tana;
                ?>
                <tr>
                  <td><?= $broneering['id'] ?></td>
                  <td><?= htmlspecialchars($broneering['toa_tyyp']) ?></td>
                  <td><?= htmlspecialchars($broneering['toa_nr']) ?></td>
                  <td><?= $broneering['saabumine'] ?></td>
                  <td><?= $broneering['lahkumine'] ?></td>
                  <td class="staatus"><?= ucfirst($broneering['staatus']) ?></td>
                  <td class="tegevus">
                    <?php if ($vahe > 3 && $tulevikus && $broneering['staatus'] === 'kinnitatud'): ?>
                      <button class="btn btn-sm btn-danger tuhista-btn" data-id="<?= $broneering['id'] ?>">Tühista</button>
                    <?php else: ?>
                      <span class="text-muted small">Tühistamine keelatud</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>Teil ei ole veel broneeringuid.</p>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulge</button>
      </div>
    </div>
  </div>
</div>

<!-- AJAX Tühistamise skript -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.tuhista-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();

      if (!confirm('Oled kindel, et soovid broneeringu tühistada?')) return;

      const broneeringId = this.dataset.id;
      const row = this.closest('tr');
      const statusCell = row.querySelector('.staatus');
      const actionCell = row.querySelector('.tegevus');

      fetch('tuhista_broneering.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'broneering_id=' + encodeURIComponent(broneeringId)
      })
      .then(response => response.text())
      .then(result => {
        if (result.trim() === 'OK') {
          statusCell.textContent = 'Tühistatud';
          actionCell.innerHTML = '<span class="text-muted small">Tühistatud</span>';
        } else {
          alert('Tühistamine ebaõnnestus: ' + result);
        }
      })
      .catch(err => {
        alert('Midagi läks valesti. Proovi uuesti.');
        console.error(err);
      });
    });
  });
});
</script>
