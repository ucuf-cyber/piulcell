<div class="admin-card">
  <div class="d-flex gap-1 mb-3">
    <a href="/admin/reviews?status=pending" class="btn btn-<?= $status==='pending'?'primary':'outline' ?> btn-sm">Menunggu Approval</a>
    <a href="/admin/reviews?status=approved" class="btn btn-<?= $status==='approved'?'primary':'outline' ?> btn-sm">Sudah Disetujui</a>
  </div>
  <?php if (!$list): ?>
    <div class="empty-state"><div class="empty-icon"><i class="bi bi-chat"></i></div><h3>Tidak ada ulasan</h3></div>
  <?php else: foreach ($list as $r): ?>
    <div class="admin-card" style="margin-bottom:12px;">
      <div class="d-flex justify-between align-center mb-1">
        <div>
          <div class="fw-bold"><?= e($r['user_name']) ?> · <?= star_rating((float)$r['rating']) ?></div>
          <div class="text-sm text-muted">Produk: <?= e($r['product_name']) ?> · <?= time_ago($r['created_at']) ?></div>
        </div>
        <div>
          <?php if (!$r['is_approved']): ?>
            <button class="btn btn-success btn-sm" onclick="approve(<?= (int)$r['id'] ?>)"><i class="bi bi-check"></i> Approve</button>
            <button class="btn btn-danger btn-sm" onclick="rejectR(<?= (int)$r['id'] ?>)"><i class="bi bi-x"></i> Hapus</button>
          <?php else: ?>
            <span class="badge badge-success">Disetujui</span>
          <?php endif; ?>
        </div>
      </div>
      <?php if ($r['title']): ?><strong><?= e($r['title']) ?></strong><?php endif; ?>
      <div class="text-sm"><?= nl2br(e($r['body'])) ?></div>
      <?php if ($r['reply']): ?><div class="review-reply"><strong>Balasan:</strong> <?= e($r['reply']) ?></div><?php endif; ?>
      <details class="mt-1"><summary class="text-sm">Balas ulasan</summary>
        <form onsubmit="return reply(event, <?= (int)$r['id'] ?>)"><textarea class="form-control mt-1" name="reply" rows="2" placeholder="Tulis balasan…"><?= e($r['reply'] ?? '') ?></textarea><button class="btn btn-primary btn-sm mt-1">Kirim Balasan</button></form>
      </details>
    </div>
  <?php endforeach; endif; ?>
</div>
<script>
async function approve(id) { await postJSON('/admin/reviews/approve/' + id, {}); location.reload(); }
async function rejectR(id) { if (!confirm('Hapus ulasan?')) return; await postJSON('/admin/reviews/reject/' + id, {}); location.reload(); }
async function reply(e, id) { e.preventDefault(); const r = e.target.reply.value; await postJSON('/admin/reviews/reply/' + id, { reply: r }); showToast('Balasan disimpan', 'success'); return false; }
</script>
