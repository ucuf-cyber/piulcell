<?php /** @var array $items, $subtotal */ ?>
<section style="background: var(--bg-surface); padding: 20px 0; border-bottom: 1px solid var(--border-color);">
  <div class="container"><h1 style="font-size: var(--text-2xl); margin: 0;"><i class="bi bi-bag"></i> Keranjang Belanja</h1></div>
</section>

<div class="container">
  <?php if (!$items): ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="bi bi-bag"></i></div>
      <h3>Keranjang kosong</h3>
      <p>Yuk mulai belanja dulu!</p>
      <a href="/katalog" class="btn btn-primary"><i class="bi bi-shop"></i> Lihat Produk</a>
    </div>
  <?php else: ?>
    <div class="cart-layout">
      <div class="cart-items">
        <?php foreach ($items as $it): ?>
          <div class="cart-item">
            <div class="cart-item-image"><img src="<?= product_image($it['image']) ?>" alt=""></div>
            <div>
              <a href="/produk/<?= e($it['slug']) ?>" class="cart-item-name" style="color: var(--text-primary);"><?= e($it['name']) ?></a>
              <div class="cart-item-variant"><?= e($it['brand_name']) ?> · <?= e(implode(' / ', array_values($it['combination']))) ?></div>
              <div class="cart-item-price"><?= rupiah($it['final_price']) ?>
                <?php if (!empty($it['price_promo']) && $it['price_promo'] > 0): ?>
                  <span class="text-xs text-muted" style="text-decoration:line-through;"><?= rupiah($it['price']) ?></span>
                <?php endif; ?>
              </div>
            </div>
            <div class="quantity-control">
              <button onclick="updateCartItem(<?= (int)$it['variant_id'] ?>, <?= max(1, (int)$it['quantity']-1) ?>)"><i class="bi bi-dash"></i></button>
              <input type="number" value="<?= (int)$it['quantity'] ?>" min="1" max="<?= (int)$it['stock'] ?>" readonly>
              <button onclick="updateCartItem(<?= (int)$it['variant_id'] ?>, <?= (int)$it['quantity']+1 ?>)"><i class="bi bi-plus"></i></button>
            </div>
            <div style="text-align:right;">
              <div class="fw-bold mb-1"><?= rupiah($it['line_total']) ?></div>
              <button class="btn btn-ghost btn-sm" onclick="removeCartItem(<?= (int)$it['variant_id'] ?>)" title="Hapus"><i class="bi bi-trash text-danger"></i></button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="summary-card">
        <div class="summary-title">Ringkasan</div>
        <div class="summary-row"><span>Subtotal (<?= count($items) ?> item)</span><span class="summary-value"><?= rupiah($subtotal) ?></span></div>
        <div class="summary-row"><span>Estimasi Ongkir</span><span class="summary-value">Hitung di checkout</span></div>
        <div class="summary-row total"><span>Total</span><span class="summary-value"><?= rupiah($subtotal) ?></span></div>
        <a href="/checkout" class="btn btn-primary btn-block btn-lg mt-3">Lanjut Checkout <i class="bi bi-arrow-right"></i></a>
        <a href="/katalog" class="btn btn-outline btn-block mt-2">Lanjut Belanja</a>
      </div>
    </div>
  <?php endif; ?>
</div>
