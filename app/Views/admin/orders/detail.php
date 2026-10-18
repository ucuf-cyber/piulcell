<a href="/admin/orders" class="text-sm">← Kembali ke daftar pesanan</a>
<div class="d-flex justify-between align-center mt-2 mb-3">
  <div>
    <h2 class="mb-0">Order <?= e($order['order_number']) ?></h2>
    <div class="text-sm text-muted"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></div>
  </div>
  <div class="d-flex gap-1 align-center">
    <a href="/invoice/<?= (int)$order['id'] ?>" target="_blank" class="btn btn-outline btn-sm"><i class="bi bi-printer"></i> Cetak Invoice</a>
    <?= order_status_badge($order['status']) ?>
  </div>
</div>

<div class="d-grid grid-2 gap-3">
  <div class="admin-card">
    <h4>Pembeli</h4>
    <div class="fw-bold"><?= e($order['buyer_name']) ?></div>
    <div class="text-sm"><?= e($order['email']) ?> · <?= e($order['buyer_phone']) ?></div>
    <hr style="border:none; border-top:1px solid var(--border-color); margin:12px 0;">
    <h4>Alamat Pengiriman</h4>
    <div><?= e($order['ship_name']) ?> · <?= e($order['ship_phone']) ?></div>
    <div class="text-sm text-muted"><?= e($order['ship_address']) ?>, <?= e($order['ship_district']) ?>, <?= e($order['ship_city']) ?>, <?= e($order['ship_province']) ?> <?= e($order['ship_postal']) ?></div>
  </div>
  <div class="admin-card">
    <h4>Pembayaran</h4>
    <div><?= payment_method_label($payment['payment_method'] ?? '') ?></div>
    <div class="text-sm">Status: <strong><?= payment_status_label($payment['status'] ?? '') ?></strong></div>
    <?php if ($payment && $payment['proof_image']): ?>
      <a href="<?= upload($payment['proof_image']) ?>" target="_blank" class="btn btn-outline btn-sm mt-2"><i class="bi bi-eye"></i> Lihat Bukti</a>
    <?php endif; ?>
    <hr style="border:none; border-top:1px solid var(--border-color); margin:12px 0;">
    <h4>Update Status</h4>
    <form method="POST" action="/admin/orders/update-status/<?= (int)$order['id'] ?>">
      <?= csrf_field() ?>
      <select name="status" class="form-control mb-1">
        <?php foreach (['pending','confirmed','processing','shipped','delivered','cancelled','refunded'] as $s): ?>
          <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= order_status_label($s) ?></option>
        <?php endforeach; ?>
      </select>
      <input name="note" class="form-control mb-1" placeholder="Catatan (opsional)">
      <button class="btn btn-primary btn-sm btn-block">Update Status</button>
    </form>
    <hr style="border:none; border-top:1px solid var(--border-color); margin:12px 0;">
    <h4>No. Resi Pengiriman</h4>
    <form method="POST" action="/admin/orders/tracking/<?= (int)$order['id'] ?>">
      <?= csrf_field() ?>
      <input name="courier_service" class="form-control mb-1" placeholder="Layanan (REG/YES)" value="<?= e($order['courier_service']) ?>">
      <input name="tracking_number" class="form-control mb-1" placeholder="Nomor resi" value="<?= e($order['tracking_number']) ?>">
      <button class="btn btn-outline btn-sm btn-block">Simpan</button>
    </form>
  </div>
</div>

<div class="admin-card">
  <h4>Item Pesanan</h4>
  <table class="table">
    <thead><tr><th>Produk</th><th>Varian</th><th>Qty</th><th>Subtotal</th></tr></thead>
    <tbody>
      <?php foreach ($items as $it): $c = json_decode($it['variant_combination'], true); ?>
      <tr>
        <td><div class="d-flex gap-2 align-center"><img src="<?= product_image($it['image']) ?>" style="width:40px;height:40px;object-fit:contain;background:var(--bg-base);border-radius:6px;"><div><?= e($it['product_name']) ?></div></div></td>
        <td class="text-sm"><?= e(implode(' / ', array_values($c))) ?></td>
        <td><?= (int)$it['quantity'] ?></td>
        <td><?= rupiah($it['subtotal']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <hr style="border:none; border-top:1px solid var(--border-color);">
  <div class="summary-row"><span>Subtotal</span><span class="summary-value"><?= rupiah($order['subtotal']) ?></span></div>
  <div class="summary-row"><span>Ongkir (<?= e($order['courier']) ?>)</span><span class="summary-value"><?= rupiah($order['shipping_cost']) ?></span></div>
  <?php if ($order['discount'] > 0): ?><div class="summary-row"><span>Diskon (<?= e($order['voucher_code']) ?>)</span><span class="summary-value text-success">-<?= rupiah($order['discount']) ?></span></div><?php endif; ?>
  <div class="summary-row total"><span>Total</span><span class="summary-value"><?= rupiah($order['total']) ?></span></div>
</div>

<div class="admin-card">
  <h4>Riwayat</h4>
  <div class="timeline">
    <?php foreach ($logs as $l): ?>
      <div class="timeline-item">
        <div class="timeline-meta"><?= date('d M Y H:i', strtotime($l['created_at'])) ?></div>
        <div class="timeline-content"><strong><?= order_status_label($l['status']) ?></strong>: <?= e($l['note']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
