<?php /** @var array $items, $addresses; int $subtotal; int $codMax, $codEnabled, $transferEnabled, $qrisEnabled, $freeMin */ ?>
<section style="background: var(--bg-surface); padding: 20px 0; border-bottom: 1px solid var(--border-color);">
  <div class="container"><h1 style="font-size: var(--text-2xl); margin: 0;">Checkout</h1></div>
</section>

<div class="container">
  <div class="checkout-layout">
    <div class="checkout-form">
      <!-- Address -->
      <h3 style="font-size: var(--text-lg); margin-bottom:16px;"><i class="bi bi-geo-alt"></i> Alamat Pengiriman</h3>
      <?php if ($addresses): ?>
        <div class="mb-3">
          <?php foreach ($addresses as $i => $a): ?>
            <label class="payment-method-card <?= $i===0?'active':'' ?>" style="display:flex; align-items:start;">
              <input type="radio" name="address_id" value="<?= (int)$a['id'] ?>" <?= $i===0?'checked':'' ?> onchange="document.querySelectorAll('label.payment-method-card').forEach(l=>l.classList.remove('active')); this.closest('label').classList.add('active'); setShipFromAddress(<?= e(json_encode($a)) ?>)">
              <div style="flex:1;">
                <div class="fw-bold"><?= e($a['label']) ?> — <?= e($a['recipient']) ?> · <?= e($a['phone']) ?> <?php if ($a['is_primary']): ?><span class="badge badge-primary">Utama</span><?php endif; ?></div>
                <div class="text-sm text-muted"><?= e($a['address_detail']) ?>, <?= e($a['district']) ?>, <?= e($a['city']) ?>, <?= e($a['province']) ?> <?= e($a['postal_code']) ?></div>
              </div>
            </label>
          <?php endforeach; ?>
        </div>
        <p class="text-sm"><a href="/akun/alamat/tambah">+ Tambah alamat baru</a></p>
      <?php else: ?>
        <div class="alert alert-warning">Anda belum punya alamat. <a href="/akun/alamat/tambah">Tambah alamat dulu</a></div>
      <?php endif; ?>

      <!-- Shipping -->
      <h3 style="font-size: var(--text-lg); margin-top:24px; margin-bottom:16px;"><i class="bi bi-truck"></i> Pengiriman</h3>
      <div id="shippingResult" class="alert alert-info"><i class="bi bi-info-circle"></i> Pilih alamat untuk menghitung ongkir.</div>

      <!-- Voucher -->
      <h3 style="font-size: var(--text-lg); margin-top:24px; margin-bottom:16px;"><i class="bi bi-ticket-perforated"></i> Voucher</h3>
      <div class="d-flex gap-2 mb-3">
        <input type="text" id="voucherInput" class="form-control" placeholder="Masukkan kode voucher (PIULCELL10 / GRATIS50 / FREEONGKIR)">
        <button type="button" class="btn btn-outline" onclick="applyVoucher()">Pakai</button>
      </div>
      <div id="voucherResult"></div>

      <!-- Notes -->
      <h3 style="font-size: var(--text-lg); margin-top:24px; margin-bottom:16px;"><i class="bi bi-chat-left-text"></i> Catatan</h3>
      <textarea id="notesInput" class="form-control" placeholder="Catatan untuk admin (opsional)" rows="2"></textarea>

      <!-- Payment Method -->
      <h3 style="font-size: var(--text-lg); margin-top:24px; margin-bottom:16px;"><i class="bi bi-credit-card"></i> Metode Pembayaran</h3>
      <?php if ($transferEnabled): ?>
        <label class="payment-method-card active" onclick="selectPayment('transfer_bank', this)">
          <input type="radio" name="payment_method" value="transfer_bank" checked>
          <span class="payment-method-icon"><i class="bi bi-bank"></i></span>
          <div style="flex:1;"><div class="fw-bold">Transfer Bank</div><div class="text-sm text-muted">BCA, BRI, Mandiri, BNI. Upload bukti maks 1×24 jam.</div></div>
        </label>
      <?php endif; ?>
      <?php if ($qrisEnabled): ?>
        <label class="payment-method-card" onclick="selectPayment('qris', this)">
          <input type="radio" name="payment_method" value="qris">
          <span class="payment-method-icon"><i class="bi bi-qr-code"></i></span>
          <div style="flex:1;"><div class="fw-bold">QRIS</div><div class="text-sm text-muted">Scan QR pakai GoPay, OVO, Dana, ShopeePay, m-Banking.</div></div>
        </label>
      <?php endif; ?>
      <?php if ($codEnabled): ?>
        <label class="payment-method-card" id="codLabel" onclick="selectPayment('cod', this)">
          <input type="radio" name="payment_method" value="cod">
          <span class="payment-method-icon"><i class="bi bi-cash"></i></span>
          <div style="flex:1;"><div class="fw-bold">Bayar di Tempat (COD)</div><div class="text-sm text-muted">Hanya untuk jarak ≤ <?= (int)$codMax ?> km dari toko.</div></div>
        </label>
      <?php endif; ?>
    </div>

    <div class="summary-card">
      <div class="summary-title">Ringkasan Pesanan</div>
      <?php foreach ($items as $it): ?>
        <div class="d-flex gap-1 mb-1" style="font-size: var(--text-sm);">
          <span style="flex:1; color: var(--text-secondary);"><?= e($it['name']) ?> × <?= (int)$it['quantity'] ?></span>
          <span><?= rupiah($it['line_total']) ?></span>
        </div>
      <?php endforeach; ?>
      <hr style="border:none; border-top:1px solid var(--border-color); margin:12px 0;">
      <div class="summary-row"><span>Subtotal</span><span class="summary-value" id="sumSubtotal"><?= rupiah($subtotal) ?></span></div>
      <div class="summary-row"><span>Ongkir</span><span class="summary-value" id="sumShipping">Rp 0</span></div>
      <div class="summary-row" id="sumDiscountRow" style="display:none;"><span>Diskon Voucher</span><span class="summary-value text-success" id="sumDiscount">-Rp 0</span></div>
      <div class="summary-row total"><span>Total</span><span class="summary-value" id="sumTotal"><?= rupiah($subtotal) ?></span></div>
      <button type="button" class="btn btn-primary btn-block btn-lg mt-3" onclick="placeOrder()" id="placeBtn">Buat Pesanan <i class="bi bi-arrow-right"></i></button>
      <p class="text-xs text-muted text-center mt-2">Dengan menekan tombol di atas, Anda menyetujui syarat & ketentuan</p>
    </div>
  </div>
