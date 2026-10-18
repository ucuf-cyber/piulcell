<div class="admin-card">
  <h3>Atribut Varian: <?= e($product['name']) ?></h3>
  <p class="text-muted text-sm">Definisikan atribut & nilainya (mis: RAM = 6GB, 8GB, 12GB). Lalu generate kombinasi varian.</p>
  <div id="attrEditor">
    <div id="attrList"></div>
    <button class="btn btn-outline btn-sm" onclick="addAttr()" type="button"><i class="bi bi-plus"></i> Tambah Atribut</button>
  </div>
  <form method="POST" class="mt-3">
    <?= csrf_field() ?>
    <input type="hidden" name="attributes_json" id="attrJson">
    <button class="btn btn-primary" onclick="document.getElementById('attrJson').value = JSON.stringify(getAttrs())">Simpan Atribut</button>
  </form>
</div>

<div class="admin-card">
  <div class="d-flex justify-between align-center mb-2">
    <h3 class="mb-0">Daftar Varian (<?= count($variants) ?>)</h3>
    <button class="btn btn-primary btn-sm" onclick="openVariantModal()" type="button"><i class="bi bi-plus"></i> Tambah Varian Manual</button>
  </div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>SKU</th><th>Kombinasi</th><th>Harga</th><th>Promo</th><th>Stok</th><th>Aksi</th></tr></thead>
      <tbody id="variantTbody">
        <?php foreach ($variants as $v): ?>
          <tr data-id="<?= (int)$v['id'] ?>">
            <td class="fw-medium"><?= e($v['sku']) ?></td>
            <td class="text-sm"><?= e(implode(' / ', array_values($v['combination']))) ?></td>
            <td><?= rupiah($v['price']) ?></td>
            <td><?= $v['price_promo'] > 0 ? rupiah($v['price_promo']) : '-' ?></td>
            <td><?= (int)$v['stock'] ?></td>
            <td>
              <button class="btn btn-outline btn-sm" onclick='editVariant(<?= json_encode($v) ?>)' type="button"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-outline btn-sm text-danger" onclick="deleteVariant(<?= (int)$v['id'] ?>)" type="button"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Variant modal -->
<div id="variantModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:200; align-items:center; justify-content:center; padding:20px;">
  <div class="admin-card" style="max-width:520px; width:100%; max-height:90vh; overflow-y:auto;">
    <h3>Tambah / Edit Varian</h3>
    <input type="hidden" id="varId" value="">
    <div class="form-group"><label class="form-label">SKU</label><input class="form-control" id="varSku" required></div>
    <div class="form-group"><label class="form-label">Kombinasi (JSON, mis: {"RAM":"8GB","Storage":"256GB"})</label><textarea class="form-control" id="varCombo" rows="2"></textarea></div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">Harga</label><input class="form-control" id="varPrice" type="number"></div>
      <div class="form-group"><label class="form-label">Harga Promo (opsional)</label><input class="form-control" id="varPromo" type="number"></div>
    </div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">Stok</label><input class="form-control" id="varStock" type="number" value="0"></div>
      <div class="form-group"><label class="form-label">Berat (gram)</label><input class="form-control" id="varWeight" type="number" value="300"></div>
    </div>
    <button class="btn btn-primary" onclick="saveVariant()">Simpan</button>
    <button class="btn btn-ghost" onclick="document.getElementById('variantModal').style.display='none'">Tutup</button>
  </div>
</div>

<script>
const PRODUCT_ID = <?= (int)$product['id'] ?>;
let attrs = <?= json_encode(array_map(fn($a) => ['name' => $a['attribute'], 'values' => $a['values']], $attrs)) ?>;

function renderAttrs() {
  document.getElementById('attrList').innerHTML = attrs.map((a, i) => `
    <div class="d-grid grid-2 gap-2 mb-2" style="grid-template-columns: 1fr 2fr auto;">
      <input class="form-control" placeholder="Nama atribut (RAM, Storage, Warna)" value="${a.name}" onchange="attrs[${i}].name=this.value">
      <input class="form-control" placeholder="Nilai, pisah koma: 6GB,8GB,12GB" value="${(a.values||[]).join(',')}" onchange="attrs[${i}].values=this.value.split(',').map(s=>s.trim()).filter(Boolean)">
      <button class="btn btn-outline btn-sm text-danger" onclick="attrs.splice(${i},1); renderAttrs()" type="button"><i class="bi bi-trash"></i></button>
    </div>
  `).join('');
}
function addAttr() { attrs.push({name:'', values:[]}); renderAttrs(); }
function getAttrs() { return attrs.filter(a => a.name && a.values.length); }
renderAttrs();

function openVariantModal() {
  document.getElementById('varId').value = '';
  document.getElementById('varSku').value = '';
  document.getElementById('varCombo').value = '';
  document.getElementById('varPrice').value = '';
  document.getElementById('varPromo').value = '';
  document.getElementById('varStock').value = '0';
  document.getElementById('varWeight').value = '300';
  document.getElementById('variantModal').style.display = 'flex';
}
function editVariant(v) {
  document.getElementById('varId').value = v.id;
  document.getElementById('varSku').value = v.sku;
  document.getElementById('varCombo').value = JSON.stringify(v.combination);
  document.getElementById('varPrice').value = v.price;
  document.getElementById('varPromo').value = v.price_promo || '';
  document.getElementById('varStock').value = v.stock;
  document.getElementById('varWeight').value = v.weight;
  document.getElementById('variantModal').style.display = 'flex';
}
async function saveVariant() {
  const data = {
    product_id: PRODUCT_ID,
    id: document.getElementById('varId').value,
    sku: document.getElementById('varSku').value,
    combination: document.getElementById('varCombo').value,
    price: document.getElementById('varPrice').value,
    price_promo: document.getElementById('varPromo').value,
    stock: document.getElementById('varStock').value,
    weight: document.getElementById('varWeight').value,
  };
  const r = await postJSON('/admin/products/variant/save', data);
  if (r.success) { showToast('Tersimpan', 'success'); setTimeout(() => location.reload(), 500); }
  else showToast(r.message || 'Gagal', 'error');
}
async function deleteVariant(id) {
  if (!confirm('Hapus varian ini?')) return;
  await postJSON('/admin/products/variant/delete/' + id, {});
  location.reload();
}
</script>
