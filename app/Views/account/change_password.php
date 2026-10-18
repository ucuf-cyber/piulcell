<div class="container" style="padding: 32px 0;">
  <div style="display:grid; grid-template-columns: 260px 1fr; gap: 32px;">
    <?php include APP_PATH . '/Views/partials/account_sidebar.php'; ?>
    <div class="admin-card">
      <h2>Ganti Password</h2>
      <form method="POST" action="/akun/ganti-password" style="max-width:400px;">
        <?= csrf_field() ?>
        <div class="form-group"><label class="form-label">Password Lama</label><input type="password" class="form-control" name="old_password" required></div>
        <div class="form-group"><label class="form-label">Password Baru</label><input type="password" class="form-control" name="new_password" required minlength="8"></div>
        <div class="form-group"><label class="form-label">Konfirmasi Password Baru</label><input type="password" class="form-control" name="new_password_confirm" required minlength="8"></div>
        <button class="btn btn-primary">Ubah Password</button>
      </form>
    </div>
  </div>
</div>
