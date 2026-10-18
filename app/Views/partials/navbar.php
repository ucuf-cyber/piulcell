<nav class="navbar" role="navigation" aria-label="Navigasi utama">
  <div class="container">
    <div class="navbar-inner">
      <a href="/" class="navbar-brand"><span class="logo-dot"></span> PIUL CELL</a>

      <div class="navbar-links hide-md">
        <a href="/" class="<?= active_when('/', 'exact') ?>">Beranda</a>
        <a href="/katalog" class="<?= active_when('/katalog') ?>">Produk</a>
        <a href="/katalog?promo=1">Promo</a>
        <a href="/page/tentang-kami">Tentang</a>
        <a href="/page/hubungi-kami">Kontak</a>
      </div>

      <div class="navbar-actions" id="searchWrap" style="position:relative;">
        <div class="hide-sm" style="position:relative;">
          <input id="searchInput" type="text" class="form-control" placeholder="Cari HP atau aksesoris…" style="width:280px; height:40px; border-radius:999px; padding-left:40px;">
          <i class="bi bi-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-tertiary);"></i>
          <div id="searchSuggest"></div>
        </div>
        <button class="nav-icon-btn hide-sm" id="themeToggle" onclick="toggleTheme()" title="Mode gelap/terang"><i class="bi bi-moon"></i></button>
        <a href="/akun/wishlist" class="nav-icon-btn" title="Wishlist">
          <i class="bi bi-heart"></i>
          <?php if (($wishlist_count ?? 0) > 0): ?><span class="badge-count"><?= $wishlist_count ?></span><?php endif; ?>
        </a>
        <a href="/cart" class="nav-icon-btn" title="Keranjang">
          <i class="bi bi-bag"></i>
          <span class="badge-count" id="cartCount" style="<?= ($cart_count ?? 0) > 0 ? '' : 'display:none' ?>"><?= $cart_count ?? 0 ?></span>
        </a>
        <?php if (!empty($user)): ?>
          <div class="user-dropdown">
            <button class="nav-icon-btn" onclick="toggleUserMenu(event)" title="Akun"><i class="bi bi-person-circle"></i></button>
            <div class="user-menu" id="userMenu">
              <div style="padding:10px 16px; border-bottom:1px solid var(--border-color);">
                <div class="fw-bold"><?= e($user['name']) ?></div>
                <div class="text-xs text-muted"><?= e($user['email']) ?></div>
              </div>
              <a href="/akun"><i class="bi bi-person"></i> Profil Saya</a>
              <a href="/akun/pesanan"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
              <a href="/akun/wishlist"><i class="bi bi-heart"></i> Wishlist</a>
              <a href="/akun/alamat"><i class="bi bi-geo-alt"></i> Alamat</a>
              <a href="/akun/notifikasi"><i class="bi bi-bell"></i> Notifikasi <?php if (($notif_unread ?? 0) > 0): ?><span class="badge badge-primary"><?= $notif_unread ?></span><?php endif; ?></a>
              <hr>
              <?php if (in_array($user['role'], ['admin','owner'])): ?>
                <a href="/admin"><i class="bi bi-speedometer2"></i> Panel Admin</a>
              <?php endif; ?>
              <?php if ($user['role'] === 'owner'): ?>
                <a href="/owner"><i class="bi bi-graph-up"></i> Panel Owner</a>
              <?php endif; ?>
              <a href="/logout"><i class="bi bi-box-arrow-right"></i> Keluar</a>
            </div>
          </div>
        <?php else: ?>
          <a href="/login" class="btn btn-outline btn-sm hide-sm">Masuk</a>
          <a href="/register" class="btn btn-primary btn-sm hide-sm">Daftar</a>
        <?php endif; ?>
        <button class="nav-icon-btn mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Buka menu" aria-controls="mobileMenu" aria-expanded="false"><i class="bi bi-list"></i></button>
      </div>
    </div>
  </div>
</nav>

<div class="mobile-menu" id="mobileMenu" aria-hidden="true">
  <div class="mobile-menu-header">
    <button class="nav-icon-btn mobile-menu-close" onclick="toggleMobileMenu()" aria-label="Tutup menu"><i class="bi bi-x-lg"></i></button>
  </div>
  <a href="/">Beranda</a>
  <a href="/katalog">Produk</a>
  <a href="/katalog?promo=1">Promo</a>
  <a href="/page/tentang-kami">Tentang</a>
  <a href="/page/hubungi-kami">Kontak</a>
  <?php if (empty($user)): ?>
    <a href="/login">Masuk</a>
    <a href="/register">Daftar</a>
  <?php else: ?>
    <a href="/akun">Akun Saya</a>
    <a href="/akun/pesanan">Pesanan</a>
    <a href="/logout">Keluar</a>
  <?php endif; ?>
</div>
