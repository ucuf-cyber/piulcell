<div class="admin-card">
  <div class="d-flex justify-between align-center mb-3">
    <div></div>
    <a href="/admin/categories/create" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Kategori</a>
  </div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>#</th><th>Nama</th><th>Icon</th><th>Slug</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($cats as $c): ?>
      <tr>
        <td><?= $c['id'] ?></td>
        <td class="fw-bold"><?= e($c['name']) ?></td>
        <td><i class="bi bi-<?= e($c['icon']) ?>"></i> <?= e($c['icon']) ?></td>
        <td class="text-muted text-sm"><?= e($c['slug']) ?></td>
        <td><?= $c['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Tidak</span>' ?></td>
        <td>
          <a href="/admin/categories/edit/<?= (int)$c['id'] ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
          <form method="POST" action="/admin/categories/delete/<?= (int)$c['id'] ?>" style="display:inline;" onsubmit="return confirm('Hapus?')"><?= csrf_field() ?><button class="btn btn-outline btn-sm text-danger"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
