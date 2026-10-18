<div class="admin-card">
  <div class="d-flex justify-between align-center mb-3">
    <div></div>
    <a href="/admin/products/create" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Produk</a>
  </div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>#</th><th>Produk</th><th>Brand</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><img src="<?= product_image($p['thumbnail']) ?>" alt="" style="width:48px; height:48px; object-fit:contain; border-radius:8px; background:var(--bg-base);"></td>
          <td><div class="fw-bold"><?= e($p['name']) ?></div><div class="text-xs text-muted">Slug: <?= e($p['slug']) ?> · Terjual: <?= $p['sold_count'] ?></div></td>
          <td><?= e($p['brand_name']) ?></td>
          <td><?= e($p['category_name']) ?></td>
          <td><?= $p['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?> <?php if($p['is_featured']):?><span class="badge badge-primary">Featured</span><?php endif;?></td>
          <td>
            <a href="/admin/products/variants/<?= (int)$p['id'] ?>" class="btn btn-outline btn-sm" title="Varian"><i class="bi bi-layers"></i></a>
            <a href="/admin/products/edit/<?= (int)$p['id'] ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
            <a href="/produk/<?= e($p['slug']) ?>" class="btn btn-outline btn-sm" target="_blank"><i class="bi bi-eye"></i></a>
            <form method="POST" action="/admin/products/delete/<?= (int)$p['id'] ?>" style="display:inline;" onsubmit="return confirm('Hapus?')"><?= csrf_field() ?><button class="btn btn-outline btn-sm text-danger"><i class="bi bi-trash"></i></button></form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
