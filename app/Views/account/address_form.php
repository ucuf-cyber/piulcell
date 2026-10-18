<div class="container" style="padding: 32px 0;">
  <div style="display:grid; grid-template-columns: 260px 1fr; gap: 32px;">
    <?php include APP_PATH . '/Views/partials/account_sidebar.php'; ?>
    <div class="admin-card">
      <h2>Tambah Alamat</h2>
      <form method="POST" action="/akun/alamat/tambah">
        <?= csrf_field() ?>
        <div class="form-group"><label class="form-label">Label</label><input class="form-control" name="label" value="Rumah" required></div>
        <div class="d-grid grid-2 gap-2">
          <div class="form-group"><label class="form-label">Nama Penerima</label><input class="form-control" name="recipient" required></div>
          <div class="form-group"><label class="form-label">No. HP</label><input class="form-control" name="phone" required></div>
        </div>
        <div style="position:relative;" class="form-group">
          <label class="form-label">Cari Kota/Kecamatan (live search)</label>
          <input type="text" id="citySearch" class="form-control" placeholder="Ketik nama kota/kecamatan…" autocomplete="off">
          <div id="citySuggest" style="position:absolute; top:100%; left:0; right:0; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:8px; box-shadow:var(--shadow-md); display:none; max-height:280px; overflow-y:auto; z-index:10;"></div>
          <div class="form-help">Pilih hasil yang muncul untuk mendapatkan koordinat (otomatis untuk kalkulasi ongkir)</div>
        </div>
        <div class="d-grid grid-2 gap-2">
          <div class="form-group"><label class="form-label">Provinsi</label><input id="provIn" class="form-control" name="province" required></div>
          <div class="form-group"><label class="form-label">Kota</label><input id="cityIn" class="form-control" name="city" required></div>
        </div>
        <div class="d-grid grid-2 gap-2">
          <div class="form-group"><label class="form-label">Kecamatan</label><input id="distIn" class="form-control" name="district" required></div>
          <div class="form-group"><label class="form-label">Kode Pos</label><input class="form-control" name="postal_code" required></div>
        </div>
        <div class="form-group"><label class="form-label">Alamat Detail (jalan, RT/RW, dll)</label><textarea class="form-control" name="address_detail" rows="2" required></textarea></div>
        <input type="hidden" name="latitude" id="latIn">
        <input type="hidden" name="longitude" id="lngIn">
        <label class="text-sm"><input type="checkbox" name="is_primary" value="1"> Jadikan alamat utama</label>
        <div class="mt-3">
          <button class="btn btn-primary">Simpan Alamat</button>
          <a href="/akun/alamat" class="btn btn-ghost">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Live city search via Nominatim
let timer;
const input = document.getElementById('citySearch');
const box = document.getElementById('citySuggest');
input.addEventListener('input', () => {
  clearTimeout(timer);
  const q = input.value.trim();
  if (q.length < 3) { box.style.display = 'none'; return; }
  timer = setTimeout(async () => {
    const url = 'https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q + ', Indonesia') + '&format=json&addressdetails=1&limit=6&countrycodes=id';
    try {
      const res = await fetch(url, { headers: { 'User-Agent': 'PiulCell/1.0' } });
      const data = await res.json();
      if (!data.length) { box.innerHTML = '<div style="padding:10px 16px;color:var(--text-tertiary);">Tidak ditemukan</div>'; box.style.display = 'block'; return; }
      box.innerHTML = data.map(it => {
        const a = it.address || {};
        const label = [a.village||a.suburb, a.city_district||a.county, a.city||a.town||a.regency||a.state].filter(Boolean).join(', ') || it.display_name;
        return `<div style="padding:10px 16px;cursor:pointer;border-bottom:1px solid var(--border-color);font-size:var(--text-sm);" onclick='selectCity(${JSON.stringify(it)})'>${label}</div>`;
      }).join('');
      box.style.display = 'block';
    } catch (e) { console.error(e); }
  }, 400);
});
function selectCity(item) {
  const a = item.address || {};
  const city = a.city || a.town || a.regency || a.state || '';
  document.getElementById('cityIn').value = city;
  document.getElementById('provIn').value = a.state || '';
  document.getElementById('distIn').value = a.city_district || a.county || a.village || a.suburb || '';
  document.getElementById('latIn').value = item.lat;
  document.getElementById('lngIn').value = item.lon;
  input.value = `${document.getElementById('distIn').value}, ${city}`;
  box.style.display = 'none';
}
document.addEventListener('click', e => { if (!e.target.closest('#citySearch') && !e.target.closest('#citySuggest')) box.style.display = 'none'; });
</script>
