<div class="admin-card">
  <h3>Semua Pembayaran</h3>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Order</th><th>Buyer</th><th>Metode</th><th>Jumlah</th><th>Status</th><th>Tanggal</th></tr></thead>
    <tbody>
    <?php foreach ($payments as $p): ?>
      <tr>
        <td><a href="/admin/orders/<?= (int)$p['order_id'] ?>" class="fw-bold"><?= e($p['order_number']) ?></a></td>
        <td><?= e($p['buyer_name']) ?></td>
        <td><span class="badge badge-secondary"><?= payment_method_label($p['payment_method']) ?></span></td>
        <td class="fw-bold"><?= rupiah($p['amount']) ?></td>
        <td><span class="badge badge-<?= in_array($p['status'], ['verified','cod_done']) ? 'success' : ($p['status']==='rejected'?'danger':'warning') ?>"><?= payment_status_label($p['status']) ?></span></td>
        <td class="text-xs"><?= date('d/m H:i', strtotime($p['created_at'])) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
