<aside class="admin-sidebar">
  <div class="brand-block">
    <div class="brand-name"><i class="bi bi-shop"></i> PIUL CELL</div>
    <div class="brand-sub"><?= ucfirst($user['role']) ?> Panel</div>
  </div>
  <ul class="admin-nav">
    <?php if ($user['role'] === 'owner'): ?>
      <li class="section-label">Owner</li>
      <li><a href="/owner/dashboard" class="<?= active_when('/owner') ?>"><i class="bi bi-graph-up-arrow"></i> Owner Dashboard</a></li>
      <li><a href="/owner/analytics" class="<?= active_when('/owner/analytics') ?>"><i class="bi bi-bar-chart"></i> Analitik</a></li>
      <li><a href="/owner/financial" class="<?= active_when('/owner/financial') ?>"><i class="bi bi-cash-stack"></i> Keuangan</a></li>
      <li><a href="/owner/reports" class="<?= active_when('/owner/reports') ?>"><i class="bi bi-file-earmark-text"></i> Laporan</a></li>
    <?php else: ?>
      <li class="section-label">Utama</li>
      <li><a href="/admin/dashboard" class="<?= active_when('/admin/dashboard') ?: active_when('/admin', 'exact') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>

      <li class="section-label">Katalog</li>
      <li><a href="/admin/products" class="<?= active_when('/admin/products') ?>"><i class="bi bi-box-seam"></i> Produk</a></li>
      <li><a href="/admin/brands" class="<?= active_when('/admin/brands') ?>"><i class="bi bi-tags"></i> Brand</a></li>
      <li><a href="/admin/categories" class="<?= active_when('/admin/categories') ?>"><i class="bi bi-list-nested"></i> Kategori</a></li>

      <li class="section-label">Penjualan</li>
      <li><a href="/admin/orders" class="<?= active_when('/admin/orders') ?>"><i class="bi bi-receipt"></i> Pesanan</a></li>
      <li><a href="/admin/payments/pending" class="<?= active_when('/admin/payments') ?>"><i class="bi bi-cash-coin"></i> Verifikasi Bayar</a></li>
      <li><a href="/admin/payment-accounts" class="<?= active_when('/admin/payment-accounts') ?>"><i class="bi bi-bank"></i> Rekening Toko</a></li>

      <li class="section-label">Marketing</li>
      <li><a href="/admin/vouchers" class="<?= active_when('/admin/vouchers') ?>"><i class="bi bi-ticket-perforated"></i> Voucher</a></li>
      <li><a href="/admin/banners" class="<?= active_when('/admin/banners') ?>"><i class="bi bi-image"></i> Banner</a></li>
      <li><a href="/admin/reviews" class="<?= active_when('/admin/reviews') ?>"><i class="bi bi-star"></i> Ulasan</a></li>

      <li class="section-label">Operasional</li>
      <li><a href="/admin/customers" class="<?= active_when('/admin/customers') ?>"><i class="bi bi-people"></i> Pelanggan</a></li>
      <li><a href="/admin/shipping-zones" class="<?= active_when('/admin/shipping-zones') ?>"><i class="bi bi-truck"></i> Zona Pengiriman</a></li>
      <li><a href="/admin/settings" class="<?= active_when('/admin/settings') ?>"><i class="bi bi-gear"></i> Pengaturan Toko</a></li>
      <li><a href="/admin/users" class="<?= active_when('/admin/users') ?>"><i class="bi bi-shield-lock"></i> User Admin</a></li>
    <?php endif; ?>
  </ul>
</aside>
