<div class="admin-card">
  <form method="POST">
    <?= csrf_field() ?>
    <div class="form-group"><label class="form-label">Nama Zona</label><input class="form-control" name="name" value="<?= e($z['name'] ?? '') ?>" required></div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">Jarak Maks (km, 0 = tanpa batas)</label><input type="number" class="form-control" name="max_distance" value="<?= e($z['max_distance'] ?? '0') ?>"></div>
      <div class="form-group"><label class="form-label">Tarif Dasar (Rp)</label><input type="number" class="form-control" name="base_price" value="<?= e($z['base_price'] ?? '5000') ?>" required></div>
    </div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">Per Km (Rp)</label><input type="number" class="form-control" name="price_per_km" value="<?= e($z['price_per_km'] ?? '0') ?>"></div>
      <div class="form-group"><label class="form-label">Kurir</label><input class="form-control" name="courier" value="<?= e($z['courier'] ?? 'Internal') ?>"></div>
    </div>
    <div class="form-group"><label class="form-label">ETD (mis: 1-2)</label><input class="form-control" name="etd_days" value="<?= e($z['etd_days'] ?? '1-2') ?>"></div>
    <?php if (isset($z)): ?><label><input type="checkbox" name="is_active" value="1" <?= !empty($z['is_active'])?'checked':'' ?>> Aktif</label><?php endif; ?>
    <div class="mt-2"><button class="btn btn-primary">Simpan</button> <a href="/admin/shipping-zones" class="btn btn-ghost">Batal</a></div>
  </form>
</div>
