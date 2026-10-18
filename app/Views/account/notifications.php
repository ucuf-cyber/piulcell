<div class="container" style="padding: 32px 0;">
  <div style="display:grid; grid-template-columns: 260px 1fr; gap: 32px;">
    <?php include APP_PATH . '/Views/partials/account_sidebar.php'; ?>
    <div>
      <h2 class="mb-3">Notifikasi</h2>
      <?php if (!$notifs): ?>
        <div class="empty-state"><div class="empty-icon"><i class="bi bi-bell"></i></div><h3>Tidak ada notifikasi</h3></div>
      <?php else: ?>
        <div style="display:grid; gap:8px;">
          <?php foreach ($notifs as $n): ?>
            <a href="<?= e($n['url'] ?: '#') ?>" class="admin-card" style="margin:0; color: inherit; display:flex; gap: 12px; <?= $n['is_read'] ? 'opacity:0.7;' : '' ?>">
              <div style="width:40px; height:40px; border-radius:50%; background: var(--color-primary-50); color: var(--color-primary-500); display:flex; align-items:center; justify-content:center;"><i class="bi bi-bell"></i></div>
              <div style="flex:1;">
                <div class="fw-bold"><?= e($n['title']) ?></div>
                <div class="text-sm text-muted"><?= e($n['message']) ?></div>
                <div class="text-xs text-muted mt-1"><?= time_ago($n['created_at']) ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
