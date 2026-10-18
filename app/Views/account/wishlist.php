<div class="container" style="padding: 32px 0;">
  <div style="display:grid; grid-template-columns: 260px 1fr; gap: 32px;">
    <?php include APP_PATH . '/Views/partials/account_sidebar.php'; ?>
    <div>
      <h2 class="mb-3">Wishlist</h2>
      <?php if (!$wishlist): ?>
        <div class="empty-state"><div class="empty-icon"><i class="bi bi-heart"></i></div><h3>Wishlist kosong</h3><a href="/katalog" class="btn btn-primary">Lihat Produk</a></div>
      <?php else: ?>
        <div class="product-grid">
          <?php foreach ($wishlist as $p) include APP_PATH . '/Views/partials/product_card.php'; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