</div>

<script>
const SUBTOTAL = <?= (int)$subtotal ?>;
const COD_MAX = <?= (int)$codMax ?>;
const FREE_MIN = <?= (int)$freeMin ?>;
let SHIPPING = 0;
let DISTANCE = 999;
let VOUCHER = null;
let CURRENT_ADDR = <?= $addresses ? json_encode($addresses[0]) : 'null' ?>;

function rp(n) { return 'Rp ' + Number(n||0).toLocaleString('id-ID'); }

function setShipFromAddress(addr) {
  CURRENT_ADDR = addr;
  if (addr.latitude && addr.longitude) {
    calcShipping(parseFloat(addr.latitude), parseFloat(addr.longitude));
  } else {
    // Try to geocode address client-side via Nominatim as a fallback
    document.getElementById('shippingResult').className = 'alert alert-info';
    document.getElementById('shippingResult').innerHTML = '<i class="bi bi-arrow-repeat"></i> Mencari koordinat alamat…';
    const q = [addr.address_detail || '', addr.district || '', addr.city || '', addr.province || '', 'Indonesia'].filter(Boolean).join(', ');
    (async () => {
      try {
        const url = 'https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q) + '&format=json&addressdetails=1&limit=1&countrycodes=id';
        const res = await fetch(url, { headers: { 'User-Agent': 'PiulCell/1.0' } });
        const data = await res.json();
        if (data && data.length) {
          const it = data[0];
          const lat = parseFloat(it.lat), lng = parseFloat(it.lon);
          // call shipping calc with found coords
          calcShipping(lat, lng);
        } else {
          document.getElementById('shippingResult').className = 'alert alert-danger';
          document.getElementById('shippingResult').innerHTML = '<i class="bi bi-exclamation-circle"></i> Alamat ini belum punya koordinat. Edit alamat dan pilih hasil pencarian supaya koordinat tersimpan.';
          SHIPPING = 0; updateTotals();
        }
      } catch (e) {
        console.error(e);
        document.getElementById('shippingResult').className = 'alert alert-danger';
        document.getElementById('shippingResult').innerHTML = '<i class="bi bi-exclamation-circle"></i> Gagal mencari koordinat alamat.';
        SHIPPING = 0; updateTotals();
      }
    })();
  }
}

