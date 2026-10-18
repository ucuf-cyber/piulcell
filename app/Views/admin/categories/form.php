<div class="admin-card">
  <form method="POST">
    <?= csrf_field() ?>
    <div class="form-group"><label class="form-label">Nama</label><input class="form-control" name="name" value="<?= e($cat['name'] ?? '') ?>" required></div>
    <div class="form-group"><label class="form-label">Icon (Bootstrap Icons, mis: smartphone, tablet, headphones)</label><input class="form-control" name="icon" value="<?= e($cat['icon'] ?? 'box') ?>"></div>
    <div class="form-group"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="2"><?= e($cat['description'] ?? '') ?></textarea></div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">Urutan</label><input type="number" class="form-control" name="sort_order" value="<?= (int)($cat['sort_order'] ?? 0) ?>"></div>
      <div class="form-group"><label class="form-label">&nbsp;</label><label><input type="checkbox" name="is_active" value="1" <?= !isset($cat) || !empty($cat['is_active']) ? 'checked' : '' ?>> Aktif</label></div>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a href="/admin/categories" class="btn btn-ghost">Batal</a>
  </form>
</div>
