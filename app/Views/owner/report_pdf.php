<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Penjualan <?= $days ?> Hari</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  body { font-family: 'Plus Jakarta Sans', sans-serif; color: #1d1d1f; background: #f5f5f7; padding: 24px; margin: 0; }
  .toolbar { max-width: 1100px; margin: 0 auto 16px; display: flex; gap: 8px; justify-content: flex-end; }
  .btn { padding: 10px 20px; border-radius: 999px; border: 1px solid #d2d2d7; background: white; color: #1d1d1f; cursor: pointer; font-weight: 600; font-size: 14px; text-decoration: none; }
  .btn-primary { background: #1e4d9b; color: white; border-color: #1e4d9b; }
  .report { max-width: 1100px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
  h1 { font-family: 'Sora', sans-serif; font-size: 32px; margin: 0 0 4px; letter-spacing: -0.03em; color: #1e4d9b; }
  .meta { color: #6e6e73; font-size: 14px; margin-bottom: 24px; }
  .header { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 3px solid #1e4d9b; padding-bottom: 20px; margin-bottom: 32px; }
  .store-info { text-align: right; font-size: 13px; color: #6e6e73; }
  .store-info strong { display: block; font-size: 18px; color: #1e4d9b; margin-bottom: 4px; }
  .summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px; }
  .summary-box { background: linear-gradient(135deg, #f0f6ff, #dce9f8); border-radius: 12px; padding: 20px; border: 1px solid #93b5e8; }
  .summary-box.success { background: linear-gradient(135deg, #d6f5e1, #7be39a); border-color: #30d158; }
  .summary-box.warning { background: linear-gradient(135deg, #fff4b8, #ffd60a); border-color: #ffd60a; }
  .summary-box .label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; opacity: 0.7; }
  .summary-box .value { font-family: 'Sora', sans-serif; font-size: 24px; font-weight: 800; margin-top: 4px; letter-spacing: -0.02em; }
  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  table thead { background: #1e4d9b; color: white; }
  table th, table td { padding: 10px 12px; text-align: left; }
  table th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
  table td { border-bottom: 1px solid #e8e8ed; }
  table tbody tr:nth-child(even) { background: #f9f9fb; }
  .right { text-align: right; }
  .center { text-align: center; }
  .status-badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; }
  .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e8e8ed; text-align: center; color: #6e6e73; font-size: 11px; }
  @media print {
    body { background: white; padding: 0; }
    .toolbar { display: none; }
    .report { box-shadow: none; max-width: none; padding: 16px; border-radius: 0; }
    @page { margin: 12mm; size: A4 landscape; }
  }
</style>
</head>
<body>
<div class="toolbar">
  <a href="javascript:history.back()" class="btn">← Kembali</a>
  <a href="/owner/reports/export-csv?period=<?= $days ?>" class="btn">📊 Export Excel/CSV</a>
  <button onclick="window.print()" class="btn btn-primary">🖨️ Print / Save PDF</button>
</div>

<div class="report">
  <div class="header">
    <div>
      <h1>LAPORAN PENJUALAN</h1>
      <div class="meta">Periode: <strong><?= $days ?> hari terakhir</strong> · Dibuat: <?= date('d M Y H:i') ?></div>
    </div>
    <div class="store-info">
      <strong>● <?= e($settings['store_name'] ?? 'Piul Cell') ?></strong>
      <?= e($settings['store_address'] ?? '') ?><br>
      <?= e($settings['store_email'] ?? '') ?> · <?= e($settings['store_phone'] ?? '') ?>
    </div>
  </div>

  <div class="summary">
    <div class="summary-box"><div class="label">Total Pesanan</div><div class="value"><?= number_format($totals['orders']) ?></div></div>
    <div class="summary-box success"><div class="label">Total Revenue</div><div class="value"><?= rupiah($totals['revenue']) ?></div></div>
    <div class="summary-box warning"><div class="label">Pelanggan Baru</div><div class="value"><?= number_format($newCustomers) ?></div></div>
    <div class="summary-box"><div class="label">Pesanan Berbayar</div><div class="value"><?= number_format($totals['paid']) ?></div></div>
    <div class="summary-box"><div class="label">Total Ongkir</div><div class="value"><?= rupiah($totals['shipping']) ?></div></div>
    <div class="summary-box"><div class="label">Total Diskon</div><div class="value"><?= rupiah($totals['discount']) ?></div></div>
  </div>

  <h3 style="font-family:'Sora',sans-serif; margin-bottom:12px;">Daftar Pesanan</h3>
  <table>
    <thead>
      <tr>
        <th>#</th><th>No. Order</th><th>Tanggal</th><th>Buyer</th><th>HP</th><th>Kota</th>
        <th class="right">Subtotal</th><th class="right">Ongkir</th><th class="right">Total</th><th class="center">Status</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $i => $o): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><strong><?= e($o['order_number']) ?></strong></td>
        <td><?= date('d/m H:i', strtotime($o['created_at'])) ?></td>
        <td><?= e($o['buyer_name']) ?></td>
        <td><?= e($o['buyer_phone']) ?></td>
        <td><?= e($o['ship_city']) ?></td>
        <td class="right"><?= rupiah($o['subtotal']) ?></td>
        <td class="right"><?= rupiah($o['shipping_cost']) ?></td>
        <td class="right"><strong><?= rupiah($o['total']) ?></strong></td>
        <td class="center"><?= order_status_badge($o['status']) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$orders): ?><tr><td colspan="10" class="center" style="padding:32px;">Tidak ada pesanan dalam periode ini</td></tr><?php endif; ?>
    </tbody>
  </table>

  <div class="footer">
    <p>Laporan ini dihasilkan otomatis oleh sistem Piul Cell.</p>
    <p>© <?= date('Y') ?> <?= e($settings['store_name'] ?? 'Piul Cell') ?> — Confidential</p>
  </div>
</div>
</body>
</html>
