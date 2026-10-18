<?php /** @var array $order, $payment, $banks; ?array $qris */
$expire = $payment['expire_at'] ? strtotime($payment['expire_at']) : 0;
$isExpired = $expire && $expire < time();
$isCOD = $payment['payment_method'] === 'cod';
?>
<div class="payment-page container">
  <div class="text-center mb-4 success-burst">
    <div style="font-size: 64px; color: var(--color-primary-500); display:inline-block;" class="success-icon"><i class="bi bi-check-circle-fill"></i></div>
    <h1 class="success-title">Pesanan Berhasil Dibuat! 🎉</h1>
    <p class="text-muted">Order #<?= e($order['order_number']) ?> · <?= payment_method_label($payment['payment_method']) ?></p>
  </div>

  <?php if (!$isCOD && $payment['status'] !== 'verified'): ?>
    <div class="timer-card <?= $isExpired ? 'expired' : '' ?>" id="timerCard">
      <?php if ($isExpired): ?>
        <div>Batas pembayaran telah lewat</div>
        <div class="timer-display">EXPIRED</div>
      <?php else: ?>
        <div>Selesaikan pembayaran sebelum</div>
        <div class="timer-display" id="countdownTimer" data-expire="<?= $expire ?>">--:--:--</div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="admin-card">
    <div class="summary-row"><span>No. Pesanan</span><span class="summary-value"><strong><?= e($order['order_number']) ?></strong></span></div>
    <div class="summary-row"><span>Total Bayar</span><span class="summary-value" style="font-size:var(--text-2xl); color: var(--color-primary-500);"><strong><?= rupiah($order['total']) ?></strong></span></div>
    <div class="summary-row"><span>Status</span><span><?= order_status_badge($order['status']) ?></span></div>
  </div>

  <?php if ($payment['payment_method'] === 'transfer_bank'): ?>
    <div class="admin-card">
      <h3><i class="bi bi-bank"></i> Transfer Bank</h3>
      <p class="text-muted text-sm">Transfer tepat sesuai nominal. Pilih salah satu rekening di bawah.</p>
      <?php foreach ($banks as $b): ?>
        <div class="bank-item">
          <div class="bank-info">
            <div class="bank-logo"><?= e($b['bank_name']) ?></div>
            <div>
              <div class="bank-account-no"><?= e($b['account_number']) ?></div>
              <div class="text-xs text-muted">a.n. <?= e($b['account_name']) ?></div>
            </div>
          </div>
          <button class="btn btn-outline btn-sm" onclick="copyText('<?= e($b['account_number']) ?>', this)"><i class="bi bi-clipboard"></i> Salin</button>
        </div>
      <?php endforeach; ?>
    </div>
  <?php elseif ($payment['payment_method'] === 'qris' && $qris): ?>
    <div class="admin-card text-center">
      <h3><i class="bi bi-qr-code"></i> QRIS</h3>
      <p class="text-muted text-sm">Scan QR ini dengan aplikasi apapun: GoPay, OVO, Dana, ShopeePay, atau m-Banking.</p>
      <?php $qrisImage = strpos($qris['qris_image'] ?? '', '/') === false ? 'qris/' . $qris['qris_image'] : $qris['qris_image']; ?>
      <img class="qris-image" src="<?= upload($qrisImage) ?>" alt="QRIS Piul Cell">
      <p class="text-xs text-muted mt-2">Pastikan nominal yang dibayar sesuai dengan total pesanan.</p>
    </div>
  <?php elseif ($isCOD): ?>
    <div class="admin-card text-center">
      <h3><i class="bi bi-cash"></i> Bayar di Tempat (COD)</h3>
      <p>Siapkan uang tunai <strong><?= rupiah($order['total']) ?></strong> saat kurir tiba.</p>
      <p class="text-muted text-sm">Pesanan sedang diproses dan akan dikirim segera.</p>
    </div>
  <?php endif; ?>

  <?php if (!$isCOD): ?>
  <div class="admin-card">
    <h3><i class="bi bi-cloud-upload"></i> Upload Bukti Pembayaran</h3>
    <?php if ($payment['status'] === 'waiting_verification' && $payment['proof_image']): ?>
      <div class="alert alert-info"><i class="bi bi-info-circle"></i> Bukti sudah diupload. Menunggu verifikasi admin (maks 1×24 jam).</div>
      <img src="<?= upload($payment['proof_image']) ?>" alt="Bukti" style="max-width:240px; border-radius:8px; border:1px solid var(--border-color);">
    <?php elseif ($payment['status'] === 'verified'): ?>
      <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> Pembayaran sudah diverifikasi! Pesanan akan segera diproses.</div>
    <?php else: ?>
      <?php if ($payment['status'] === 'rejected' && $payment['reject_reason']): ?>
        <div class="alert alert-danger"><strong>Bukti ditolak:</strong> <?= e($payment['reject_reason']) ?>. Silakan upload ulang.</div>
      <?php endif; ?>
      <form id="proofForm" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="upload-proof-section">
          <i class="bi bi-cloud-arrow-up" style="font-size:48px; color: var(--color-primary-400);"></i>
          <p class="mt-2 mb-1">Klik untuk pilih file</p>
          <p class="form-help">Format: JPG, PNG, WebP, PDF. Maks 5MB.</p>
          <input type="file" name="proof" id="proofFile" accept=".jpg,.jpeg,.png,.webp,.pdf" required>
        </div>
        <textarea name="notes" class="form-control mt-2" placeholder="Catatan untuk admin (opsional)" rows="2"></textarea>
        <button type="submit" class="btn btn-primary btn-block btn-lg mt-3" id="uploadBtn"><i class="bi bi-cloud-upload"></i> Upload Bukti Bayar</button>
      </form>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="text-center mt-3">
    <a href="/akun/pesanan/<?= (int)$order['id'] ?>" class="btn btn-outline">Lihat Detail Pesanan</a>
    <a href="/katalog" class="btn btn-ghost">Lanjut Belanja</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
