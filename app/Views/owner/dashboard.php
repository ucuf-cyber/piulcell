<?php
$growth = $kpi['last_month'] > 0 ? round(($kpi['this_month'] - $kpi['last_month']) / $kpi['last_month'] * 100, 1) : 0;
$chartLabels = []; $chartValues = [];
foreach ($monthlyChart as $m) { $chartLabels[] = $m['ym']; $chartValues[] = (float)$m['total']; }
?>
<div class="stats-grid">
  <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#d6f5e1,#7be39a); color:#06743b;"><i class="bi bi-currency-dollar"></i></div><div class="stat-label">Total Revenue</div><div class="stat-value"><?= rupiah($kpi['total_revenue']) ?></div></div>
  <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#dce9f8,#93b5e8); color:var(--color-primary-700);"><i class="bi bi-receipt"></i></div><div class="stat-label">Total Pesanan</div><div class="stat-value" data-count="<?= $kpi['total_orders'] ?>"><?= number_format($kpi['total_orders']) ?></div></div>
  <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#fff4b8,#ffd60a); color:#8a6d00;"><i class="bi bi-bar-chart-line"></i></div><div class="stat-label">Avg Order Value</div><div class="stat-value"><?= rupiah($kpi['avg_order_value']) ?></div></div>
  <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#ffd6d3,#ff8a82); color:#a01b13;"><i class="bi bi-people-fill"></i></div><div class="stat-label">Pelanggan</div><div class="stat-value" data-count="<?= $kpi['customers'] ?>"><?= number_format($kpi['customers']) ?></div></div>
</div>

<div class="d-grid grid-2 gap-3">
  <div class="chart-card">
    <h3><i class="bi bi-calendar-event text-primary"></i> Pendapatan Bulan Ini</h3>
    <div class="stat-value" style="font-size:36px; margin-bottom:4px;"><?= rupiah($kpi['this_month']) ?></div>
    <div class="stat-delta <?= $growth < 0 ? 'negative' : '' ?>" style="font-size:14px;">
      <i class="bi bi-<?= $growth >= 0 ? 'arrow-up-circle-fill' : 'arrow-down-circle-fill' ?>"></i>
      <?= abs($growth) ?>% vs bulan lalu (<?= rupiah($kpi['last_month']) ?>)
    </div>
  </div>
  <div class="chart-card">
    <h3><i class="bi bi-cash-stack text-primary"></i> Penjualan Hari Ini</h3>
    <div class="stat-value" style="font-size:36px; margin-bottom:4px;"><?= rupiah((float) Database::val("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE(delivered_at)=CURDATE() AND status='delivered'")) ?></div>
    <div class="text-sm text-muted"><?= (int) Database::val("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()") ?> pesanan masuk hari ini</div>
  </div>
</div>

<div class="chart-card">
  <div class="d-flex justify-between align-center mb-2">
    <h3 class="mb-0"><i class="bi bi-graph-up text-primary"></i> Tren Pendapatan 6 Bulan Terakhir</h3>
    <span class="badge badge-primary">Bulanan</span>
  </div>
  <div class="chart-wrap chart-wrap-tall"><canvas id="ownerRevChart"></canvas></div>
</div>

<div class="d-grid grid-2 gap-3">
  <div class="chart-card">
    <h3 class="mb-2"><i class="bi bi-trophy-fill text-warning"></i> Top 5 Produk Terlaris</h3>
    <div class="chart-wrap"><canvas id="topProdChart"></canvas></div>
  </div>
  <div class="chart-card">
    <h3 class="mb-2"><i class="bi bi-pie-chart-fill text-primary"></i> Kontribusi Kategori</h3>
    <div class="chart-wrap"><canvas id="catChart"></canvas></div>
  </div>
</div>

<div class="admin-card">
  <h3><i class="bi bi-list-stars text-primary"></i> Detail Top Produk</h3>
  <table class="table">
    <thead><tr><th>#</th><th>Produk</th><th>Terjual</th><th>Revenue</th><th>Share</th></tr></thead>
    <tbody>
    <?php $totalRev = array_sum(array_column($topProducts, 'revenue')); foreach ($topProducts as $i => $p): $share = $totalRev > 0 ? round($p['revenue']/$totalRev*100, 1) : 0; ?>
      <tr>
        <td class="fw-bold"><?= $i + 1 ?></td>
        <td><div class="d-flex align-center gap-2"><img src="<?= product_image($p['thumbnail']) ?>" style="width:48px;height:48px;object-fit:contain;background:var(--bg-base);border-radius:8px;padding:4px;"><span class="fw-medium"><?= e($p['name']) ?></span></div></td>
        <td><?= (int)$p['sold'] ?> unit</td>
        <td class="fw-bold text-primary"><?= rupiah($p['revenue']) ?></td>
        <td>
          <div style="display:flex; align-items:center; gap:8px;">
            <div style="flex:1; height:8px; background:var(--bg-base); border-radius:4px; overflow:hidden;">
              <div style="width:<?= $share ?>%; height:100%; background:linear-gradient(90deg, var(--color-primary-500), var(--color-primary-300)); border-radius:4px;"></div>
            </div>
            <span class="text-sm fw-medium"><?= $share ?>%</span>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
(function(){
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const textColor = isDark ? '#aeaeb2' : '#6e6e73';
  const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
  Chart.defaults.color = textColor;
  Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";

  // Revenue line 6mo
  new Chart(document.getElementById('ownerRevChart'), {
    type: 'line',
    data: {
      labels: <?= json_encode($chartLabels) ?>,
      datasets: [{
        label: 'Revenue',
        data: <?= json_encode($chartValues) ?>,
        borderColor: '#1e4d9b', backgroundColor: 'rgba(30,77,155,0.12)',
        borderWidth: 3, tension: 0.4, fill: true,
        pointRadius: 6, pointHoverRadius: 9, pointBackgroundColor: '#1e4d9b', pointBorderColor: 'white', pointBorderWidth: 2,
      }]
    },
    options: { responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => 'Rp ' + Number(c.parsed.y).toLocaleString('id-ID') } } },
      scales: { y: { beginAtZero: true, grid: { color: gridColor }, ticks: { callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1) + 'jt' : v >= 1000 ? (v/1000).toFixed(0) + 'rb' : v) } }, x: { grid: { display: false } } }
    }
  });

  // Top products bar
  const tp = <?= json_encode($topProducts) ?>;
  new Chart(document.getElementById('topProdChart'), {
    type: 'bar',
    data: {
      labels: tp.map(p => p.name.length > 18 ? p.name.substr(0, 15) + '…' : p.name),
      datasets: [{
        label: 'Terjual',
        data: tp.map(p => parseInt(p.sold)),
        backgroundColor: ['#1e4d9b','#2563c4','#4a7fd4','#93b5e8','#dce9f8'],
        borderRadius: 8, borderSkipped: false,
      }]
    },
    options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { x: { grid: { color: gridColor } }, y: { grid: { display: false } } }
    }
  });

  // Categories pie
  const cats = <?= json_encode($topCategories) ?>;
  new Chart(document.getElementById('catChart'), {
    type: 'pie',
    data: {
      labels: cats.map(c => c.name),
      datasets: [{ data: cats.map(c => parseFloat(c.revenue)), backgroundColor: ['#1e4d9b','#30d158','#ffd60a','#ff9500','#a020f0'], borderWidth: 0, hoverOffset: 10 }]
    },
    options: { responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } }, tooltip: { callbacks: { label: c => c.label + ': Rp ' + Number(c.parsed).toLocaleString('id-ID') } } }
    }
  });
})();
</script>
