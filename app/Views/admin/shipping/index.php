<div class="admin-card">
  <div class="d-flex justify-between align-center mb-3"><div></div><a href="/admin/shipping-zones/create" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Zona</a></div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Nama Zona</th><th>Jarak Maks</th><th>Tarif Dasar</th><th>Per Km</th><th>Kurir</th><th>ETD</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($zones as $z): ?>
      <tr>
        <td class="fw-bold"><?= e($z['name']) ?></td>
        <td><?= $z['max_distance'] ? $z['max_distance'].' km' : 'Tanpa batas' ?></td>
        <td><?= rupiah($z['base_price']) ?></td>
        <td><?= rupiah($z['price_per_km']) ?></td>
        <td><?= e($z['courier']) ?></td>
        <td><?= e($z['etd_days']) ?></td>
        <td><?= $z['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">-</span>' ?></td>
        <td>
          <a href="/admin/shipping-zones/edit/<?= (int)$z['id'] ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
          <form method="POST" action="/admin/shipping-zones/delete/<?= (int)$z['id'] ?>" style="display:inline;" onsubmit="return confirm('Hapus?')"><?= csrf_field() ?><button class="btn btn-outline btn-sm text-danger"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
