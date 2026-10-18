<aside style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 20px; height: fit-content;">
  <div style="text-align:center; padding-bottom: 16px; border-bottom: 1px solid var(--border-color); margin-bottom: 16px;">
    <div style="width:64px; height:64px; border-radius:50%; background: var(--color-primary-500); color:white; display:inline-flex; align-items:center; justify-content:center; font-size: var(--text-xl); font-weight: 700;"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></div>
    <div class="fw-bold mt-1"><?= e($user['name']) ?></div>
    <div class="text-xs text-muted"><?= e($user['email']) ?></div>
  </div>
  <nav style="display: flex; flex-direction: column; gap: 4px;">
    <a href="/akun" class="<?= ($tab ?? '')==='profile'?'btn-primary':'btn-ghost' ?> btn" style="justify-content:flex-start;"><i class="bi bi-person"></i> Profil</a>
    <a href="/akun/alamat" class="<?= ($tab ?? '')==='addresses'?'btn-primary':'btn-ghost' ?> btn" style="justify-content:flex-start;"><i class="bi bi-geo-alt"></i> Alamat</a>
    <a href="/akun/pesanan" class="<?= ($tab ?? '')==='orders'?'btn-primary':'btn-ghost' ?> btn" style="justify-content:flex-start;"><i class="bi bi-bag-check"></i> Pesanan</a>
    <a href="/akun/wishlist" class="<?= ($tab ?? '')==='wishlist'?'btn-primary':'btn-ghost' ?> btn" style="justify-content:flex-start;"><i class="bi bi-heart"></i> Wishlist</a>
    <a href="/akun/notifikasi" class="<?= ($tab ?? '')==='notif'?'btn-primary':'btn-ghost' ?> btn" style="justify-content:flex-start;"><i class="bi bi-bell"></i> Notifikasi</a>
    <a href="/akun/ganti-password" class="<?= ($tab ?? '')==='password'?'btn-primary':'btn-ghost' ?> btn" style="justify-content:flex-start;"><i class="bi bi-key"></i> Ganti Password</a>
    <hr style="border:none; border-top:1px solid var(--border-color); margin: 8px 0;">
    <a href="/logout" class="btn btn-ghost text-danger" style="justify-content:flex-start;"><i class="bi bi-box-arrow-right"></i> Keluar</a>
  </nav>
</aside>
