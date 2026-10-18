<?php
$labels = []; $values = [];
foreach ($chartData as $d) { $labels[] = date('d M', strtotime($d['d'])); $values[] = (float) $d['total']; }
// Pad to 7 days
while (count($labels) < 7) { array_unshift($labels, ''); array_unshift($values, 0); }
?>
<div class="stats-grid">
  <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#dce9f8,#93b5e8); color:var(--color-primary-700);"><i class="bi bi-receipt-cutoff"></i></div><div class="stat-label">Pesanan Hari Ini</div><div class="stat-value" data-count="<?= $stats['orders_today'] ?>"><?= $stats['orders_today'] ?></div></div>
  <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#d6f5e1,#7be39a); color:#06743b;"><i class="bi bi-coin"></i></div><div class="stat-label">Revenue Hari Ini</div><div class="stat-value"><?= rupiah($stats['revenue_today']) ?></div></div>
  <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#fff4b8,#ffd60a); color:#8a6d00;"><i class="bi bi-hourglass-split"></i></div><div class="stat-label">Pesanan Tertunda</div><div class="stat-value" data-count="<?= $stats['orders_pending'] ?>"><?= $stats['orders_pending'] ?></div></div>
  <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg,#ffd6d3,#ff8a82); color:#a01b13;"><i class="bi bi-shield-exclamation"></i></div><div class="stat-label">Verifikasi Bayar</div><div class="stat-value" data-count="<?= $stats['payments_to_verify'] ?>"><?= $stats['payments_to_verify'] ?></div></div>
</div>

<div class="stats-grid">
  <div class="stat-card"><div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div><div class="stat-label">Stok Menipis</div><div class="stat-value" data-count="<?= $stats['low_stock'] ?>"><?= $stats['low_stock'] ?></div></div>
  <div class="stat-card"><div class="stat-icon"><i class="bi bi-box-seam"></i></div><div class="stat-label">Total Produk</div><div class="stat-value" data-count="<?= $stats['total_products'] ?>"><?= $stats['total_products'] ?></div></div>
  <div class="stat-card"><div class="stat-icon"><i class="bi bi-people-fill"></i></div><div class="stat-label">Total Pelanggan</div><div class="stat-value" data-count="<?= $stats['total_customers'] ?>"><?= $stats['total_customers'] ?></div></div>
  <div class="stat-card"><div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div><div class="stat-label">Total Revenue</div><div class="stat-value"><?= rupiah($stats['total_revenue']) ?></div></div>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap: 24px;">
  <div class="chart-card">
    <div class="d-flex justify-between align-center mb-2">
      <h3 class="mb-0"><i class="bi bi-graph-up text-primary"></i> Revenue 7 Hari Terakhir</h3>
      <span class="badge badge-primary">Live</span>
    </div>
    <div class="chart-wrap"><canvas id="revenueChart"></canvas></div>
  </div>
  <div class="chart-card">
    <h3 class="mb-2"><i class="bi bi-pie-chart text-primary"></i> Status Pesanan</h3>
    <div class="chart-wrap"><canvas id="statusChart"></canvas></div>
  </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
  <div class="admin-card">
    <h3><i class="bi bi-clock-history text-primary"></i> Pesanan Terbaru</h3>
    <table class="table">
      <thead><tr><th>No. Order</th><th>Buyer</th><th>Total</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($recentOrders as $o): ?>
        <tr>
          <td><a href="/admin/orders/<?= (int)$o['id'] ?>" class="fw-bold"><?= e($o['order_number']) ?></a><div class="text-xs text-muted"><?= time_ago($o['created_at']) ?></div></td>
          <td><?= e($o['buyer_name']) ?></td>
          <td class="fw-bold"><?= rupiah($o['total']) ?></td>
          <td><?= order_status_badge($o['status']) ?></td>
        </tr>
        <?php endforeach; if (!$recentOrders): ?><tr><td colspan="4" class="text-center text-muted">Belum ada pesanan</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="admin-card">
    <h3><i class="bi bi-exclamation-octagon text-danger"></i> Stok Menipis</h3>
    <table class="table">
      <thead><tr><th>Produk / SKU</th><th>Stok</th></tr></thead>
      <tbody>
        <?php foreach ($lowStock as $v): ?>
        <tr>
          <td><div class="fw-medium"><?= e($v['product_name']) ?></div><div class="text-xs text-muted"><?= e($v['sku']) ?></div></td>
          <td><span class="badge badge-danger"><?= (int)$v['stock'] ?> unit</span></td>
        </tr>
        <?php endforeach; if (!$lowStock): ?><tr><td colspan="2" class="text-center text-muted">Semua stok aman ✓</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
(function(){
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const textColor = isDark ? '#aeaeb2' : '#6e6e73';
  const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
  Chart.defaults.color = textColor;
  Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";

  // Revenue line
  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
      labels: <?= json_encode($labels) ?>,
      datasets: [{
        label: 'Revenue',
        data: <?= json_encode($values) ?>,
        borderColor: '#1e4d9b', backgroundColor: 'rgba(30,77,155,0.12)',
        borderWidth: 3, tension: 0.35, fill: true,
        pointBackgroundColor: '#1e4d9b', pointRadius: 5, pointHoverRadius: 7,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => 'Rp ' + Number(c.parsed.y).toLocaleString('id-ID') } } },
      scales: {
        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1) + 'jt' : v.toLocaleString('id-ID')) } },
        x: { grid: { display: false } }
      }
    }
  });

  // Order status donut
  const statusData = <?= json_encode(Database::all("SELECT status, COUNT(*) cnt FROM orders GROUP BY status")) ?>;
  const labels = statusData.map(s => s.status);
  const labelMap = {'pending':'Menunggu','confirmed':'Verifikasi','processing':'Diproses','shipped':'Dikirim','delivered':'Selesai','cancelled':'Batal','refunded':'Refund'};
  const colorMap = {'pending':'#af9201','confirmed':'#2563c4','processing':'#1e4d9b','shipped':'#4a7fd4','delivered':'#048324','cancelled':'#ff453a','refunded':'#8e8e97'};
  new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
      labels: labels.map(l => labelMap[l] || l),
      datasets: [{
        data: statusData.map(s => s.cnt),
        backgroundColor: labels.map(l => colorMap[l] || '#6e6e73'),
        borderWidth: 0, hoverOffset: 10,
      }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } } } }
  });
})();
</script>
