<div class="admin-card">
  <form method="POST">
    <?= csrf_field() ?>
    <div class="form-group"><label class="form-label">Nama Brand</label><input class="form-control" name="name" value="<?= e($brand['name'] ?? '') ?>" required></div>
    <div class="form-group"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="2"><?= e($brand['description'] ?? '') ?></textarea></div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">Urutan</label><input type="number" class="form-control" name="sort_order" value="<?= (int)($brand['sort_order'] ?? 0) ?>"></div>
      <div class="form-group"><label class="form-label">&nbsp;</label><label><input type="checkbox" name="is_featured" value="1" <?= !empty($brand['is_featured']) ? 'checked' : '' ?>> Tampilkan di Beranda</label></div>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a href="/admin/brands" class="btn btn-ghost">Batal</a>
  </form>
</div>
