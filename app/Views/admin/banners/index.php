<div class="admin-card">
  <div class="d-flex justify-between align-center mb-3"><div></div><a href="/admin/banners/create" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Banner</a></div>
  <div class="d-grid grid-2 gap-3">
    <?php foreach ($list as $b): ?>
      <div class="admin-card" style="margin:0;">
        <img src="<?= product_image($b['image_desktop'], 'banners') ?>" alt="" style="width:100%; aspect-ratio: 16/9; object-fit:cover; border-radius: var(--radius-md); margin-bottom: 12px;">
        <div class="fw-bold"><?= e($b['title']) ?> <span class="badge badge-secondary"><?= e($b['position']) ?></span> <?= $b['is_active']?'<span class="badge badge-success">Aktif</span>':'' ?></div>
        <div class="text-sm text-muted mb-2"><?= e($b['subtitle']) ?></div>
        <div class="d-flex gap-1">
          <a href="/admin/banners/edit/<?= (int)$b['id'] ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i> Edit</a>
          <form method="POST" action="/admin/banners/delete/<?= (int)$b['id'] ?>" style="display:inline;" onsubmit="return confirm('Hapus?')"><?= csrf_field() ?><button class="btn btn-outline btn-sm text-danger"><i class="bi bi-trash"></i></button></form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
