<div class="admin-card">
  <form method="POST">
    <?= csrf_field() ?>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">Kode</label><input class="form-control" name="code" value="<?= e($v['code'] ?? '') ?>" required <?= isset($v) ? 'readonly' : '' ?> style="text-transform:uppercase;"></div>
      <div class="form-group"><label class="form-label">Nama</label><input class="form-control" name="name" value="<?= e($v['name'] ?? '') ?>" required></div>
    </div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group">
        <label class="form-label">Tipe</label>
        <select name="type" class="form-control">
          <option value="percentage" <?= ($v['type']??'')==='percentage'?'selected':'' ?>>Persentase (%)</option>
          <option value="fixed" <?= ($v['type']??'')==='fixed'?'selected':'' ?>>Nominal (Rp)</option>
          <option value="free_shipping" <?= ($v['type']??'')==='free_shipping'?'selected':'' ?>>Gratis Ongkir</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Nilai</label><input type="number" step="0.01" class="form-control" name="value" value="<?= e($v['value'] ?? '0') ?>"></div>
    </div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">Min. Belanja</label><input type="number" class="form-control" name="min_purchase" value="<?= e($v['min_purchase'] ?? '0') ?>"></div>
      <div class="form-group"><label class="form-label">Maks. Diskon (opsional)</label><input type="number" class="form-control" name="max_discount" value="<?= e($v['max_discount'] ?? '') ?>"></div>
    </div>
    <div class="d-grid grid-3 gap-2">
      <div class="form-group"><label class="form-label">Quota (kosong=unlimited)</label><input type="number" class="form-control" name="quota" value="<?= e($v['quota'] ?? '') ?>"></div>
      <div class="form-group"><label class="form-label">Mulai</label><input type="date" class="form-control" name="start_date" value="<?= e($v['start_date'] ?? date('Y-m-d')) ?>"></div>
      <div class="form-group"><label class="form-label">Berakhir</label><input type="date" class="form-control" name="end_date" value="<?= e($v['end_date'] ?? '') ?>"></div>
    </div>
    <?php if (isset($v)): ?><label><input type="checkbox" name="is_active" value="1" <?= !empty($v['is_active'])?'checked':'' ?>> Aktif</label><?php endif; ?>
    <div class="mt-2"><button class="btn btn-primary">Simpan</button> <a href="/admin/vouchers" class="btn btn-ghost">Batal</a></div>
  </form>
</div>
