<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Admin · Piul Cell') ?></title>
<meta name="csrf-token" content="<?= csrf_token() ?>">
<link rel="icon" type="image/svg+xml" href="/assets/img/placeholder.svg">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="/assets/css/main.css">
<link rel="stylesheet" href="/assets/css/animations.css">
<script src="/assets/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="admin-layout">
  <?php include APP_PATH . '/Views/partials/sidebar_admin.php'; ?>
  <div class="admin-main">
    <div class="admin-header">
      <div class="d-flex align-center gap-2">
        <button class="nav-icon-btn hide-md" onclick="document.querySelector('.admin-sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
        <h1 class="admin-page-title"><?= e($pageTitle ?? 'Dashboard') ?></h1>
      </div>
      <div class="d-flex align-center gap-1">
        <button class="nav-icon-btn" id="themeToggle" onclick="toggleTheme()" title="Mode gelap/terang"><i class="bi bi-moon"></i></button>
        <a href="/" class="btn btn-outline btn-sm"><i class="bi bi-shop"></i> Lihat Toko</a>
        <div class="user-dropdown">
          <button class="nav-icon-btn" onclick="toggleUserMenu(event)"><i class="bi bi-person-circle"></i></button>
          <div class="user-menu" id="userMenu">
            <div style="padding:10px 16px; border-bottom:1px solid var(--border-color);">
              <div class="fw-bold"><?= e($user['name']) ?></div>
              <div class="text-xs text-muted"><?= e($user['role']) ?></div>
            </div>
            <a href="/logout"><i class="bi bi-box-arrow-right"></i> Keluar</a>
          </div>
        </div>
      </div>
    </div>
    <?php foreach (flash_messages() as $f): ?>
      <div class="alert alert-<?= $f['type'] ?>"><i class="bi bi-info-circle"></i> <?= e($f['msg']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
  </div>
</div>
<?php if (!empty($pageScripts)) echo $pageScripts; ?>
</body>
</html>
