<a href="/admin/customers" class="text-sm">← Kembali</a>
<div class="d-flex justify-between align-center mt-2 mb-3">
  <h2 class="mb-0"><?= e($customer['name']) ?></h2>
  <form method="POST" action="/admin/customers/toggle/<?= (int)$customer['id'] ?>"><?= csrf_field() ?><button class="btn btn-<?= $customer['is_active']?'danger':'success' ?>"><?= $customer['is_active']?'Blokir':'Aktifkan' ?></button></form>
</div>
<div class="d-grid grid-2 gap-3">
  <div class="admin-card">
    <h4>Profil</h4>
    <div><strong>Email:</strong> <?= e($customer['email']) ?></div>
    <div><strong>HP:</strong> <?= e($customer['phone']) ?></div>
    <div><strong>Bergabung:</strong> <?= date('d M Y', strtotime($customer['created_at'])) ?></div>
  </div>
  <div class="admin-card">
    <h4>Alamat (<?= count($addresses) ?>)</h4>
    <?php foreach ($addresses as $a): ?>
      <div class="text-sm mb-1"><strong><?= e($a['label']) ?>:</strong> <?= e($a['recipient']) ?>, <?= e($a['city']) ?></div>
    <?php endforeach; ?>
  </div>
</div>
<div class="admin-card">
  <h4>Pesanan</h4>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>No</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
      <tr><td><a href="/admin/orders/<?= $o['id'] ?>"><?= e($o['order_number']) ?></a></td><td class="text-sm"><?= date('d/m/Y', strtotime($o['created_at'])) ?></td><td><?= rupiah($o['total']) ?></td><td><?= order_status_badge($o['status']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
