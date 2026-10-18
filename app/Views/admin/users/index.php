<div class="admin-card">
  <div class="d-flex justify-between align-center mb-3"><div></div><?php if ($user['role']==='owner'): ?><a href="/admin/users/create" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah</a><?php endif; ?></div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td class="fw-bold"><?= e($u['name']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><span class="badge badge-<?= $u['role']==='owner'?'primary':'secondary' ?>"><?= e($u['role']) ?></span></td>
        <td><?= $u['is_active']?'<span class="badge badge-success">Aktif</span>':'<span class="badge badge-secondary">Nonaktif</span>' ?></td>
        <td>
          <a href="/admin/users/edit/<?= (int)$u['id'] ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
          <?php if ($user['role']==='owner' && $u['role']!=='owner'): ?>
            <form method="POST" action="/admin/users/delete/<?= (int)$u['id'] ?>" style="display:inline;" onsubmit="return confirm('Hapus?')"><?= csrf_field() ?><button class="btn btn-outline btn-sm text-danger"><i class="bi bi-trash"></i></button></form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
