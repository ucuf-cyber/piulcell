<?php /** @var array $banners, $brands, $featured, $newest, $bestSeller, $categories */ ?>
<!-- HERO -->
<section class="hero">
  <div class="container hero-slider">
    <?php foreach ($banners as $i => $b): if ($b['position'] !== 'hero') continue; ?>
      <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>">
        <div class="hero-content">
          <div class="hero-text">
            <div style="color: var(--color-primary-200); font-size: var(--text-sm); font-weight: 600; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 16px;">★ Pilihan terbaik</div>
            <h1><?= e($b['title']) ?></h1>
            <p><?= e($b['subtitle']) ?></p>
            <?php if ($b['link_url']): ?>
              <a href="<?= e($b['link_url']) ?>" class="btn btn-light btn-lg"><?= e($b['link_text'] ?: 'Lihat Detail') ?> <i class="bi bi-arrow-right"></i></a>
            <?php endif; ?>
          </div>
          <div class="hero-image">
            <img src="<?= product_image($b['image_desktop'], 'banners') ?>" alt="<?= e($b['title']) ?>">
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <div class="hero-dots">
      <?php $heroBanners = array_values(array_filter($banners, fn($x) => $x['position'] === 'hero'));
      foreach ($heroBanners as $i => $b): ?>
        <button class="hero-dot <?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i+1 ?>"></button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- BRANDS -->
<section class="section-sm">
  <div class="container">
    <div class="section-header">
      <div>
        <h2>Brand Terpercaya</h2>
        <p class="section-subtitle">Pilihan brand smartphone ternama</p>
      </div>
    </div>
    <div class="brand-strip">
      <?php foreach ($brands as $b): ?>
        <a href="/katalog?brand=<?= e($b['slug']) ?>" class="brand-chip">
          <div class="brand-icon"><i class="bi bi-phone"></i></div>
          <span><?= e($b['name']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FEATURED -->
<?php if ($featured): ?>
<section class="section">
  <div class="container">
    <div class="section-header">
      <div>
        <h2 class="section-title">Produk Unggulan</h2>
        <p class="section-subtitle">Pilihan favorit pelanggan Piul Cell</p>
      </div>
      <a href="/katalog" class="section-link">Lihat semua <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="product-grid">
      <?php foreach ($featured as $p) include APP_PATH . '/Views/partials/product_card.php'; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- NEWEST -->
<?php if ($newest): ?>
<section class="section" style="background: var(--bg-surface);">
  <div class="container">
    <div class="section-header">
      <div>
        <h2 class="section-title">Baru Datang</h2>
        <p class="section-subtitle">Smartphone terbaru di etalase</p>
      </div>
      <a href="/katalog" class="section-link">Lihat semua <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="product-grid">
      <?php foreach (array_slice($newest, 0, 5) as $p) include APP_PATH . '/Views/partials/product_card.php'; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- PROMO BANNER -->
<?php $promo = array_values(array_filter($banners, fn($x) => $x['position'] === 'promo'))[0] ?? null; ?>
<?php if ($promo): ?>
<section class="section-sm">
  <div class="container">
    <a href="<?= e($promo['link_url'] ?: '/katalog?promo=1') ?>" style="display:block; border-radius: var(--radius-xl); overflow:hidden; position:relative; background: linear-gradient(135deg, var(--color-primary-700), var(--color-primary-500)); padding: 48px; color:white;">
      <div style="display:grid; grid-template-columns: 1fr auto; align-items:center; gap:24px;">
        <div>
          <div style="font-size: var(--text-sm); letter-spacing:2px; text-transform:uppercase; opacity:0.8; margin-bottom:8px;">Penawaran Spesial</div>
          <h2 style="font-size: var(--text-4xl); color:white; margin-bottom:8px;"><?= e($promo['title']) ?></h2>
          <p style="opacity:0.9; font-size: var(--text-lg);"><?= e($promo['subtitle']) ?></p>
        </div>
        <div><span class="btn btn-light btn-lg"><?= e($promo['link_text'] ?: 'Lihat Promo') ?> <i class="bi bi-arrow-right"></i></span></div>
      </div>
    </a>
  </div>
</section>
<?php endif; ?>

<!-- BEST SELLER -->
<?php if ($bestSeller): ?>
<section class="section">
  <div class="container">
    <div class="section-header">
      <div>
        <h2 class="section-title">Paling Laris</h2>
        <p class="section-subtitle">Pilihan terlaris bulan ini</p>
      </div>
      <a href="/katalog?sort=popular" class="section-link">Lihat semua <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="product-grid">
      <?php foreach (array_slice($bestSeller, 0, 5) as $p) include APP_PATH . '/Views/partials/product_card.php'; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CATEGORIES -->
<section class="section" style="background: var(--bg-surface);">
  <div class="container">
    <div class="section-header"><h2 class="section-title">Jelajahi Kategori</h2></div>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
      <?php foreach ($categories as $cat): ?>
        <a href="/katalog?kategori=<?= e($cat['slug']) ?>" style="display:block; padding:32px 24px; background: var(--bg-card); border:1px solid var(--border-color); border-radius: var(--radius-lg); text-align:center; transition:all 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='var(--color-primary-400)'" onmouseout="this.style.transform='none'; this.style.borderColor='var(--border-color)'">
          <div style="font-size:32px; color: var(--color-primary-500); margin-bottom:8px;"><i class="bi bi-<?= e($cat['icon'] ?: 'box') ?>"></i></div>
          <div class="fw-bold"><?= e($cat['name']) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
