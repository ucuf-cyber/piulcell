<?php /** @var array $product, $attributes, $variants, $reviews, $ratingInfo, $related, $inWishlist */
$firstVariant = $variants[0] ?? null;
$initialFinal = $firstVariant ? (!empty($firstVariant['price_promo']) ? (float)$firstVariant['price_promo'] : (float)$firstVariant['price']) : 0;
$initialOrig = $firstVariant ? (float)$firstVariant['price'] : 0;
$initialDiscount = ($firstVariant && $initialFinal < $initialOrig) ? round((1 - $initialFinal/$initialOrig)*100) : 0;
$variantsJson = json_encode($variants, JSON_HEX_TAG | JSON_HEX_AMP);
?>
<div class="container">
  <div style="padding: 16px 0; font-size: var(--text-sm); color: var(--text-secondary);">
    <a href="/">Beranda</a> / <a href="/katalog?kategori=<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a> / <span><?= e($product['name']) ?></span>
  </div>

  <div class="pd-layout">
    <div class="pd-gallery">
      <div class="pd-main-image">
        <img id="mainImg" src="<?= product_image($product['thumbnail']) ?>" alt="<?= e($product['name']) ?>">
      </div>
      <div class="pd-thumbs">
        <div class="pd-thumb active" onclick="changeMainImg(this, '<?= product_image($product['thumbnail']) ?>')"><img src="<?= product_image($product['thumbnail']) ?>" alt=""></div>
        <?php
          $allImgs = [];
          foreach ($variants as $v) {
            foreach ($v['images'] as $im) if (!in_array($im, $allImgs)) $allImgs[] = $im;
          }
          foreach (array_slice($allImgs, 0, 5) as $im):
        ?>
          <div class="pd-thumb" onclick="changeMainImg(this, '<?= product_image($im) ?>')"><img src="<?= product_image($im) ?>" alt=""></div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="pd-info">
      <div class="pd-brand-row">
        <span style="font-weight:600; color: var(--color-primary-500);"><?= e($product['brand_name']) ?></span>
        <span>·</span>
        <span><?= star_rating($ratingInfo['avg']) ?> <?= number_format($ratingInfo['avg'],1) ?> (<?= $ratingInfo['count'] ?> ulasan)</span>
        <span>·</span>
        <span><i class="bi bi-eye"></i> <?= number_format($product['view_count']) ?></span>
      </div>
      <h1><?= e($product['name']) ?></h1>

      <div class="pd-price-box">
        <span class="pd-price-current" id="pdPriceCurrent"><?= rupiah($initialFinal) ?></span>
        <?php if ($initialDiscount > 0): ?>
          <span class="pd-price-old" id="pdPriceOld"><?= rupiah($initialOrig) ?></span>
          <span class="pd-savings" id="pdSavings">Hemat <?= $initialDiscount ?>%</span>
        <?php else: ?>
          <span class="pd-price-old" id="pdPriceOld" style="display:none;"></span>
          <span class="pd-savings" id="pdSavings" style="display:none;"></span>
        <?php endif; ?>
      </div>

      <?php foreach ($attributes as $attr): ?>
        <div class="variant-row">
          <div class="variant-label"><?= e($attr['attribute']) ?>: <span id="sel-<?= e($attr['attribute']) ?>">Pilih <?= e($attr['attribute']) ?></span></div>
          <div class="variant-options">
            <?php foreach ($attr['values'] as $val): ?>
              <button type="button" class="variant-btn" data-attr="<?= e($attr['attribute']) ?>" data-value="<?= e($val) ?>" onclick="selectOption(this)"><?= e($val) ?></button>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="pd-stock in-stock" id="pdStock">
        <?php if ($firstVariant): ?>
          <i class="bi bi-check-circle-fill"></i> Tersedia (<?= (int)$firstVariant['stock'] ?> unit) — SKU: <?= e($firstVariant['sku']) ?>
        <?php endif; ?>
      </div>

      <div class="quantity-group">
        <label class="variant-label" style="margin:0;">Jumlah:</label>
        <div class="quantity-control">
          <button type="button" onclick="changeQty(document.getElementById('qty'), -1)"><i class="bi bi-dash"></i></button>
          <input type="number" id="qty" value="1" min="1">
          <button type="button" onclick="changeQty(document.getElementById('qty'), 1)"><i class="bi bi-plus"></i></button>
        </div>
      </div>

      <input type="hidden" id="variantId" value="<?= (int)($firstVariant['id'] ?? 0) ?>">

      <div class="pd-buttons">
        <button class="btn btn-outline btn-lg" onclick="addToCart(parseInt(document.getElementById('variantId').value), parseInt(document.getElementById('qty').value))"><i class="bi bi-bag-plus"></i> Tambah Keranjang</button>
        <button class="btn btn-primary btn-lg" onclick="buyNow(parseInt(document.getElementById('variantId').value), parseInt(document.getElementById('qty').value))"><i class="bi bi-lightning-charge-fill"></i> Beli Sekarang</button>
        <button class="btn btn-ghost <?= $inWishlist ? 'text-danger' : '' ?>" onclick="toggleWishlist(<?= (int)$product['id'] ?>, this)" title="Wishlist"><i class="bi bi-heart<?= $inWishlist ? '-fill' : '' ?>"></i></button>
      </div>

      <div style="background: var(--bg-card); border:1px solid var(--border-color); border-radius: var(--radius-md); padding: 16px; margin-top: 16px;">
        <div class="d-flex gap-2 align-center" style="margin-bottom:8px;"><i class="bi bi-shield-check text-primary"></i> <strong>Garansi resmi distributor</strong></div>
        <div class="d-flex gap-2 align-center" style="margin-bottom:8px;"><i class="bi bi-truck text-primary"></i> <span>Gratis ongkir min. belanja Rp 1.500.000</span></div>
        <div class="d-flex gap-2 align-center"><i class="bi bi-cash-coin text-primary"></i> <span>Bisa COD (radius 15 km dari toko)</span></div>
      </div>
    </div>
  </div>

  <div class="pd-tabs">
    <div class="tab-nav">
      <button class="tab-btn active" onclick="showTab('tab-spec', this)">Spesifikasi</button>
      <button class="tab-btn" onclick="showTab('tab-desc', this)">Deskripsi</button>
      <button class="tab-btn" onclick="showTab('tab-rev', this)">Ulasan (<?= $ratingInfo['count'] ?>)</button>
    </div>

    <div class="tab-pane active" id="tab-spec">
      <table class="spec-table">
        <?php foreach ($product['specifications'] as $k => $v): ?>
          <tr><th><?= e($k) ?></th><td><?= e($v) ?></td></tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="tab-pane" id="tab-desc">
      <div style="line-height:1.8; font-size: var(--text-base);"><?= nl2br(e($product['description'])) ?></div>
    </div>

    <div class="tab-pane" id="tab-rev">
      <?php if (!$reviews): ?>
        <p class="text-muted">Belum ada ulasan. Jadi yang pertama!</p>
      <?php else: foreach ($reviews as $r): ?>
        <div class="review-item">
          <div class="review-header">
            <div>
              <div class="review-user"><?= e($r['user_name']) ?></div>
              <div><?= star_rating((float)$r['rating']) ?></div>
            </div>
            <div class="review-meta"><?= time_ago($r['created_at']) ?></div>
          </div>
          <?php if (!empty($r['title'])): ?><strong><?= e($r['title']) ?></strong><?php endif; ?>
          <div class="review-body"><?= nl2br(e($r['body'])) ?></div>
          <?php if (!empty($r['reply'])): ?>
            <div class="review-reply"><strong>Balasan Toko:</strong><br><?= nl2br(e($r['reply'])) ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <?php if ($related): ?>
  <section class="section">
    <div class="section-header"><h2 class="section-title">Produk Serupa</h2></div>
    <div class="product-grid">
      <?php foreach ($related as $p) include APP_PATH . '/Views/partials/product_card.php'; ?>
    </div>
  </section>
  <?php endif; ?>
