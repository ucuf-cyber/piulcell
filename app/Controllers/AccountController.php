<?php

class AccountController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireAuth('buyer');
    }

    public function profile() {
        $this->view('account.profile', ['pageTitle' => 'Profil Saya', 'tab' => 'profile']);
    }

    public function update() {
        $data = [
            'name' => trim($this->input('name', '')),
            'phone' => trim($this->input('phone', '')),
        ];
        if ($data['name']) Database::update('users', $data, 'id=?', [$this->user['id']]);
        $this->flash('success', 'Profil diperbarui.');
        $this->redirect('/akun');
    }

    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = (string)$this->input('old_password', '');
            $new = (string)$this->input('new_password', '');
            $new2 = (string)$this->input('new_password_confirm', '');
            if (!password_verify($old, $this->user['password'])) { $this->flash('danger', 'Password lama salah.'); $this->redirect('/akun/ganti-password'); }
            if (strlen($new) < 8 || $new !== $new2) { $this->flash('danger', 'Password baru tidak valid / tidak cocok.'); $this->redirect('/akun/ganti-password'); }
            Database::update('users', ['password' => password_hash($new, PASSWORD_BCRYPT, ['cost' => 10])], 'id=?', [$this->user['id']]);
            $this->flash('success', 'Password berhasil diubah.');
            $this->redirect('/akun');
        }
        $this->view('account.change_password', ['pageTitle' => 'Ganti Password', 'tab' => 'password']);
    }

    public function addresses() {
        $addrs = Database::all("SELECT * FROM user_addresses WHERE user_id=? ORDER BY is_primary DESC, id ASC", [$this->user['id']]);
        $this->view('account.addresses', ['pageTitle' => 'Alamat Saya', 'tab' => 'addresses', 'addresses' => $addrs]);
    }

    public function addAddress() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $this->user['id'],
                'label' => $this->input('label', 'Rumah'),
                'recipient' => $this->input('recipient'),
                'phone' => $this->input('phone'),
                'province' => $this->input('province'),
                'city' => $this->input('city'),
                'district' => $this->input('district'),
                'postal_code' => $this->input('postal_code'),
                'address_detail' => $this->input('address_detail'),
                'latitude' => $this->input('latitude') ?: null,
                'longitude' => $this->input('longitude') ?: null,
                'is_primary' => $this->input('is_primary') ? 1 : 0,
            ];
            if ($data['is_primary']) Database::q("UPDATE user_addresses SET is_primary=0 WHERE user_id=?", [$this->user['id']]);
            Database::insert('user_addresses', $data);
            $this->flash('success', 'Alamat ditambahkan.');
            $this->redirect('/akun/alamat');
        }
        $this->view('account.address_form', ['pageTitle' => 'Tambah Alamat', 'tab' => 'addresses']);
    }

    public function deleteAddress($id) {
        Database::delete('user_addresses', 'id=? AND user_id=?', [(int)$id, $this->user['id']]);
        $this->flash('success', 'Alamat dihapus.');
        $this->redirect('/akun/alamat');
    }

    public function setPrimaryAddress($id) {
        Database::q("UPDATE user_addresses SET is_primary=0 WHERE user_id=?", [$this->user['id']]);
        Database::update('user_addresses', ['is_primary' => 1], 'id=? AND user_id=?', [(int)$id, $this->user['id']]);
        $this->flash('success', 'Alamat utama diperbarui.');
        $this->redirect('/akun/alamat');
    }

    public function orders() {
        $status = $_GET['status'] ?? '';
        $where = "user_id=?";
        $params = [$this->user['id']];
        if ($status) { $where .= " AND status=?"; $params[] = $status; }
        $orders = Database::all("SELECT * FROM orders WHERE $where ORDER BY created_at DESC", $params);
        $this->view('account.orders', ['pageTitle' => 'Pesanan Saya', 'tab' => 'orders', 'orders' => $orders, 'currentStatus' => $status]);
    }

    public function orderDetail($id) {
        $order = Database::row("SELECT * FROM orders WHERE id=? AND user_id=?", [(int)$id, $this->user['id']]);
        if (!$order) { $this->flash('danger', 'Pesanan tidak ditemukan'); $this->redirect('/akun/pesanan'); }
        $items = Database::all("SELECT * FROM order_items WHERE order_id=?", [(int)$id]);
        $payment = Database::row("SELECT * FROM payments WHERE order_id=?", [(int)$id]);
        $logs = Database::all("SELECT * FROM order_logs WHERE order_id=? ORDER BY created_at DESC", [(int)$id]);
        $this->view('account.order_detail', [
            'pageTitle' => 'Pesanan ' . $order['order_number'],
            'tab' => 'orders', 'order' => $order, 'items' => $items, 'payment' => $payment, 'logs' => $logs,
        ]);
    }

    public function cancelOrder($id) {
        $order = Database::row("SELECT * FROM orders WHERE id=? AND user_id=?", [(int)$id, $this->user['id']]);
        if (!$order || !in_array($order['status'], ['pending', 'confirmed'])) {
            $this->flash('danger', 'Tidak dapat dibatalkan'); $this->redirect('/akun/pesanan');
        }
        Database::update('orders', ['status' => 'cancelled'], 'id=?', [(int)$id]);
        Database::insert('order_logs', ['order_id' => (int)$id, 'status' => 'cancelled', 'note' => 'Dibatalkan oleh buyer', 'created_by' => $this->user['id']]);
        // Restore stock
        $items = Database::all("SELECT * FROM order_items WHERE order_id=?", [(int)$id]);
        foreach ($items as $it) {
            Database::q("UPDATE product_variants SET stock = stock + ? WHERE id=?", [$it['quantity'], $it['variant_id']]);
        }
        $this->flash('success', 'Pesanan dibatalkan.');
        $this->redirect('/akun/pesanan');
    }

    public function confirmReceived($id) {
        $order = Database::row("SELECT * FROM orders WHERE id=? AND user_id=?", [(int)$id, $this->user['id']]);
        if (!$order || $order['status'] !== 'shipped') { $this->flash('danger', 'Tidak dapat dikonfirmasi'); $this->redirect('/akun/pesanan'); }
        Database::update('orders', ['status' => 'delivered', 'delivered_at' => date('Y-m-d H:i:s')], 'id=?', [(int)$id]);
        Database::insert('order_logs', ['order_id' => (int)$id, 'status' => 'delivered', 'note' => 'Buyer mengkonfirmasi pesanan diterima', 'created_by' => $this->user['id']]);
        $this->flash('success', 'Terima kasih! Pesanan telah selesai.');
        $this->redirect('/akun/pesanan/' . (int)$id);
    }

    public function wishlist() {
        $items = Database::all("SELECT p.*, b.name AS brand_name FROM wishlists w JOIN products p ON p.id=w.product_id JOIN brands b ON b.id=p.brand_id WHERE w.user_id=? ORDER BY w.created_at DESC", [$this->user['id']]);
        $this->view('account.wishlist', ['pageTitle' => 'Wishlist', 'tab' => 'wishlist', 'wishlist' => $items]);
    }

    public function toggleWishlist() {
        if (!$this->user) $this->json(['success' => false, 'redirect' => '/login']);
        $productId = (int) $this->input('product_id');
        $existing = Database::row("SELECT id FROM wishlists WHERE user_id=? AND product_id=?", [$this->user['id'], $productId]);
        if ($existing) {
            Database::delete('wishlists', 'id=?', [$existing['id']]);
            $this->json(['success' => true, 'added' => false]);
        }
        Database::insert('wishlists', ['user_id' => $this->user['id'], 'product_id' => $productId]);
        $this->json(['success' => true, 'added' => true]);
    }

    public function writeReview($orderItemId) {
        $oi = Database::row("SELECT oi.*, o.user_id, o.status FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE oi.id=?", [(int)$orderItemId]);
        if (!$oi || (int)$oi['user_id'] !== (int)$this->user['id'] || $oi['status'] !== 'delivered') {
            $this->flash('danger', 'Tidak dapat membuat ulasan'); $this->redirect('/akun/pesanan');
        }
        $existing = Database::row("SELECT id FROM reviews WHERE order_item_id=? AND user_id=?", [(int)$orderItemId, $this->user['id']]);
        if ($existing) { $this->flash('info', 'Anda sudah memberi ulasan.'); $this->redirect('/akun/pesanan'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rating = max(1, min(5, (int) $this->input('rating', 5)));
            $title = trim($this->input('title', ''));
            $body = trim($this->input('body', ''));
            if (!$body) { $this->flash('danger', 'Isi ulasan diperlukan'); $this->redirect('/akun/ulasan/' . (int)$orderItemId); }
            Database::insert('reviews', [
                'product_id' => $oi['product_id'], 'variant_id' => $oi['variant_id'], 'user_id' => $this->user['id'],
                'order_item_id' => (int)$orderItemId, 'rating' => $rating, 'title' => $title, 'body' => $body, 'is_approved' => 0,
            ]);
            $this->flash('success', 'Ulasan terkirim! Menunggu moderasi admin.');
            $this->redirect('/akun/pesanan');
        }
        $this->view('account.review_form', ['pageTitle' => 'Tulis Ulasan', 'tab' => 'orders', 'order_item' => $oi]);
    }

    public function notifications() {
        $notifs = Database::all("SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 50", [$this->user['id']]);
        Database::q("UPDATE notifications SET is_read=1 WHERE user_id=? AND is_read=0", [$this->user['id']]);
        $this->view('account.notifications', ['pageTitle' => 'Notifikasi', 'tab' => 'notif', 'notifs' => $notifs]);
    }

    public function readNotif($id) {
        Database::update('notifications', ['is_read' => 1], 'id=? AND user_id=?', [(int)$id, $this->user['id']]);
        $this->json(['success' => true]);
    }
}
