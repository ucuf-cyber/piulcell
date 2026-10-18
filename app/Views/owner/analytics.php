<div class="admin-card">
  <h3>Funnel Konversi</h3>
  <div class="d-grid grid-2 gap-2" style="grid-template-columns: repeat(4, 1fr);">
    <?php $items = [['Views', $funnel['view'], 'eye'], ['Add to Cart', $funnel['add_cart'], 'bag-plus'], ['Order Dibuat', $funnel['orders'], 'receipt'], ['Berbayar', $funnel['paid'], 'check-circle']]; ?>
    <?php foreach ($items as $it): ?>
      <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-<?= $it[2] ?>"></i></div>
        <div class="stat-label"><?= $it[0] ?></div>
        <div class="stat-value"><?= number_format($it[1]) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<div class="admin-card">
  <h3>Analitik Produk (Top 20 by View)</h3>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Produk</th><th>View</th><th>Terjual</th><th>Conversion</th><th>Ulasan</th></tr></thead>
    <tbody>
    <?php foreach ($productAnalytics as $p): $conv = $p['view_count'] > 0 ? round($p['sold_count']/$p['view_count']*100, 1) : 0; ?>
      <tr>
        <td><?= e($p['name']) ?></td>
        <td><?= number_format($p['view_count']) ?></td>
        <td><?= number_format($p['sold_count']) ?></td>
        <td><span class="badge badge-<?= $conv > 5 ? 'success' : 'secondary' ?>"><?= $conv ?>%</span></td>
        <td><?= $p['review_count'] ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
