<div class="admin-card">
  <div class="d-flex justify-between align-center mb-3"><div></div><a href="/admin/vouchers/create" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Voucher</a></div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Kode</th><th>Nama</th><th>Tipe</th><th>Nilai</th><th>Min Belanja</th><th>Quota</th><th>Periode</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($list as $v): ?>
      <tr>
        <td class="fw-bold"><?= e($v['code']) ?></td>
        <td><?= e($v['name']) ?></td>
        <td><span class="badge badge-secondary"><?= e($v['type']) ?></span></td>
        <td><?= $v['type']==='percentage' ? $v['value'].'%' : ($v['type']==='free_shipping' ? 'Gratis Ongkir' : rupiah($v['value'])) ?></td>
        <td><?= rupiah($v['min_purchase']) ?></td>
        <td><?= $v['quota'] ? $v['used_count'].'/'.$v['quota'] : '∞' ?></td>
        <td class="text-xs"><?= e($v['start_date']) ?> → <?= e($v['end_date']) ?></td>
        <td><?= $v['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">-</span>' ?></td>
        <td>
          <a href="/admin/vouchers/edit/<?= (int)$v['id'] ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
          <form method="POST" action="/admin/vouchers/delete/<?= (int)$v['id'] ?>" style="display:inline;" onsubmit="return confirm('Hapus?')"><?= csrf_field() ?><button class="btn btn-outline btn-sm text-danger"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
