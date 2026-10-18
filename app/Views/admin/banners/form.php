<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-group"><label class="form-label">Judul</label><input class="form-control" name="title" value="<?= e($b['title'] ?? '') ?>" required></div>
    <div class="form-group"><label class="form-label">Subjudul</label><input class="form-control" name="subtitle" value="<?= e($b['subtitle'] ?? '') ?>"></div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">URL Tujuan</label><input class="form-control" name="link_url" value="<?= e($b['link_url'] ?? '') ?>"></div>
      <div class="form-group"><label class="form-label">Teks Tombol</label><input class="form-control" name="link_text" value="<?= e($b['link_text'] ?? 'Lihat Detail') ?>"></div>
    </div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group">
        <label class="form-label">Posisi</label>
        <select name="position" class="form-control">
          <option value="hero" <?= ($b['position']??'')==='hero'?'selected':'' ?>>Hero (slider utama)</option>
          <option value="promo" <?= ($b['position']??'')==='promo'?'selected':'' ?>>Promo (banner tengah)</option>
          <option value="brand" <?= ($b['position']??'')==='brand'?'selected':'' ?>>Brand</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Urutan</label><input type="number" class="form-control" name="sort_order" value="<?= e($b['sort_order'] ?? 0) ?>"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Gambar (16:9 ideal, JPG/PNG/WebP)</label>
      <input type="file" name="image_desktop" accept=".jpg,.jpeg,.png,.webp">
      <?php if (!empty($b['image_desktop'])): ?><div class="mt-1"><img src="<?= product_image($b['image_desktop'], 'banners') ?>" style="max-width:200px; border-radius:8px;"></div><?php endif; ?>
    </div>
    <?php if (isset($b)): ?><label><input type="checkbox" name="is_active" value="1" <?= !empty($b['is_active'])?'checked':'' ?>> Aktif</label><?php endif; ?>
    <div class="mt-2"><button class="btn btn-primary">Simpan</button> <a href="/admin/banners" class="btn btn-ghost">Batal</a></div>
  </form>
</div>
