<div class="container" style="padding: 32px 0;">
  <div style="display:grid; grid-template-columns: 260px 1fr; gap: 32px;">
    <?php include APP_PATH . '/Views/partials/account_sidebar.php'; ?>
    <div>
      <div class="d-flex justify-between align-center mb-3">
        <h2 class="mb-0">Alamat Saya</h2>
        <a href="/akun/alamat/tambah" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Alamat</a>
      </div>
      <?php if (!$addresses): ?>
        <div class="empty-state"><div class="empty-icon"><i class="bi bi-geo-alt"></i></div><h3>Belum ada alamat</h3><p>Tambahkan alamat untuk pengiriman.</p></div>
      <?php else: ?>
        <div style="display:grid; gap:16px;">
          <?php foreach ($addresses as $a): ?>
            <div class="admin-card" style="margin:0;">
              <div class="d-flex justify-between align-center mb-1">
                <div class="fw-bold"><?= e($a['label']) ?> <?php if ($a['is_primary']): ?><span class="badge badge-primary">Utama</span><?php endif; ?></div>
                <div class="d-flex gap-1">
                  <?php if (!$a['is_primary']): ?>
                    <form method="POST" action="/akun/alamat/primary/<?= (int)$a['id'] ?>" style="display:inline;">
                      <?= csrf_field() ?><button class="btn btn-outline btn-sm">Jadikan Utama</button>
                    </form>
                  <?php endif; ?>
                  <form method="POST" action="/akun/alamat/hapus/<?= (int)$a['id'] ?>" style="display:inline;" onsubmit="return confirm('Hapus alamat ini?')">
                    <?= csrf_field() ?><button class="btn btn-ghost btn-sm text-danger"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
              </div>
              <div class="fw-medium"><?= e($a['recipient']) ?> · <?= e($a['phone']) ?></div>
              <div class="text-sm text-muted"><?= e($a['address_detail']) ?>, <?= e($a['district']) ?>, <?= e($a['city']) ?>, <?= e($a['province']) ?> <?= e($a['postal_code']) ?></div>
              <?php if ($a['latitude']): ?><div class="text-xs text-muted">📍 <?= number_format((float)$a['latitude'], 5) ?>, <?= number_format((float)$a['longitude'], 5) ?></div><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