async function calcShipping(lat, lng) {
  document.getElementById('shippingResult').className = 'alert alert-info';
  document.getElementById('shippingResult').innerHTML = '<i class="bi bi-arrow-repeat"></i> Menghitung ongkir…';
  const r = await postJSON('/shipping/calculate', { lat, lng, subtotal: SUBTOTAL });
  if (!r.success) {
    document.getElementById('shippingResult').className = 'alert alert-danger';
    document.getElementById('shippingResult').innerHTML = r.message;
    return;
  }
  SHIPPING = Number(r.cost);
  DISTANCE = Number(r.distance_km);
  document.getElementById('shippingResult').className = 'alert alert-info';
  document.getElementById('shippingResult').innerHTML = `<i class="bi bi-truck"></i> <strong>${r.zone_name}</strong> · ${r.distance_km} km · ${r.courier} · ETD ${r.etd} → <strong>${r.cost_display}</strong>`;
  // COD availability
  const codL = document.getElementById('codLabel');
  if (codL) {
    if (!r.cod_available) {
      codL.style.opacity = 0.4; codL.style.pointerEvents = 'none';
      codL.querySelector('.text-muted').textContent = `Tidak tersedia untuk jarak ${r.distance_km} km (maks ${COD_MAX} km)`;
    } else {
      codL.style.opacity = 1; codL.style.pointerEvents = '';
    }
  }
  updateTotals();
}

function updateTotals() {
  let shipping = SHIPPING;
  let discount = 0;
  if (VOUCHER) {
    if (VOUCHER.free_shipping) { shipping = 0; }
    else { discount = Number(VOUCHER.discount); }
  }
  document.getElementById('sumShipping').textContent = rp(shipping);
  if (discount > 0) {
    document.getElementById('sumDiscountRow').style.display = '';
    document.getElementById('sumDiscount').textContent = '-' + rp(discount);
  } else {
    document.getElementById('sumDiscountRow').style.display = 'none';
  }
  const total = Math.max(0, SUBTOTAL + shipping - discount);
  document.getElementById('sumTotal').textContent = rp(total);
}

async function applyVoucher() {
  const code = document.getElementById('voucherInput').value.trim().toUpperCase();
  if (!code) return;
  const r = await postJSON('/voucher/validate', { code, subtotal: SUBTOTAL });
  if (!r.success) {
    VOUCHER = null;
    document.getElementById('voucherResult').innerHTML = `<div class="alert alert-danger">${r.message}</div>`;
    updateTotals();
    return;
  }
  VOUCHER = r.voucher;
  let msg = `<i class="bi bi-check-circle-fill text-success"></i> Voucher <strong>${r.voucher.code}</strong> dipakai!`;
  if (r.voucher.free_shipping) msg += ' Gratis ongkir.';
  else msg += ' Hemat ' + r.voucher.discount_display;
  document.getElementById('voucherResult').innerHTML = `<div class="alert alert-success">${msg}</div>`;
  updateTotals();
}

function selectPayment(method, el) {
  document.querySelectorAll('label.payment-method-card').forEach(l => l.classList.remove('active'));
  el.classList.add('active');
  el.querySelector('input').checked = true;
}

async function placeOrder() {
  if (!CURRENT_ADDR) { showToast('Pilih alamat dulu', 'error'); return; }
  const method = document.querySelector('input[name=payment_method]:checked')?.value || 'transfer_bank';
  if (method === 'cod' && DISTANCE > COD_MAX) { showToast('COD tidak tersedia untuk jarak ini', 'error'); return; }
  const btn = document.getElementById('placeBtn');
  btn.disabled = true; btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Memproses…';
  const r = await postJSON('/checkout/process', {
    address_id: CURRENT_ADDR.id,
    shipping_cost: VOUCHER && VOUCHER.free_shipping ? 0 : SHIPPING,
    courier_name: 'Internal',
    voucher_code: VOUCHER ? VOUCHER.code : '',
    payment_method: method,
    notes: document.getElementById('notesInput').value,
  });
  if (r.success) {
    window.location.href = r.redirect;
  } else {
    showToast(r.message || 'Gagal', 'error');
    btn.disabled = false; btn.innerHTML = 'Buat Pesanan';
  }
}

// Auto-calc on load
if (CURRENT_ADDR) setShipFromAddress(CURRENT_ADDR);
</script>
