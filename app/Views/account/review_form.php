<div class="container" style="padding: 32px 0;">
  <div style="display:grid; grid-template-columns: 260px 1fr; gap: 32px;">
    <?php include APP_PATH . '/Views/partials/account_sidebar.php'; ?>
    <div class="admin-card">
      <h2>Tulis Ulasan</h2>
      <p class="text-muted text-sm">Produk: <strong><?= e($order_item['product_name']) ?></strong></p>
      <form method="POST" action="/akun/ulasan/<?= (int)$order_item['id'] ?>">
        <?= csrf_field() ?>
        <div class="form-group">
          <label class="form-label">Rating</label>
          <div id="ratingBox" style="font-size:28px; color:#ffc107; cursor:pointer;">
            <?php for ($i=1;$i<=5;$i++): ?><i class="bi bi-star-fill" data-r="<?= $i ?>"></i><?php endfor; ?>
          </div>
          <input type="hidden" name="rating" id="ratingVal" value="5">
        </div>
        <div class="form-group"><label class="form-label">Judul (opsional)</label><input class="form-control" name="title"></div>
        <div class="form-group"><label class="form-label">Ulasan Anda</label><textarea class="form-control" name="body" rows="4" required></textarea></div>
        <button class="btn btn-primary">Kirim Ulasan</button>
      </form>
    </div>
  </div>
</div>
<script>
document.querySelectorAll('#ratingBox i').forEach(s => {
  s.addEventListener('click', () => {
    const r = parseInt(s.dataset.r);
    document.getElementById('ratingVal').value = r;
    document.querySelectorAll('#ratingBox i').forEach((el, i) => el.style.color = i < r ? '#ffc107' : '#ddd');
  });
});
</script>
