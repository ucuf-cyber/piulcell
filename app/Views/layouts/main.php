<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? ($settings['store_name'] ?? 'Piul Cell')) ?></title>
<meta name="description" content="<?= e($pageDesc ?? ($settings['store_tagline'] ?? 'Toko HP Terpercaya')) ?>">
<meta name="csrf-token" content="<?= csrf_token() ?>">
<link rel="icon" type="image/svg+xml" href="/assets/img/placeholder.svg">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="/assets/css/main.css">
<link rel="stylesheet" href="/assets/css/animations.css">
<script src="/assets/js/app.js"></script>
</head>
<body>
<?php include APP_PATH . '/Views/partials/navbar.php'; ?>

<?php foreach (flash_messages() as $f): ?>
<div class="container" style="padding-top:16px;">
  <div class="alert alert-<?= $f['type'] ?>"><i class="bi bi-info-circle"></i> <?= e($f['msg']) ?></div>
</div>
<?php endforeach; ?>

<main>
<?= $content ?>
</main>

<?php include APP_PATH . '/Views/partials/footer.php'; ?>

<?php if (!empty($pageScripts)) echo $pageScripts; ?>
</body>
</html>
