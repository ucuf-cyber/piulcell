<?php

class PaymentController extends Controller {
    public function instructions($id) {
        $this->requireAuth('buyer');
        $id = (int) $id;
        $order = Database::row("SELECT * FROM orders WHERE id=? AND user_id=?", [$id, $this->user['id']]);
        if (!$order) { $this->flash('danger', 'Pesanan tidak ditemukan'); $this->redirect('/akun/pesanan'); }
        $payment = Database::row("SELECT * FROM payments WHERE order_id=?", [$id]);
        $banks = Database::all("SELECT * FROM payment_accounts WHERE type='transfer_bank' AND is_active=1 ORDER BY sort_order");
        $qris = Database::row("SELECT * FROM payment_accounts WHERE type='qris' AND is_active=1 LIMIT 1");
        $this->view('payment.instructions', [
            'pageTitle' => 'Pembayaran — ' . $order['order_number'],
            'order' => $order, 'payment' => $payment, 'banks' => $banks, 'qris' => $qris,
        ]);
    }

    public function uploadProof($id) {
        $this->requireAuth('buyer');
        $id = (int) $id;
        $order = Database::row("SELECT * FROM orders WHERE id=? AND user_id=?", [$id, $this->user['id']]);
        if (!$order) $this->json(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        $payment = Database::row("SELECT * FROM payments WHERE order_id=?", [$id]);
        if (!$payment) $this->json(['success' => false, 'message' => 'Data pembayaran tidak ditemukan']);
        if (in_array($payment['status'], ['verified', 'cod_done'])) $this->json(['success' => false, 'message' => 'Pembayaran sudah diverifikasi']);
        if ($payment['payment_method'] === 'cod') $this->json(['success' => false, 'message' => 'COD tidak perlu upload bukti']);

        if (empty($_FILES['proof']['tmp_name'])) $this->json(['success' => false, 'message' => 'File bukti diperlukan']);
        $ext = strtolower(pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp','pdf'])) $this->json(['success' => false, 'message' => 'Format tidak didukung']);
        if ($_FILES['proof']['size'] > 5 * 1024 * 1024) $this->json(['success' => false, 'message' => 'File maks 5MB']);

        $fileName = 'proof_' . $order['order_number'] . '_' . time() . '.' . $ext;
        $dest = BASE_PATH . '/uploads/payment_proofs/' . $fileName;
        if (!move_uploaded_file($_FILES['proof']['tmp_name'], $dest)) $this->json(['success' => false, 'message' => 'Gagal upload']);

        Database::update('payments', [
            'proof_image' => 'payment_proofs/' . $fileName,
            'notes' => $this->input('notes', ''),
            'status' => 'waiting_verification',
            'reject_reason' => null,
        ], 'id=?', [$payment['id']]);
        Database::update('orders', ['status' => 'confirmed'], 'id=?', [$id]);
        Database::insert('order_logs', ['order_id' => $id, 'status' => 'confirmed', 'note' => 'Bukti bayar diupload, menunggu verifikasi admin', 'created_by' => $this->user['id']]);
        // Notify admins
        $admins = Database::all("SELECT id FROM users WHERE role IN ('admin','owner') AND is_active=1");
        foreach ($admins as $a) {
            Database::insert('notifications', [
                'user_id' => $a['id'], 'type' => 'payment_proof',
                'title' => 'Bukti Bayar Baru',
                'message' => 'Order #' . $order['order_number'] . ' menunggu verifikasi pembayaran',
                'url' => '/admin/payments/pending',
            ]);
        }
        $this->json(['success' => true, 'message' => 'Bukti bayar terkirim! Menunggu verifikasi admin (maks 1×24 jam).']);
    }

    public function status($id) {
        $this->requireAuth('buyer');
        $id = (int) $id;
        $payment = Database::row("SELECT p.*, o.user_id FROM payments p JOIN orders o ON o.id=p.order_id WHERE p.order_id=?", [$id]);
        if (!$payment || (int)$payment['user_id'] !== (int)$this->user['id']) $this->json(['success' => false]);
        $this->json(['success' => true, 'status' => $payment['status'], 'reject_reason' => $payment['reject_reason']]);
    }
}
