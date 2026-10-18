<div class="admin-card">
  <div class="d-flex gap-1 mb-3" style="flex-wrap:wrap;">
    <a href="/admin/orders" class="btn btn-<?= !$status?'primary':'outline' ?> btn-sm">Semua</a>
    <?php foreach (['pending'=>'Menunggu Bayar','confirmed'=>'Verifikasi','processing'=>'Diproses','shipped'=>'Dikirim','delivered'=>'Selesai','cancelled'=>'Batal'] as $k=>$lbl): ?>
      <a href="/admin/orders?status=<?= $k ?>" class="btn btn-<?= $status===$k?'primary':'outline' ?> btn-sm"><?= $lbl ?></a>
    <?php endforeach; ?>
  </div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>No. Order</th><th>Buyer</th><th>Metode</th><th>Total</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
      <tr>
        <td class="fw-bold"><?= e($o['order_number']) ?></td>
        <td><?= e($o['buyer_name']) ?></td>
        <td><span class="badge badge-secondary"><?= payment_method_label($o['payment_method'] ?? '') ?></span></td>
        <td class="fw-bold"><?= rupiah($o['total']) ?></td>
        <td><?= order_status_badge($o['status']) ?></td>
        <td class="text-xs text-muted"><?= date('d/m H:i', strtotime($o['created_at'])) ?></td>
        <td><a href="/admin/orders/<?= (int)$o['id'] ?>" class="btn btn-outline btn-sm">Detail</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
