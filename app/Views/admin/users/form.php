<div class="admin-card">
  <form method="POST">
    <?= csrf_field() ?>
    <div class="form-group"><label class="form-label">Nama</label><input class="form-control" name="name" value="<?= e($u['name'] ?? '') ?>" required></div>
    <?php if (!isset($u)): ?><div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div><?php else: ?><div class="form-group"><label class="form-label">Email</label><input class="form-control" value="<?= e($u['email']) ?>" disabled></div><?php endif; ?>
    <div class="form-group"><label class="form-label">No. HP</label><input class="form-control" name="phone" value="<?= e($u['phone'] ?? '') ?>"></div>
    <div class="form-group"><label class="form-label">Password <?= isset($u) ? '(kosongkan untuk tidak ganti)' : '' ?></label><input type="password" class="form-control" name="password" <?= isset($u) ? '' : 'required' ?>></div>
    <?php if ($user['role']==='owner'): ?>
    <div class="form-group">
      <label class="form-label">Role</label>
      <select name="role" class="form-control">
        <option value="admin" <?= ($u['role'] ?? '')==='admin'?'selected':'' ?>>Admin</option>
        <option value="owner" <?= ($u['role'] ?? '')==='owner'?'selected':'' ?>>Owner</option>
      </select>
    </div>
    <?php endif; ?>
    <?php if (isset($u)): ?><label><input type="checkbox" name="is_active" value="1" <?= !empty($u['is_active'])?'checked':'' ?>> Aktif</label><?php endif; ?>
    <div class="mt-2"><button class="btn btn-primary">Simpan</button> <a href="/admin/users" class="btn btn-ghost">Batal</a></div>
  </form>
</div>
