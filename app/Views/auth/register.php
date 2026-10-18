<div class="auth-page">
  <div class="auth-card">
    <h2>Daftar Akun</h2>
    <p class="auth-sub">Buat akun untuk mulai berbelanja</p>
    <form method="POST" action="/register">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="name" class="form-control" required autofocus>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">No. HP / WhatsApp</label>
        <input type="text" name="phone" class="form-control" required placeholder="0812xxxxxxxx">
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required minlength="8" placeholder="Minimal 8 karakter">
      </div>
      <div class="form-group">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirm" class="form-control" required minlength="8">
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg">Daftar</button>
    </form>
    <div class="auth-divider">— atau —</div>
    <p class="text-center text-sm">Sudah punya akun? <a href="/login" class="fw-bold">Masuk</a></p>
  </div>
</div>
