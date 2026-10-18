<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">Nama Produk</label><input class="form-control" name="name" required value="<?= e($product['name'] ?? '') ?>"></div>
      <div class="form-group"><label class="form-label">SKU Base</label><input class="form-control" name="sku_base" value="<?= e($product['sku_base'] ?? '') ?>"></div>
    </div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group">
        <label class="form-label">Brand</label>
        <select name="brand_id" class="form-control" required>
          <?php foreach ($brands as $b): ?><option value="<?= (int)$b['id'] ?>" <?= ($product['brand_id'] ?? 0) == $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-control" required>
          <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= ($product['category_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="3"><?= e($product['description'] ?? '') ?></textarea></div>
    <div class="form-group">
      <label class="form-label">Spesifikasi (format: <code>Kunci: Nilai</code>, satu per baris)</label>
      <textarea class="form-control" name="specifications" rows="6" placeholder="Layar: 6.7&quot; OLED&#10;Chipset: Snapdragon 8 Gen 3&#10;Baterai: 5000 mAh"><?= e($product['specifications_text'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Thumbnail (JPG/PNG/WebP, maks 5MB)</label>
      <input type="file" name="thumbnail" accept=".jpg,.jpeg,.png,.webp">
      <?php if (!empty($product['thumbnail'])): ?><div class="mt-1"><img src="<?= product_image($product['thumbnail']) ?>" style="width:100px; height:100px; object-fit:contain; background:var(--bg-base); border-radius:8px;"></div><?php endif; ?>
    </div>
    <div class="form-group">
      <label class="form-label d-flex gap-3" style="flex-wrap:wrap;">
        <span><input type="checkbox" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?>> Featured</span>
        <span><input type="checkbox" name="is_new" value="1" <?= !empty($product['is_new']) ? 'checked' : '' ?>> Produk Baru</span>
        <?php if (isset($product['is_active'])): ?>
          <span><input type="checkbox" name="is_active" value="1" <?= !empty($product['is_active']) ? 'checked' : '' ?>> Aktif</span>
        <?php endif; ?>
      </label>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a href="/admin/products" class="btn btn-ghost">Batal</a>
  </form>
</div>
