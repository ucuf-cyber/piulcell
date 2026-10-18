<div class="admin-card">
  <div class="d-flex justify-between align-center mb-3" style="flex-wrap:wrap; gap:12px;">
    <form method="GET" class="d-flex gap-2 align-center">
      <label class="form-label mb-0"><i class="bi bi-calendar3 text-primary"></i> Periode:</label>
      <select name="period" class="form-control" style="width:180px;" onchange="this.form.submit()">
        <option value="7" <?= $period===7?'selected':'' ?>>7 hari terakhir</option>
        <option value="30" <?= $period===30?'selected':'' ?>>30 hari terakhir</option>
        <option value="90" <?= $period===90?'selected':'' ?>>90 hari terakhir</option>
        <option value="365" <?= $period===365?'selected':'' ?>>1 tahun terakhir</option>
      </select>
    </form>
    <div class="d-flex gap-1">
      <a href="/owner/reports/export-csv?period=<?= $period ?>" class="btn btn-outline"><i class="bi bi-file-earmark-spreadsheet text-success"></i> Excel / CSV</a>
      <a href="/owner/reports/export-pdf?period=<?= $period ?>" target="_blank" class="btn btn-primary"><i class="bi bi-file-earmark-pdf"></i> PDF / Print</a>
    </div>
  </div>
  <div class="stats-grid">
    <div class="stat-card"><div class="stat-icon"><i class="bi bi-receipt"></i></div><div class="stat-label">Total Pesanan</div><div class="stat-value"><?= count($orders) ?></div></div>
    <div class="stat-card"><div class="stat-icon"><i class="bi bi-cash-stack"></i></div><div class="stat-label">Total Revenue</div><div class="stat-value"><?= rupiah(array_sum(array_column(array_filter($orders, fn($o) => $o['status']==='delivered'), 'total'))) ?></div></div>
    <div class="stat-card"><div class="stat-icon"><i class="bi bi-person-plus"></i></div><div class="stat-label">Pelanggan Baru</div><div class="stat-value"><?= $newCustomers ?></div></div>
    <div class="stat-card"><div class="stat-icon"><i class="bi bi-check2-circle"></i></div><div class="stat-label">Selesai</div><div class="stat-value"><?= count(array_filter($orders, fn($o) => $o['status']==='delivered')) ?></div></div>
  </div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>No Order</th><th>Buyer</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
      <tr><td class="fw-bold"><?= e($o['order_number']) ?></td><td><?= e($o['buyer_name']) ?></td><td class="text-sm"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td><td><?= rupiah($o['total']) ?></td><td><?= order_status_badge($o['status']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
