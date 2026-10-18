<?php
$labels = []; $revenues = []; $orderCounts = [];
for ($i = 1; $i <= 12; $i++) {
  $found = false;
  foreach ($monthly as $m) { if ((int)$m['m'] === $i) { $revenues[] = (float)$m['revenue']; $orderCounts[] = (int)$m['orders']; $found = true; break; } }
  if (!$found) { $revenues[] = 0; $orderCounts[] = 0; }
  $labels[] = date('M', mktime(0,0,0,$i));
}
?>
<div class="admin-card">
  <div class="d-flex justify-between align-center mb-3" style="flex-wrap:wrap; gap:12px;">
    <form method="GET" class="d-flex gap-2 align-center">
      <label class="form-label mb-0"><i class="bi bi-calendar3 text-primary"></i> Tahun:</label>
      <select name="year" class="form-control" style="width:140px;" onchange="this.form.submit()">
        <?php for ($y = (int)date('Y'); $y >= 2023; $y--): ?>
          <option value="<?= $y ?>" <?= $y===$year?'selected':'' ?>><?= $y ?></option>
        <?php endfor; ?>
      </select>
    </form>
    <a href="/owner/financial/export-csv?year=<?= $year ?>" class="btn btn-outline"><i class="bi bi-file-earmark-spreadsheet text-success"></i> Export Excel / CSV</a>
  </div>
</div>

<div class="chart-card">
  <h3><i class="bi bi-bar-chart-line text-primary"></i> Pendapatan & Pesanan <?= $year ?></h3>
  <div class="chart-wrap chart-wrap-tall"><canvas id="finChart"></canvas></div>
</div>

<div class="admin-card">
  <h3><i class="bi bi-table"></i> Detail Bulanan</h3>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Bulan</th><th>Pesanan</th><th>Revenue</th><th>Ongkir</th><th>Diskon</th><th>Net Revenue</th></tr></thead>
    <tbody>
    <?php $totals = ['orders'=>0,'rev'=>0,'ship'=>0,'disc'=>0]; foreach ($monthly as $m):
      $net = $m['revenue'] - $m['shipping'] - $m['discount'];
      $totals['orders'] += $m['orders']; $totals['rev'] += $m['revenue']; $totals['ship'] += $m['shipping']; $totals['disc'] += $m['discount'];
    ?>
      <tr>
        <td class="fw-bold"><?= date('F', mktime(0,0,0,(int)$m['m'])) ?></td>
        <td><?= number_format($m['orders']) ?></td>
        <td><?= rupiah($m['revenue']) ?></td>
        <td><?= rupiah($m['shipping']) ?></td>
        <td class="text-danger">-<?= rupiah($m['discount']) ?></td>
        <td class="fw-bold text-primary"><?= rupiah($net) ?></td>
      </tr>
    <?php endforeach; ?>
      <tr style="border-top:2px solid var(--color-primary-500); background: var(--bg-base);">
        <td class="fw-bold">TOTAL <?= $year ?></td>
        <td class="fw-bold"><?= number_format($totals['orders']) ?></td>
        <td class="fw-bold"><?= rupiah($totals['rev']) ?></td>
        <td class="fw-bold"><?= rupiah($totals['ship']) ?></td>
        <td class="fw-bold text-danger">-<?= rupiah($totals['disc']) ?></td>
        <td class="fw-bold text-primary"><?= rupiah($totals['rev'] - $totals['ship'] - $totals['disc']) ?></td>
      </tr>
    </tbody>
  </table></div>
</div>

<script>
(function(){
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const textColor = isDark ? '#aeaeb2' : '#6e6e73';
  const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
  Chart.defaults.color = textColor;
  Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";

  new Chart(document.getElementById('finChart'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($labels) ?>,
      datasets: [
        { type: 'bar', label: 'Revenue (Rp)', data: <?= json_encode($revenues) ?>, backgroundColor: 'rgba(30,77,155,0.85)', borderRadius: 8, yAxisID: 'y' },
        { type: 'line', label: 'Pesanan', data: <?= json_encode($orderCounts) ?>, borderColor: '#30d158', backgroundColor: 'rgba(48,209,88,0.15)', borderWidth: 3, tension: 0.4, fill: false, yAxisID: 'y1', pointRadius: 5, pointBackgroundColor: '#30d158' },
      ]
    },
    options: { responsive: true, maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: c => c.dataset.label + ': ' + (c.dataset.yAxisID === 'y' ? 'Rp ' + Number(c.parsed.y).toLocaleString('id-ID') : c.parsed.y) } } },
      scales: {
        y: { beginAtZero: true, position: 'left', grid: { color: gridColor }, ticks: { callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1) + 'jt' : v >= 1000 ? (v/1000).toFixed(0) + 'rb' : v) } },
        y1: { beginAtZero: true, position: 'right', grid: { display: false }, ticks: { stepSize: 1 } },
        x: { grid: { display: false } }
      }
    }
  });
})();
</script>
