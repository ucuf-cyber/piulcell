<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <h5><?= e($settings['store_name'] ?? 'Piul Cell') ?></h5>
        <p style="opacity:0.7; font-size: var(--text-sm); margin-bottom:8px;"><?= e($settings['store_tagline'] ?? '') ?></p>
        <p style="opacity:0.7; font-size: var(--text-sm);"><i class="bi bi-geo-alt"></i> <?= e($settings['store_address'] ?? '') ?></p>
        <p style="opacity:0.7; font-size: var(--text-sm);"><i class="bi bi-telephone"></i> <?= e($settings['store_phone'] ?? '') ?></p>
        <div class="footer-social">
          <?php if (!empty($settings['social_instagram'])): ?><a href="<?= e($settings['social_instagram']) ?>" target="_blank"><i class="bi bi-instagram"></i></a><?php endif; ?>
          <?php if (!empty($settings['social_facebook'])): ?><a href="<?= e($settings['social_facebook']) ?>" target="_blank"><i class="bi bi-facebook"></i></a><?php endif; ?>
          <?php if (!empty($settings['social_tiktok'])): ?><a href="<?= e($settings['social_tiktok']) ?>" target="_blank"><i class="bi bi-tiktok"></i></a><?php endif; ?>
          <?php if (!empty($settings['store_whatsapp'])): ?><a href="https://wa.me/<?= e($settings['store_whatsapp']) ?>" target="_blank"><i class="bi bi-whatsapp"></i></a><?php endif; ?>
        </div>
      </div>
      <div>
        <h5>Belanja</h5>
        <a href="/katalog">Semua Produk</a>
        <a href="/katalog?kategori=smartphone">Smartphone</a>
        <a href="/katalog?kategori=tablet">Tablet</a>
        <a href="/katalog?kategori=aksesoris">Aksesoris</a>
        <a href="/katalog?promo=1">Promo</a>
      </div>
      <div>
        <h5>Informasi</h5>
        <a href="/page/tentang-kami">Tentang Kami</a>
        <a href="/page/cara-belanja">Cara Belanja</a>
        <a href="/page/kebijakan-pengiriman">Pengiriman</a>
        <a href="/page/kebijakan-pengembalian">Pengembalian</a>
        <a href="/page/faq">FAQ</a>
      </div>
      <div>
        <h5>Bantuan</h5>
        <a href="/page/hubungi-kami">Hubungi Kami</a>
        <a href="/akun/pesanan">Lacak Pesanan</a>
        <a href="/akun">Akun Saya</a>
        <?php if (!empty($settings['store_whatsapp'])): ?><a href="https://wa.me/<?= e($settings['store_whatsapp']) ?>" target="_blank">WhatsApp Kami</a><?php endif; ?>
      </div>
    </div>
    <div class="footer-bottom">
      © <?= date('Y') ?> <?= e($settings['store_name'] ?? 'Piul Cell') ?>. Semua hak dilindungi. <span style="opacity:0.5; margin-left:8px;">Pembayaran: Transfer Bank · QRIS · COD</span>
    </div>
  </div>
</footer>
