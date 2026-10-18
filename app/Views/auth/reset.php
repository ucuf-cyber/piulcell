<div class="auth-page">
  <div class="auth-card">
    <h2>Reset Password</h2>
    <p class="auth-sub">Masukkan password baru Anda</p>
    <form method="POST" action="/reset-password">
      <?= csrf_field() ?>
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <div class="form-group">
        <label class="form-label">Password Baru</label>
        <input type="password" name="password" class="form-control" required minlength="8">
      </div>
      <div class="form-group">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirm" class="form-control" required minlength="8">
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg">Simpan Password Baru</button>
    </form>
  </div>
</div>
