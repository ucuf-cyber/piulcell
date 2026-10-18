<div class="container" style="padding: 32px 0;">
  <div style="display:grid; grid-template-columns: 260px 1fr; gap: 32px;">
    <?php include APP_PATH . '/Views/partials/account_sidebar.php'; ?>
    <div>
      <a href="/akun/pesanan" class="text-sm">← Kembali ke pesanan</a>
      <div class="d-flex justify-between align-center mt-2 mb-3">
        <div>
          <h2 class="mb-0">Pesanan <?= e($order['order_number']) ?></h2>
          <div class="text-sm text-muted"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></div>
        </div>
        <?= order_status_badge($order['status']) ?>
      </div>

      <div class="d-grid grid-2 gap-2">
        <div class="admin-card" style="margin:0;">
          <h4>Alamat Pengiriman</h4>
          <div><?= e($order['ship_name']) ?> · <?= e($order['ship_phone']) ?></div>
          <div class="text-sm text-muted"><?= e($order['ship_address']) ?>, <?= e($order['ship_district']) ?>, <?= e($order['ship_city']) ?>, <?= e($order['ship_province']) ?> <?= e($order['ship_postal']) ?></div>
          <hr style="border:none; border-top:1px solid var(--border-color); margin: 12px 0;">
          <div class="text-sm"><strong>Kurir:</strong> <?= e($order['courier']) ?></div>
          <?php if ($order['tracking_number']): ?><div class="text-sm"><strong>No. Resi:</strong> <?= e($order['tracking_number']) ?></div><?php endif; ?>
        </div>
        <div class="admin-card" style="margin:0;">
          <h4>Pembayaran</h4>
          <div><strong><?= payment_method_label($payment['payment_method']) ?></strong></div>
          <div class="text-sm">Status: <strong><?= payment_status_label($payment['status']) ?></strong></div>
          <?php if ($payment['reject_reason']): ?><div class="text-sm text-danger">Ditolak: <?= e($payment['reject_reason']) ?></div><?php endif; ?>
          <?php if (in_array($payment['status'], ['pending','rejected']) && $payment['payment_method'] !== 'cod'): ?>
            <a href="/payment/instructions/<?= (int)$order['id'] ?>" class="btn btn-primary btn-sm mt-2"><i class="bi bi-cloud-upload"></i> Upload Bukti</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="admin-card">
        <h4>Item</h4>
        <table class="table">
          <thead><tr><th>Produk</th><th>Varian</th><th class="text-center">Qty</th><th style="text-align:right;">Subtotal</th></tr></thead>
          <tbody>
          <?php foreach ($items as $it): $combo = json_decode($it['variant_combination'], true); ?>
            <tr>
              <td><div class="d-flex gap-2 align-center"><img src="<?= product_image($it['image']) ?>" alt="" style="width:48px;height:48px;object-fit:contain;border-radius:8px;background:var(--bg-base);"><div><?= e($it['product_name']) ?></div></div></td>
              <td class="text-sm"><?= e(implode(' / ', array_values($combo))) ?></td>
              <td class="text-center"><?= (int)$it['quantity'] ?></td>
              <td style="text-align:right;"><?= rupiah($it['subtotal']) ?></td>
            </tr>
            <?php if ($order['status'] === 'delivered'):
              $hasReview = Database::row("SELECT id FROM reviews WHERE order_item_id=?", [$it['id']]);
              if (!$hasReview): ?>
              <tr><td colspan="4" style="text-align:right;"><a href="/akun/ulasan/<?= (int)$it['id'] ?>" class="btn btn-outline btn-sm">★ Beri Ulasan</a></td></tr>
            <?php endif; endif; ?>
          <?php endforeach; ?>
          </tbody>
        </table>
        <hr style="border:none; border-top:1px solid var(--border-color);">
        <div class="summary-row"><span>Subtotal</span><span class="summary-value"><?= rupiah($order['subtotal']) ?></span></div>
        <div class="summary-row"><span>Ongkir</span><span class="summary-value"><?= rupiah($order['shipping_cost']) ?></span></div>
        <?php if ($order['discount'] > 0): ?><div class="summary-row"><span>Diskon</span><span class="summary-value text-success">-<?= rupiah($order['discount']) ?></span></div><?php endif; ?>
        <div class="summary-row total"><span>Total</span><span class="summary-value"><?= rupiah($order['total']) ?></span></div>
      </div>

      <?php if (in_array($order['status'], ['pending','confirmed'])): ?>
        <form method="POST" action="/akun/pesanan/batal/<?= (int)$order['id'] ?>" onsubmit="return confirm('Yakin batalkan pesanan?')"><?= csrf_field() ?><button class="btn btn-danger">Batalkan Pesanan</button></form>
      <?php endif; ?>
      <?php if ($order['status'] === 'shipped'): ?>
        <form method="POST" action="/akun/pesanan/terima/<?= (int)$order['id'] ?>"><?= csrf_field() ?><button class="btn btn-success"><i class="bi bi-check-lg"></i> Konfirmasi Pesanan Diterima</button></form>
      <?php endif; ?>
      <a href="/invoice/<?= (int)$order['id'] ?>" target="_blank" class="btn btn-outline mt-2"><i class="bi bi-printer"></i> Cetak Invoice / Save PDF</a>

      <div class="admin-card">
        <h4>Riwayat Status</h4>
        <div class="timeline">
          <?php foreach ($logs as $l): ?>
            <div class="timeline-item">
              <div class="timeline-meta"><?= date('d M Y H:i', strtotime($l['created_at'])) ?></div>
              <div class="timeline-content"><strong><?= order_status_label($l['status']) ?></strong>: <?= e($l['note']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>
