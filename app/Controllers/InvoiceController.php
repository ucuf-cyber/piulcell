<?php

class InvoiceController extends Controller {
    public function show($id) {
        $id = (int) $id;
        $order = Database::row("SELECT o.*, u.name AS buyer_name, u.email, u.phone AS buyer_phone FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id=?", [$id]);
        if (!$order) { http_response_code(404); echo "Pesanan tidak ditemukan"; return; }
        // Access: owner of the order, admin, or owner
        $allowed = false;
        if ($this->user) {
            if ((int)$order['user_id'] === (int)$this->user['id']) $allowed = true;
            if (in_array($this->user['role'], ['admin','owner'])) $allowed = true;
        }
        if (!$allowed) { http_response_code(403); echo "Akses ditolak"; return; }

        $items = Database::all("SELECT * FROM order_items WHERE order_id=?", [$id]);
        $payment = Database::row("SELECT * FROM payments WHERE order_id=?", [$id]);
        $settings = $this->shared['settings'];

        // Pure HTML, no layout
        ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice <?= e($order['order_number']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; color: #1d1d1f; background: #f5f5f7; padding: 24px; margin: 0; }
  .toolbar { max-width: 800px; margin: 0 auto 16px; display: flex; gap: 8px; justify-content: flex-end; }
  .btn { padding: 10px 20px; border-radius: 999px; border: 1px solid #d2d2d7; background: white; color: #1d1d1f; cursor: pointer; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
  .btn-primary { background: #1e4d9b; color: white; border-color: #1e4d9b; }
  .invoice { max-width: 800px; margin: 0 auto; background: white; padding: 48px; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
  .header { display: flex; justify-content: space-between; align-items: start; border-bottom: 2px solid #1e4d9b; padding-bottom: 24px; margin-bottom: 24px; }
  .brand { font-size: 28px; font-weight: 800; letter-spacing: -0.02em; color: #1e4d9b; }
  .brand-sub { color: #6e6e73; font-size: 13px; margin-top: 4px; }
  .invoice-meta { text-align: right; }
  .invoice-meta h1 { font-size: 36px; margin: 0; letter-spacing: -0.02em; color: #1d1d1f; }
  .invoice-meta .invo-num { font-size: 14px; color: #6e6e73; margin-top: 4px; font-family: 'SF Mono', Menlo, monospace; }
  .status-badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; margin-top: 8px; }
  .status-paid { background: #d6f5e1; color: #06743b; }
  .status-pending { background: #fff4b8; color: #8a6d00; }
  .status-cancel { background: #ffd6d3; color: #a01b13; }
  .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
  .meta-block h4 { font-size: 11px; color: #6e6e73; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px; font-weight: 600; }
  .meta-block p { margin: 2px 0; font-size: 14px; color: #1d1d1f; }
  .meta-block strong { font-weight: 600; }
  table.items { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 14px; }
  table.items thead { background: #1e4d9b; color: white; }
  table.items th, table.items td { padding: 12px; text-align: left; }
  table.items th { font-size: 12px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
  table.items td { border-bottom: 1px solid #e8e8ed; }
  table.items .text-right { text-align: right; }
  table.items .text-center { text-align: center; }
  .totals { width: 320px; margin-left: auto; }
  .totals .row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
  .totals .row.grand { border-top: 2px solid #1d1d1f; padding-top: 12px; margin-top: 8px; font-size: 18px; font-weight: 700; color: #1e4d9b; }
  .footer { margin-top: 40px; padding-top: 24px; border-top: 1px solid #e8e8ed; text-align: center; color: #6e6e73; font-size: 12px; line-height: 1.7; }
  .footer strong { color: #1d1d1f; }
  .stamp { display: inline-block; margin-top: 24px; padding: 14px 28px; border: 3px solid; border-radius: 8px; font-weight: 800; font-size: 22px; letter-spacing: 2px; transform: rotate(-8deg); opacity: 0.7; }
  .stamp.paid { color: #06743b; border-color: #06743b; }
  .stamp.pending { color: #8a6d00; border-color: #8a6d00; }
  @media print {
    body { background: white; padding: 0; }
    .toolbar { display: none; }
    .invoice { box-shadow: none; max-width: none; padding: 16px; border-radius: 0; }
    @page { margin: 12mm; }
  }
</style>
</head>
<body>
<div class="toolbar">
  <a href="javascript:history.back()" class="btn">← Kembali</a>
  <button onclick="window.print()" class="btn btn-primary">🖨️ Print / Save PDF</button>
</div>

<div class="invoice">
  <div class="header">
    <div>
      <div class="brand">● <?= e($settings['store_name'] ?? 'PIUL CELL') ?></div>
      <div class="brand-sub"><?= e($settings['store_tagline'] ?? '') ?></div>
      <div class="brand-sub" style="margin-top:8px;"><?= e($settings['store_address'] ?? '') ?></div>
      <div class="brand-sub">📧 <?= e($settings['store_email'] ?? '') ?> · 📞 <?= e($settings['store_phone'] ?? '') ?></div>
    </div>
    <div class="invoice-meta">
      <h1>INVOICE</h1>
      <div class="invo-num"><?= e($order['order_number']) ?></div>
      <div class="invo-num">Tanggal: <?= date('d M Y', strtotime($order['created_at'])) ?></div>
      <?php
        $cls = 'status-pending';
        if (in_array($order['status'], ['delivered','shipped','processing'])) $cls = 'status-paid';
        if (in_array($order['status'], ['cancelled','refunded'])) $cls = 'status-cancel';
      ?>
      <div class="status-badge <?= $cls ?>"><?= order_status_label($order['status']) ?></div>
    </div>
  </div>

  <div class="meta-grid">
    <div class="meta-block">
      <h4>Ditagihkan kepada</h4>
      <p><strong><?= e($order['buyer_name']) ?></strong></p>
      <p><?= e($order['email']) ?></p>
      <p><?= e($order['buyer_phone']) ?></p>
    </div>
    <div class="meta-block">
      <h4>Kirim ke</h4>
      <p><strong><?= e($order['ship_name']) ?></strong> · <?= e($order['ship_phone']) ?></p>
      <p><?= e($order['ship_address']) ?></p>
      <p><?= e($order['ship_district']) ?>, <?= e($order['ship_city']) ?></p>
      <p><?= e($order['ship_province']) ?> <?= e($order['ship_postal']) ?></p>
    </div>
    <div class="meta-block">
      <h4>Metode Pembayaran</h4>
      <p><strong><?= payment_method_label($payment['payment_method'] ?? '') ?></strong></p>
      <?php if (!empty($payment['bank_name'])): ?><p><?= e($payment['bank_name']) ?> · <?= e($payment['account_number']) ?></p><?php endif; ?>
      <p>Status: <?= payment_status_label($payment['status'] ?? '') ?></p>
    </div>
    <div class="meta-block">
      <h4>Pengiriman</h4>
      <p><strong><?= e($order['courier']) ?></strong> <?= e($order['courier_service']) ?></p>
      <?php if ($order['tracking_number']): ?><p>Resi: <strong><?= e($order['tracking_number']) ?></strong></p><?php endif; ?>
      <?php if ($order['shipped_at']): ?><p>Dikirim: <?= date('d/m/Y', strtotime($order['shipped_at'])) ?></p><?php endif; ?>
    </div>
  </div>

  <table class="items">
    <thead>
      <tr>
        <th>#</th>
        <th>Produk</th>
        <th class="text-center">Qty</th>
        <th class="text-right">Harga</th>
        <th class="text-right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $i => $it): $combo = json_decode($it['variant_combination'], true); ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><strong><?= e($it['product_name']) ?></strong><br><span style="font-size:12px; color:#6e6e73;"><?= e(implode(' / ', array_values($combo))) ?></span></td>
        <td class="text-center"><?= (int)$it['quantity'] ?></td>
        <td class="text-right"><?= rupiah($it['price']) ?></td>
        <td class="text-right"><strong><?= rupiah($it['subtotal']) ?></strong></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <div class="totals">
    <div class="row"><span>Subtotal</span><span><?= rupiah($order['subtotal']) ?></span></div>
    <div class="row"><span>Ongkos Kirim</span><span><?= rupiah($order['shipping_cost']) ?></span></div>
    <?php if ($order['discount'] > 0): ?>
      <div class="row" style="color:#06743b;"><span>Diskon (<?= e($order['voucher_code']) ?>)</span><span>-<?= rupiah($order['discount']) ?></span></div>
    <?php endif; ?>
    <?php if ($order['tax'] > 0): ?>
      <div class="row"><span>Pajak</span><span><?= rupiah($order['tax']) ?></span></div>
    <?php endif; ?>
    <div class="row grand"><span>TOTAL</span><span><?= rupiah($order['total']) ?></span></div>
  </div>

  <?php if (!empty($order['notes'])): ?>
    <div style="margin-top:32px; padding:16px; background:#f5f5f7; border-radius:8px; font-size:13px;">
      <strong>Catatan:</strong> <?= e($order['notes']) ?>
    </div>
  <?php endif; ?>

  <?php if (in_array($payment['status'] ?? '', ['verified','cod_done'])): ?>
    <div style="text-align:center;"><div class="stamp paid">LUNAS ✓</div></div>
  <?php elseif (in_array($order['status'], ['cancelled','refunded'])): ?>
    <div style="text-align:center;"><div class="stamp" style="color:#a01b13; border-color:#a01b13;">DIBATALKAN</div></div>
  <?php else: ?>
    <div style="text-align:center;"><div class="stamp pending">BELUM LUNAS</div></div>
  <?php endif; ?>

  <div class="footer">
    <p>Terima kasih telah berbelanja di <strong><?= e($settings['store_name'] ?? 'Piul Cell') ?></strong>!</p>
    <p>Pertanyaan? WhatsApp <strong><?= e($settings['store_phone'] ?? '') ?></strong> · <?= e($settings['store_email'] ?? '') ?></p>
    <p style="margin-top:12px; font-size:11px; color:#aeaeb8;">Invoice ini dihasilkan secara digital dan sah tanpa tanda tangan basah.</p>
  </div>
</div>
</body>
</html>
        <?php
    }
}
