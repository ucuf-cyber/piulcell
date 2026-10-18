<div class="container" style="padding: 32px 0;">
  <div style="display:grid; grid-template-columns: 260px 1fr; gap: 32px;">
    <?php include APP_PATH . '/Views/partials/account_sidebar.php'; ?>
    <div>
      <h2 class="mb-3">Pesanan Saya</h2>
      <div class="d-flex gap-1 mb-3" style="flex-wrap:wrap;">
        <a href="/akun/pesanan" class="btn btn-<?= !$currentStatus?'primary':'outline' ?> btn-sm">Semua</a>
        <?php foreach (['pending'=>'Menunggu Bayar','confirmed'=>'Verifikasi','processing'=>'Diproses','shipped'=>'Dikirim','delivered'=>'Selesai','cancelled'=>'Batal'] as $k=>$lbl): ?>
          <a href="/akun/pesanan?status=<?= $k ?>" class="btn btn-<?= $currentStatus===$k?'primary':'outline' ?> btn-sm"><?= $lbl ?></a>
        <?php endforeach; ?>
      </div>
      <?php if (!$orders): ?>
        <div class="empty-state"><div class="empty-icon"><i class="bi bi-bag"></i></div><h3>Belum ada pesanan</h3><a href="/katalog" class="btn btn-primary">Mulai Belanja</a></div>
      <?php else: ?>
        <div style="display:grid; gap:12px;">
        <?php foreach ($orders as $o): ?>
          <a href="/akun/pesanan/<?= (int)$o['id'] ?>" style="display:block; color:inherit;">
            <div class="admin-card" style="margin:0; transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--color-primary-400)'" onmouseout="this.style.borderColor='var(--border-color)'">
              <div class="d-flex justify-between align-center mb-1">
                <div>
                  <div class="fw-bold"><?= e($o['order_number']) ?></div>
                  <div class="text-xs text-muted"><?= date('d M Y H:i', strtotime($o['created_at'])) ?></div>
                </div>
                <?= order_status_badge($o['status']) ?>
              </div>
              <div class="d-flex justify-between align-center">
                <span class="text-sm text-muted"><?= e($o['ship_city']) ?> · <?= e($o['courier']) ?></span>
                <span class="fw-bold text-primary"><?= rupiah($o['total']) ?></span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
