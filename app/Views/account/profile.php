<div class="container" style="padding: 32px 0;">
  <div style="display:grid; grid-template-columns: 260px 1fr; gap: 32px;">
    <?php include APP_PATH . '/Views/partials/account_sidebar.php'; ?>
    <div class="admin-card">
      <h2>Profil Saya</h2>
      <form method="POST" action="/akun/update">
        <?= csrf_field() ?>
        <div class="form-group"><label class="form-label">Nama</label><input class="form-control" name="name" value="<?= e($user['name']) ?>" required></div>
        <div class="form-group"><label class="form-label">Email</label><input class="form-control" value="<?= e($user['email']) ?>" disabled></div>
        <div class="form-group"><label class="form-label">No. HP</label><input class="form-control" name="phone" value="<?= e($user['phone']) ?>"></div>
        <button class="btn btn-primary">Simpan Perubahan</button>
      </form>
    </div>
  </div>
</div>
