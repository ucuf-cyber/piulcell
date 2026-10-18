<div class="auth-page">
  <div class="auth-card">
    <h2>Lupa Password</h2>
    <p class="auth-sub">Masukkan email untuk reset password</p>
    <form method="POST" action="/forgot-password">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required autofocus>
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg">Kirim Link Reset</button>
    </form>
    <p class="text-center text-sm mt-3"><a href="/login">← Kembali ke Login</a></p>
  </div>
</div>
