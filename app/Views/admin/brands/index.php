<div class="admin-card">
  <div class="d-flex justify-between align-center mb-3">
    <div></div>
    <a href="/admin/brands/create" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Brand</a>
  </div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>#</th><th>Nama</th><th>Slug</th><th>Featured</th><th>Urutan</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($brands as $b): ?>
        <tr>
          <td><?= (int)$b['id'] ?></td>
          <td class="fw-bold"><?= e($b['name']) ?></td>
          <td class="text-muted text-sm"><?= e($b['slug']) ?></td>
          <td><?= $b['is_featured'] ? '<span class="badge badge-success">Ya</span>' : '<span class="badge badge-secondary">Tidak</span>' ?></td>
          <td><?= $b['sort_order'] ?></td>
          <td>
            <a href="/admin/brands/edit/<?= (int)$b['id'] ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
            <form method="POST" action="/admin/brands/delete/<?= (int)$b['id'] ?>" style="display:inline;" onsubmit="return confirm('Hapus?')"><?= csrf_field() ?><button class="btn btn-outline btn-sm text-danger"><i class="bi bi-trash"></i></button></form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
