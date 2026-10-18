<div class="admin-card">
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Nama</th><th>Email</th><th>HP</th><th>Pesanan</th><th>Total Belanja</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($customers as $c): ?>
      <tr>
        <td class="fw-bold"><?= e($c['name']) ?></td>
        <td><?= e($c['email']) ?></td>
        <td><?= e($c['phone']) ?></td>
        <td><?= $c['order_count'] ?></td>
        <td><?= rupiah($c['spent']) ?></td>
        <td><?= $c['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Diblokir</span>' ?></td>
        <td><a href="/admin/customers/<?= (int)$c['id'] ?>" class="btn btn-outline btn-sm">Detail</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
