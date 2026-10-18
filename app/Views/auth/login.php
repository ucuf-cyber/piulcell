<div class="auth-page">
  <div class="auth-card">
    <h2>Masuk</h2>
    <p class="auth-sub">Selamat datang kembali di Piul Cell</p>
    <form method="POST" action="/login">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required autofocus placeholder="nama@email.com">
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required placeholder="••••••••">
      </div>
      <div class="d-flex justify-between align-center mb-3 text-sm">
        <label><input type="checkbox" name="remember"> Ingat saya</label>
        <a href="/forgot-password">Lupa password?</a>
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg">Masuk</button>
    </form>
    <div class="auth-divider">— atau —</div>
    <p class="text-center text-sm">Belum punya akun? <a href="/register" class="fw-bold">Daftar di sini</a></p>
    <div style="margin-top: 24px; padding: 16px; background: var(--bg-base); border-radius: var(--radius-md); font-size: var(--text-xs);">
      <strong>Akun Demo:</strong><br>
      Owner: owner@piulcell.com / PiulOwner2024!<br>
      Admin: admin@piulcell.com / PiulAdmin2024!<br>
      Buyer: buyer@piulcell.com / Buyer2024!
    </div>
  </div>
</div>
