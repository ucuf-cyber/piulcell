<?php /** @var array $products, $allBrands, $allCats, $filters, $total, $page, $pages */ ?>
<section style="background: var(--bg-surface); padding: 20px 0; border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <h1 style="font-size: var(--text-2xl); margin: 0;">Katalog Produk</h1>
    <div class="text-sm text-muted">Tersedia <?= number_format($total) ?> produk</div>
  </div>
</section>

<div class="container">
  <div class="catalog-layout">
    <aside class="catalog-sidebar">
      <form method="GET" id="filterForm">
        <?php if (!empty($filters['q'])): ?><input type="hidden" name="q" value="<?= e($filters['q']) ?>"><?php endif; ?>

        <div class="filter-group">
          <div class="filter-title">Brand</div>
          <div class="filter-options" style="max-height: 200px; overflow-y:auto;">
            <label><input type="radio" name="brand" value="" <?= $filters['brand'] === '' ? 'checked' : '' ?> onchange="this.form.submit()"> Semua</label>
            <?php foreach ($allBrands as $b): ?>
              <label><input type="radio" name="brand" value="<?= e($b['slug']) ?>" <?= $filters['brand'] === $b['slug'] ? 'checked' : '' ?> onchange="this.form.submit()"> <?= e($b['name']) ?></label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="filter-group">
          <div class="filter-title">Kategori</div>
          <div class="filter-options">
            <label><input type="radio" name="kategori" value="" <?= $filters['category'] === '' ? 'checked' : '' ?> onchange="this.form.submit()"> Semua</label>
            <?php foreach ($allCats as $c): ?>
              <label><input type="radio" name="kategori" value="<?= e($c['slug']) ?>" <?= $filters['category'] === $c['slug'] ? 'checked' : '' ?> onchange="this.form.submit()"> <?= e($c['name']) ?></label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="filter-group">
          <div class="filter-title">Promo</div>
          <div class="filter-options">
            <label><input type="checkbox" name="promo" value="1" <?= !empty($filters['promo']) ? 'checked' : '' ?> onchange="this.form.submit()"> Hanya yang diskon</label>
          </div>
        </div>

        <a href="/katalog" class="btn btn-outline btn-sm btn-block">Reset Filter</a>
      </form>
    </aside>

    <div>
      <div class="catalog-header">
        <div class="catalog-count">Menampilkan <strong><?= count($products) ?></strong> dari <strong><?= $total ?></strong> produk</div>
        <form method="GET" id="sortForm">
          <?php foreach ($filters as $k => $v): if ($v && $k !== 'sort'): ?><input type="hidden" name="<?= e($k === 'category' ? 'kategori' : $k) ?>" value="<?= e($v) ?>"><?php endif; endforeach; ?>
          <select name="sort" class="form-control" style="width:220px;" onchange="this.form.submit()">
            <option value="newest" <?= $filters['sort']==='newest'?'selected':'' ?>>Terbaru</option>
            <option value="popular" <?= $filters['sort']==='popular'?'selected':'' ?>>Terpopuler</option>
            <option value="price_asc" <?= $filters['sort']==='price_asc'?'selected':'' ?>>Harga Terendah</option>
            <option value="price_desc" <?= $filters['sort']==='price_desc'?'selected':'' ?>>Harga Tertinggi</option>
            <option value="rating" <?= $filters['sort']==='rating'?'selected':'' ?>>Rating Tertinggi</option>
          </select>
        </form>
      </div>

      <?php if (!$products): ?>
        <div class="empty-state">
          <div class="empty-icon"><i class="bi bi-search"></i></div>
          <h3>Produk tidak ditemukan</h3>
          <p>Coba ubah filter atau kata kunci pencarian Anda.</p>
        </div>
      <?php else: ?>
        <div class="product-grid">
          <?php foreach ($products as $p) include APP_PATH . '/Views/partials/product_card.php'; ?>
        </div>
        <?php if ($pages > 1): ?>
          <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++):
              $params = $_GET; $params['page'] = $i;
              $url = '/katalog?' . http_build_query($params);
            ?>
              <?php if ($i === $page): ?><span class="current"><?= $i ?></span>
              <?php else: ?><a href="<?= e($url) ?>"><?= $i ?></a><?php endif; ?>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
