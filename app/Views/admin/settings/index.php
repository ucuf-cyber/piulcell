<?php
// Friendly labels & icons
$labels = [
  'store_name' => ['Nama Toko', 'shop'],
  'store_tagline' => ['Tagline / Slogan', 'megaphone'],
  'store_email' => ['Email Toko', 'envelope'],
  'store_phone' => ['Nomor Telepon / WhatsApp', 'telephone'],
  'store_whatsapp' => ['Nomor WhatsApp (untuk link wa.me)', 'whatsapp'],
  'store_address' => ['Alamat Lengkap Toko', 'geo-alt'],
  'store_latitude' => ['Latitude Toko (untuk hitung ongkir)', 'crosshair'],
  'store_longitude' => ['Longitude Toko (untuk hitung ongkir)', 'crosshair2'],
  'free_shipping_min' => ['Minimal Belanja Gratis Ongkir (Rp)', 'truck'],
  'tax_rate' => ['Tarif Pajak (%)', 'percent'],
  'payment_expire_hours' => ['Batas Waktu Pembayaran (jam)', 'clock'],
  'cod_max_distance' => ['Jarak Maksimum COD (km)', 'cash-stack'],
  'cod_enabled' => ['Aktifkan Bayar di Tempat (COD)', 'cash'],
  'transfer_enabled' => ['Aktifkan Transfer Bank', 'bank'],
  'qris_enabled' => ['Aktifkan QRIS', 'qr-code'],
  'social_instagram' => ['Link Instagram', 'instagram'],
  'social_facebook' => ['Link Facebook', 'facebook'],
  'social_tiktok' => ['Link TikTok', 'tiktok'],
];

$groupMeta = [
  'general' => ['Identitas Toko', 'shop', 'Nama, kontak, dan alamat toko Anda'],
  'shipping' => ['Pengaturan Ongkir', 'truck', 'Lokasi toko & aturan gratis ongkir'],
  'finance' => ['Pajak & Keuangan', 'cash-stack', 'Pajak penjualan (jika ada)'],
  'payment' => ['Metode Pembayaran', 'credit-card', 'Aktif/nonaktif & batas waktu bayar'],
  'social' => ['Media Sosial', 'share', 'Link sosmed di footer toko'],
];

$toggles = ['cod_enabled','transfer_enabled','qris_enabled'];
?>
<div class="alert alert-info" style="margin-bottom:24px;">
  <i class="bi bi-info-circle-fill"></i> Perubahan akan langsung tampil di toko setelah Anda klik <strong>Simpan Pengaturan</strong>.
</div>

<form method="POST">
  <?= csrf_field() ?>
  <?php foreach ($groupMeta as $gKey => [$gTitle, $gIcon, $gDesc]): if (empty($groups[$gKey])) continue; ?>
    <div class="settings-section reveal">
      <div class="section-head">
        <i class="bi bi-<?= e($gIcon) ?>"></i>
        <div>
          <h3><?= e($gTitle) ?></h3>
          <div class="text-xs text-muted"><?= e($gDesc) ?></div>
        </div>
      </div>
      <div class="section-body">
        <?php foreach ($groups[$gKey] as $key => $val):
          [$label, $icon] = $labels[$key] ?? [ucwords(str_replace('_',' ', $key)), 'gear'];
          $isToggle = in_array($key, $toggles);
        ?>
          <div class="setting-row">
            <label for="<?= e($key) ?>">
              <i class="bi bi-<?= e($icon) ?> text-primary"></i> <?= e($label) ?>
              <span class="form-help-text">key: <code><?= e($key) ?></code></span>
            </label>
            <div>
              <?php if ($isToggle): ?>
                <label class="switch">
                  <input type="hidden" name="<?= e($key) ?>" value="0">
                  <input type="checkbox" id="<?= e($key) ?>" name="<?= e($key) ?>" value="1" <?= $val ? 'checked' : '' ?>>
                  <span class="slider-bg"></span>
                </label>
                <span class="text-sm text-muted" style="margin-left:10px;"><?= $val ? 'Aktif' : 'Nonaktif' ?></span>
              <?php elseif (in_array($key, ['store_address']) || strlen((string)$val) > 80): ?>
                <textarea id="<?= e($key) ?>" name="<?= e($key) ?>" class="form-control" rows="2"><?= e($val) ?></textarea>
              <?php elseif (in_array($key, ['store_latitude','store_longitude'])): ?>
                <input id="<?= e($key) ?>" name="<?= e($key) ?>" class="form-control" value="<?= e($val) ?>" placeholder="<?= $key === 'store_latitude' ? '-6.289888' : '106.763866' ?>">
              <?php elseif (in_array($key, ['free_shipping_min','cod_max_distance','payment_expire_hours','tax_rate'])): ?>
                <input id="<?= e($key) ?>" name="<?= e($key) ?>" type="number" class="form-control" value="<?= e($val) ?>">
              <?php else: ?>
                <input id="<?= e($key) ?>" name="<?= e($key) ?>" class="form-control" value="<?= e($val) ?>">
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <div style="position:sticky; bottom:0; padding:16px 0; background:var(--bg-base); border-top:1px solid var(--border-color); margin-top:16px;">
    <button class="btn btn-primary btn-lg"><i class="bi bi-check2-circle"></i> Simpan Pengaturan</button>
    <a href="/admin/dashboard" class="btn btn-ghost">Batal</a>
  </div>
</form>
