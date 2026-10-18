<div class="admin-card">
  <h3>Pembayaran Menunggu Verifikasi (<?= count($payments) ?>)</h3>
  <?php if (!$payments): ?>
    <div class="empty-state"><div class="empty-icon"><i class="bi bi-check2-all"></i></div><h3>Semua sudah diverifikasi!</h3></div>
  <?php else: ?>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Order</th><th>Buyer</th><th>Metode</th><th>Jumlah</th><th>Bukti</th><th>Upload</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($payments as $p): ?>
      <tr>
        <td><a href="/admin/orders/<?= (int)$p['order_id'] ?>" class="fw-bold"><?= e($p['order_number']) ?></a></td>
        <td><?= e($p['buyer_name']) ?><br><small class="text-muted"><?= e($p['buyer_phone']) ?></small></td>
        <td><span class="badge badge-secondary"><?= payment_method_label($p['payment_method']) ?></span></td>
        <td class="fw-bold"><?= rupiah($p['amount']) ?></td>
        <td><?php if ($p['proof_image']): ?><a href="<?= upload($p['proof_image']) ?>" target="_blank"><i class="bi bi-eye"></i> Bukti</a><?php endif; ?></td>
        <td class="text-xs"><?= time_ago($p['updated_at']) ?></td>
        <td>
          <button class="btn btn-success btn-sm" onclick="verify(<?= (int)$p['id'] ?>)"><i class="bi bi-check-lg"></i></button>
          <button class="btn btn-danger btn-sm" onclick="rejectP(<?= (int)$p['id'] ?>)"><i class="bi bi-x-lg"></i></button>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</div>
<script>
async function verify(id) {
  if (!confirm('Approve pembayaran ini?')) return;
  const r = await postJSON('/admin/payments/verify/' + id, {});
  if (r.success) { showToast('Diverifikasi', 'success'); setTimeout(() => location.reload(), 500); }
  else showToast(r.message || 'Gagal', 'error');
}
async function rejectP(id) {
  const reason = prompt('Alasan penolakan:');
  if (!reason) return;
  const r = await postJSON('/admin/payments/reject/' + id, { reason });
  if (r.success) { showToast('Ditolak', 'success'); setTimeout(() => location.reload(), 500); }
  else showToast(r.message || 'Gagal', 'error');
}
</script>
