<?php /** @var array $p */
$mp = product_min_price((int)$p['id']);
$rating = product_avg_rating((int)$p['id']);
$brandName = $p['brand_name'] ?? Database::val('SELECT name FROM brands WHERE id=?', [$p['brand_id']]);
$dv = product_default_variant((int)$p['id']);
$discount = 0;
if ($dv && !empty($dv['price_promo']) && $dv['price_promo'] > 0 && $dv['price_promo'] < $dv['price']) {
  $discount = round((1 - $dv['price_promo'] / $dv['price']) * 100);
}
?>
<div class="product-card">
  <a class="product-link" href="/produk/<?= e($p['slug']) ?>">
    <div class="product-thumb">
      <img src="<?= product_image($p['thumbnail']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
      <div class="product-badges">
        <?php if (!empty($p['is_new'])): ?><span class="product-badge">NEW</span><?php endif; ?>
        <?php if ($discount > 0): ?><span class="product-badge sale">-<?= $discount ?>%</span><?php endif; ?>
        <?php if (($p['sold_count'] ?? 0) > 100): ?><span class="product-badge hot">HOT</span><?php endif; ?>
      </div>
      <button class="product-wishlist" type="button" onclick="event.preventDefault(); toggleWishlist(<?= (int)$p['id'] ?>, this)" aria-label="Wishlist"><i class="bi bi-heart"></i></button>
    </div>
    <div class="product-body">
      <div class="product-brand"><?= e($brandName) ?></div>
      <div class="product-name"><?= e($p['name']) ?></div>
      <div class="product-rating">
        <?= star_rating($rating['avg']) ?>
        <span class="rating-count">(<?= $rating['count'] ?>)</span>
      </div>
      <div class="product-price">
        <span class="price-current"><?= rupiah($mp['min'] ?? 0) ?></span>
        <?php if ($dv && !empty($dv['price_promo']) && $dv['price_promo'] > 0 && $dv['price_promo'] < $dv['price']): ?>
          <span class="price-old"><?= rupiah($dv['price']) ?></span>
        <?php endif; ?>
      </div>
    </div>
  </a>
  <div style="padding: 0 16px 16px;">
    <?php if ($dv && (int)$dv['id'] > 0): ?>
      <button class="btn btn-primary btn-block btn-sm" onclick="addToCart(<?= (int)$dv['id'] ?>, 1)"><i class="bi bi-bag-plus"></i> Tambah Keranjang</button>
    <?php else: ?>
      <a href="/produk/<?= e($p['slug']) ?>" class="btn btn-outline btn-block btn-sm">Lihat Varian <i class="bi bi-arrow-right"></i></a>
    <?php endif; ?>
  </div>
</div>