</div>

<script>
const PRODUCT_VARIANTS = <?= $variantsJson ?>;
const ATTR_KEYS = <?= json_encode(array_map(fn($a) => $a['attribute'], $attributes)) ?>;
const selected = {};

function changeMainImg(el, src) {
  document.getElementById('mainImg').src = src;
  document.querySelectorAll('.pd-thumb').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
}

function selectOption(btn) {
  const attr = btn.dataset.attr;
  const val = btn.dataset.value;
  selected[attr] = val;
  document.querySelectorAll(`[data-attr="${attr}"]`).forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('sel-' + attr).textContent = val;
  matchVariant();
}

function matchVariant() {
  const allSelected = ATTR_KEYS.every(k => selected[k]);
  if (!allSelected) return;
  const match = PRODUCT_VARIANTS.find(v => ATTR_KEYS.every(k => v.combination[k] === selected[k]));
  if (!match) {
    document.getElementById('pdStock').className = 'pd-stock out-stock';
    document.getElementById('pdStock').innerHTML = '<i class="bi bi-x-circle-fill"></i> Kombinasi ini tidak tersedia';
    document.getElementById('variantId').value = 0;
    return;
  }
  // price
  const final = match.price_promo && match.price_promo < match.price ? Number(match.price_promo) : Number(match.price);
  const orig = Number(match.price);
  document.getElementById('pdPriceCurrent').textContent = 'Rp ' + final.toLocaleString('id-ID');
  const oldEl = document.getElementById('pdPriceOld');
  const savEl = document.getElementById('pdSavings');
  if (final < orig) {
    oldEl.style.display = '';
    oldEl.textContent = 'Rp ' + orig.toLocaleString('id-ID');
    savEl.style.display = '';
    savEl.textContent = 'Hemat ' + Math.round((1 - final/orig) * 100) + '%';
  } else {
    oldEl.style.display = 'none';
    savEl.style.display = 'none';
  }
  // stock
  const stockEl = document.getElementById('pdStock');
  if (match.stock === 0 || match.stock === '0') {
    stockEl.className = 'pd-stock out-stock';
    stockEl.innerHTML = '<i class="bi bi-x-circle-fill"></i> Stok habis';
  } else if (match.stock <= 5) {
    stockEl.className = 'pd-stock low-stock';
    stockEl.innerHTML = `<i class="bi bi-exclamation-circle-fill"></i> Stok tersisa ${match.stock} unit — SKU: ${match.sku}`;
  } else {
    stockEl.className = 'pd-stock in-stock';
    stockEl.innerHTML = `<i class="bi bi-check-circle-fill"></i> Tersedia (${match.stock} unit) — SKU: ${match.sku}`;
  }
  // image
  if (match.images && match.images.length > 0) {
    // Just trust the image path; the placeholder handles missing files server-side
    document.getElementById('mainImg').src = '/uploads/products/' + match.images[0];
    document.getElementById('mainImg').onerror = function () {
      this.src = '/assets/img/placeholder.svg';
      this.onerror = null;
    };
  }
  document.getElementById('variantId').value = match.id;
}

// Auto-select first variant attributes if only one option
ATTR_KEYS.forEach(k => {
  const btns = document.querySelectorAll(`[data-attr="${k}"]`);
  if (btns.length === 1) {
    btns[0].click();
  }
});
</script>