<script>
// Confetti on first load (only if user just placed the order)
(function(){
  const key = 'piul_confetti_<?= (int)$order['id'] ?>';
  if (!sessionStorage.getItem(key)) {
    sessionStorage.setItem(key, '1');
    setTimeout(() => {
      // Burst
      confetti({ particleCount: 90, spread: 75, origin: { y: 0.55 }, colors: ['#1e4d9b','#4a7fd4','#30d158','#ffd60a','#fff'] });
      setTimeout(() => confetti({ particleCount: 60, angle: 60, spread: 60, origin: { x: 0, y: 0.7 } }), 200);
      setTimeout(() => confetti({ particleCount: 60, angle: 120, spread: 60, origin: { x: 1, y: 0.7 } }), 350);
    }, 300);
  }
})();

// Countdown
const timer = document.getElementById('countdownTimer');
if (timer) {
  const expire = parseInt(timer.dataset.expire) * 1000;
  function tick() {
    const diff = expire - Date.now();
    if (diff <= 0) { timer.textContent = 'EXPIRED'; document.getElementById('timerCard').classList.add('expired'); return; }
    const h = Math.floor(diff/3600000);
    const m = Math.floor((diff%3600000)/60000);
    const s = Math.floor((diff%60000)/1000);
    timer.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
  }
  tick(); setInterval(tick, 1000);
}

// Upload proof
const form = document.getElementById('proofForm');
if (form) {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('uploadBtn');
    btn.disabled = true; btn.textContent = 'Mengupload…';
    const fd = new FormData(form);
    const r = await fetch('/payment/upload-proof/<?= (int)$order['id'] ?>', { method: 'POST', body: fd, credentials: 'same-origin' }).then(r => r.json());
    if (r.success) { showToast(r.message, 'success'); setTimeout(() => location.reload(), 800); }
    else { showToast(r.message || 'Gagal', 'error'); btn.disabled = false; btn.textContent = 'Upload Bukti Bayar'; }
  });
}

// Poll status if waiting verification
<?php if ($payment['status'] === 'waiting_verification'): ?>
setInterval(async () => {
  const r = await fetch('/payment/status/<?= (int)$order['id'] ?>').then(r => r.json()).catch(() => null);
  if (r && r.success && r.status !== 'waiting_verification') location.reload();
}, 15000);
<?php endif; ?>
</script>
