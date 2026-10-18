<div style="padding:16px 0; min-width:300px;">
<?php if (!$items): ?>
  <p class="text-muted text-center">Keranjang kosong</p>
<?php else: foreach (array_slice($items, 0, 5) as $it): ?>
  <div class="d-flex gap-2 align-center mb-2">
    <img src="<?= product_image($it['image']) ?>" alt="" style="width:48px;height:48px;object-fit:contain;background:var(--bg-base);border-radius:8px;">
    <div style="flex:1;">
      <div class="fw-medium text-sm"><?= e($it['name']) ?></div>
      <div class="text-xs text-muted"><?= (int)$it['quantity'] ?>× <?= rupiah($it['final_price']) ?></div>
    </div>
  </div>
<?php endforeach; endif; ?>
</div>
