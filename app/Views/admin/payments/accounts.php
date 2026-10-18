<?php /** @var array $accounts; ?array $editing */ ?>
<div class="admin-card">
  <h3><i class="bi bi-bank text-primary"></i> Rekening & QRIS Toko</h3>
  <p class="text-muted text-sm">Rekening yang akan ditampilkan ke pembeli saat memilih Transfer Bank / QRIS.</p>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Tipe</th><th>Bank</th><th>No. Rekening</th><th>Nama Pemilik</th><th>QRIS</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($accounts as $a): ?>
      <tr>
        <td><span class="badge badge-<?= $a['type']==='qris' ? 'success' : 'primary' ?>"><?= $a['type']==='qris' ? 'QRIS' : 'Bank' ?></span></td>
        <td class="fw-medium"><?= e($a['bank_name'] ?: '—') ?></td>
        <td class="bank-account-no"><?= e($a['account_number'] ?: '—') ?></td>
        <td><?= e($a['account_name']) ?></td>
        <td><?php if ($a['qris_image']): ?><img src="<?= upload($a['qris_image']) ?>" style="width:48px; border-radius:6px;"><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
        <td><?= (int)$a['sort_order'] ?></td>
        <td><?= $a['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?></td>
        <td>
          <a href="/admin/payment-accounts?edit=<?= (int)$a['id'] ?>#editForm" class="btn btn-outline btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
          <form method="POST" action="/admin/payment-accounts/delete/<?= (int)$a['id'] ?>" style="display:inline;" onsubmit="return confirm('Hapus rekening ini?')"><?= csrf_field() ?><button class="btn btn-outline btn-sm text-danger" title="Hapus"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    <?php endforeach; if (!$accounts): ?><tr><td colspan="8" class="text-center text-muted">Belum ada rekening. Tambah di bawah ↓</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>

<div class="admin-card" id="editForm">
  <h3><?= $editing ? '<i class="bi bi-pencil-square text-primary"></i> Edit Rekening / QRIS' : '<i class="bi bi-plus-circle text-primary"></i> Tambah Rekening / QRIS Baru' ?></h3>
  <form method="POST" action="/admin/payment-accounts/save" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group">
        <label class="form-label">Tipe</label>
        <select class="form-control" name="type" id="acType" onchange="toggleAc()">
          <option value="transfer_bank" <?= ($editing['type'] ?? '')==='transfer_bank'?'selected':'' ?>>Transfer Bank</option>
          <option value="qris" <?= ($editing['type'] ?? '')==='qris'?'selected':'' ?>>QRIS Statis (scan kode)</option>
        </select>
      </div>
      <div class="form-group" id="bankF"><label class="form-label">Nama Bank</label><input class="form-control" name="bank_name" placeholder="BCA / BRI / Mandiri / BNI" value="<?= e($editing['bank_name'] ?? '') ?>"></div>
    </div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group" id="noF"><label class="form-label">Nomor Rekening</label><input class="form-control" name="account_number" placeholder="1234567890" value="<?= e($editing['account_number'] ?? '') ?>"></div>
      <div class="form-group"><label class="form-label">Nama Pemilik Rekening</label><input class="form-control" name="account_name" placeholder="Piul Cell Store" value="<?= e($editing['account_name'] ?? 'Piul Cell Store') ?>" required></div>
    </div>
    <div class="form-group" id="qrisF" style="display:none;">
      <label class="form-label">Upload Gambar QR Code QRIS <?= !empty($editing['qris_image']) ? '(biarkan kosong agar tidak ganti)' : '' ?></label>
      <input type="file" name="qris_image" accept=".jpg,.jpeg,.png,.webp">
      <?php if (!empty($editing['qris_image'])): ?>
        <div class="mt-1"><img src="<?= upload($editing['qris_image']) ?>" style="max-width:160px; border-radius:8px; border:1px solid var(--border-color);"></div>
      <?php endif; ?>
    </div>
    <div class="d-grid grid-2 gap-2">
      <div class="form-group"><label class="form-label">Urutan Tampil</label><input type="number" class="form-control" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>"></div>
      <div class="form-group">
        <label class="form-label">Status</label><br>
        <label class="switch">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" value="1" <?= !isset($editing) || !empty($editing['is_active']) ? 'checked' : '' ?>>
          <span class="slider-bg"></span>
        </label>
        <span style="margin-left:10px;" class="text-sm">Aktif</span>
      </div>
    </div>
    <button class="btn btn-primary"><i class="bi bi-check2"></i> <?= $editing ? 'Update' : 'Simpan' ?></button>
    <?php if ($editing): ?><a href="/admin/payment-accounts" class="btn btn-ghost">Batal Edit</a><?php endif; ?>
  </form>
</div>

<script>
function toggleAc() {
  const type = document.getElementById('acType').value;
  document.getElementById('bankF').style.display = type === 'transfer_bank' ? '' : 'none';
  document.getElementById('noF').style.display = type === 'transfer_bank' ? '' : 'none';
  document.getElementById('qrisF').style.display = type === 'qris' ? '' : 'none';
}
toggleAc();
<?php if ($editing): ?>
document.getElementById('editForm').scrollIntoView({behavior:'smooth', block:'start'});
<?php endif; ?>
</script>
