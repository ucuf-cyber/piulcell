<?php

class AdminOrdersController extends AdminBaseController {
    public function index() {
        $status = $_GET['status'] ?? '';
        $where = "1=1"; $params = [];
        if ($status) { $where .= " AND o.status=?"; $params[] = $status; }
        $orders = Database::all("SELECT o.*, u.name AS buyer_name, p.payment_method, p.status AS pay_status FROM orders o JOIN users u ON u.id=o.user_id LEFT JOIN payments p ON p.order_id=o.id WHERE $where ORDER BY o.created_at DESC LIMIT 100", $params);
        $this->view('admin.orders.index', ['pageTitle' => 'Pesanan', 'orders' => $orders, 'status' => $status], 'admin');
    }
    public function detail($id) {
        $order = Database::row("SELECT o.*, u.name AS buyer_name, u.email, u.phone AS buyer_phone FROM orders o JOIN users u ON u.id=o.user_id WHERE o.id=?", [(int)$id]);
        if (!$order) $this->redirect('/admin/orders');
        $items = Database::all("SELECT * FROM order_items WHERE order_id=?", [(int)$id]);
        $payment = Database::row("SELECT * FROM payments WHERE order_id=?", [(int)$id]);
        $logs = Database::all("SELECT * FROM order_logs WHERE order_id=? ORDER BY created_at DESC", [(int)$id]);
        $this->view('admin.orders.detail', ['pageTitle' => 'Order ' . $order['order_number'], 'order' => $order, 'items' => $items, 'payment' => $payment, 'logs' => $logs], 'admin');
    }
    public function updateStatus($id) {
        $status = $this->input('status');
        $note = $this->input('note', '');
        $allowed = ['pending','confirmed','processing','shipped','delivered','cancelled','refunded'];
        if (!in_array($status, $allowed)) { $this->flash('danger', 'Status invalid'); $this->redirect('/admin/orders/' . (int)$id); }
        // avoid double increment: check previous status
        $prev = Database::row("SELECT status FROM orders WHERE id=?", [(int)$id]);
        $data = ['status' => $status];
        if ($status === 'shipped') $data['shipped_at'] = date('Y-m-d H:i:s');
        if ($status === 'delivered') $data['delivered_at'] = date('Y-m-d H:i:s');
        Database::update('orders', $data, 'id=?', [(int)$id]);
        // If transitioned to delivered, increment product sold_count from order items
        if ($status === 'delivered' && $prev && $prev['status'] !== 'delivered') {
            $items = Database::all("SELECT product_id, quantity FROM order_items WHERE order_id=?", [(int)$id]);
            foreach ($items as $it) {
                Database::q("UPDATE products SET sold_count = sold_count + ? WHERE id=?", [$it['quantity'], $it['product_id']]);
            }
        }
        Database::insert('order_logs', ['order_id' => (int)$id, 'status' => $status, 'note' => $note ?: 'Status diupdate', 'created_by' => $this->user['id']]);
        $order = Database::row("SELECT user_id, order_number FROM orders WHERE id=?", [(int)$id]);
        Database::insert('notifications', [
            'user_id' => $order['user_id'], 'type' => 'order_update',
            'title' => 'Status Pesanan: ' . order_status_label($status),
            'message' => 'Pesanan #' . $order['order_number'] . ' kini ' . order_status_label($status),
            'url' => '/akun/pesanan/' . (int)$id,
        ]);
        $this->flash('success', 'Status diupdate.');
        $this->redirect('/admin/orders/' . (int)$id);
    }
    public function tracking($id) {
        Database::update('orders', ['tracking_number' => $this->input('tracking_number'), 'courier_service' => $this->input('courier_service')], 'id=?', [(int)$id]);
        Database::insert('order_logs', ['order_id' => (int)$id, 'status' => 'tracking', 'note' => 'Resi: ' . $this->input('tracking_number'), 'created_by' => $this->user['id']]);
        $this->flash('success', 'No. resi disimpan.');
        $this->redirect('/admin/orders/' . (int)$id);
    }
}

class AdminPaymentsController extends AdminBaseController {
    public function pending() {
        $payments = Database::all("SELECT p.*, o.order_number, o.user_id, u.name AS buyer_name, u.phone AS buyer_phone FROM payments p JOIN orders o ON o.id=p.order_id JOIN users u ON u.id=o.user_id WHERE p.status='waiting_verification' ORDER BY p.created_at ASC");
        $this->view('admin.payments.pending', ['pageTitle' => 'Verifikasi Pembayaran', 'payments' => $payments], 'admin');
    }
    public function all() {
        $payments = Database::all("SELECT p.*, o.order_number, u.name AS buyer_name FROM payments p JOIN orders o ON o.id=p.order_id JOIN users u ON u.id=o.user_id ORDER BY p.created_at DESC LIMIT 100");
        $this->view('admin.payments.all', ['pageTitle' => 'Semua Pembayaran', 'payments' => $payments], 'admin');
    }
    public function verify($id) {
        $p = Database::row("SELECT * FROM payments WHERE id=?", [(int)$id]);
        if (!$p) $this->json(['success' => false, 'message' => 'Tidak ditemukan']);
        Database::update('payments', ['status' => 'verified', 'verified_by' => $this->user['id'], 'verified_at' => date('Y-m-d H:i:s')], 'id=?', [(int)$id]);
        Database::update('orders', ['status' => 'processing'], 'id=?', [$p['order_id']]);
        Database::insert('order_logs', ['order_id' => $p['order_id'], 'status' => 'processing', 'note' => 'Pembayaran diverifikasi admin', 'created_by' => $this->user['id']]);
        $order = Database::row("SELECT user_id, order_number FROM orders WHERE id=?", [$p['order_id']]);
        Database::insert('notifications', ['user_id' => $order['user_id'], 'type' => 'payment_verified', 'title' => 'Pembayaran Dikonfirmasi', 'message' => 'Pesanan #' . $order['order_number'] . ' sedang diproses.', 'url' => '/akun/pesanan/' . (int)$p['order_id']]);
        $this->json(['success' => true, 'message' => 'Diverifikasi']);
    }
    public function reject($id) {
        $reason = $this->input('reason');
        $p = Database::row("SELECT * FROM payments WHERE id=?", [(int)$id]);
        if (!$p || !$reason) $this->json(['success' => false, 'message' => 'Data tidak lengkap']);
        Database::update('payments', ['status' => 'rejected', 'reject_reason' => $reason, 'verified_by' => $this->user['id'], 'verified_at' => date('Y-m-d H:i:s')], 'id=?', [(int)$id]);
        Database::update('orders', ['status' => 'pending'], 'id=?', [$p['order_id']]);
        Database::insert('order_logs', ['order_id' => $p['order_id'], 'status' => 'pending', 'note' => 'Bukti ditolak: ' . $reason, 'created_by' => $this->user['id']]);
        $order = Database::row("SELECT user_id, order_number FROM orders WHERE id=?", [$p['order_id']]);
        Database::insert('notifications', ['user_id' => $order['user_id'], 'type' => 'payment_rejected', 'title' => 'Bukti Pembayaran Ditolak', 'message' => 'Alasan: ' . $reason, 'url' => '/payment/instructions/' . (int)$p['order_id']]);
        $this->json(['success' => true]);
    }
    public function codDone($id) {
        $p = Database::row("SELECT * FROM payments WHERE id=?", [(int)$id]);
        if (!$p) $this->json(['success' => false]);
        Database::update('payments', ['status' => 'cod_done', 'verified_by' => $this->user['id'], 'verified_at' => date('Y-m-d H:i:s')], 'id=?', [(int)$id]);
        // mark order delivered and increment sold_count if not already delivered
        $prev = Database::row("SELECT status FROM orders WHERE id=?", [$p['order_id']]);
        Database::update('orders', ['status' => 'delivered', 'delivered_at' => date('Y-m-d H:i:s')], 'id=?', [$p['order_id']]);
        if ($prev && $prev['status'] !== 'delivered') {
            $items = Database::all("SELECT product_id, quantity FROM order_items WHERE order_id=?", [$p['order_id']]);
            foreach ($items as $it) {
                Database::q("UPDATE products SET sold_count = sold_count + ? WHERE id=?", [$it['quantity'], $it['product_id']]);
            }
        }
        Database::insert('order_logs', ['order_id' => $p['order_id'], 'status' => 'delivered', 'note' => 'COD lunas', 'created_by' => $this->user['id']]);
        $this->json(['success' => true]);
    }
    public function accounts() {
        $accounts = Database::all("SELECT * FROM payment_accounts ORDER BY sort_order");
        $editing = null;
        if (!empty($_GET['edit'])) {
            $editing = Database::row("SELECT * FROM payment_accounts WHERE id=?", [(int)$_GET['edit']]);
        }
        $this->view('admin.payments.accounts', ['pageTitle' => 'Rekening & QRIS Toko', 'accounts' => $accounts, 'editing' => $editing], 'admin');
    }
    public function saveAccount() {
        $id = (int)$this->input('id', 0);
        $data = [
            'type' => $this->input('type', 'transfer_bank'),
            'bank_name' => $this->input('bank_name') ?: null,
            'account_number' => $this->input('account_number') ?: null,
            'account_name' => $this->input('account_name'),
            'sort_order' => (int)$this->input('sort_order'),
            'is_active' => $this->input('is_active') ? 1 : 0,
        ];
        // QRIS image upload
        if (!empty($_FILES['qris_image']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['qris_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $fname = 'qris_' . time() . '.' . $ext;
                $dest = BASE_PATH . '/uploads/qris/' . $fname;
                if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0777, true);
                if (move_uploaded_file($_FILES['qris_image']['tmp_name'], $dest)) {
                    $data['qris_image'] = 'qris/' . $fname;
                }
            }
        }
        if ($id) Database::update('payment_accounts', $data, 'id=?', [$id]);
        else Database::insert('payment_accounts', $data);
        $this->flash('success', 'Tersimpan.');
        $this->redirect('/admin/payment-accounts');
    }
    public function deleteAccount($id) {
        Database::delete('payment_accounts', 'id=?', [(int)$id]);
        $this->flash('success', 'Dihapus.');
        $this->redirect('/admin/payment-accounts');
    }
}

class AdminCustomersController extends AdminBaseController {
    public function index() {
        $customers = Database::all("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id=u.id) order_count, (SELECT COALESCE(SUM(total),0) FROM orders WHERE user_id=u.id AND status NOT IN ('cancelled','refunded')) spent FROM users u WHERE u.role='buyer' ORDER BY u.created_at DESC");
        $this->view('admin.customers.index', ['pageTitle' => 'Pelanggan', 'customers' => $customers], 'admin');
    }
    public function detail($id) {
        $c = Database::row("SELECT * FROM users WHERE id=? AND role='buyer'", [(int)$id]);
        if (!$c) $this->redirect('/admin/customers');
        $orders = Database::all("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC", [(int)$id]);
        $addrs = Database::all("SELECT * FROM user_addresses WHERE user_id=?", [(int)$id]);
        $this->view('admin.customers.detail', ['pageTitle' => $c['name'], 'customer' => $c, 'orders' => $orders, 'addresses' => $addrs], 'admin');
    }
    public function toggle($id) {
        $u = Database::row("SELECT is_active FROM users WHERE id=? AND role='buyer'", [(int)$id]);
        Database::update('users', ['is_active' => $u['is_active'] ? 0 : 1], 'id=?', [(int)$id]);
        $this->flash('success', 'Status diubah.');
        $this->redirect('/admin/customers/' . (int)$id);
    }
}

class AdminVouchersController extends AdminBaseController {
    public function index() {
        $list = Database::all("SELECT * FROM vouchers ORDER BY id DESC");
        $this->view('admin.vouchers.index', ['pageTitle' => 'Voucher', 'list' => $list], 'admin');
    }
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Database::insert('vouchers', [
                'code' => strtoupper($this->input('code')), 'name' => $this->input('name'),
                'type' => $this->input('type'), 'value' => (float)$this->input('value'),
                'min_purchase' => (float)$this->input('min_purchase'),
                'max_discount' => $this->input('max_discount') ? (float)$this->input('max_discount') : null,
                'quota' => $this->input('quota') ? (int)$this->input('quota') : null,
                'start_date' => $this->input('start_date') ?: null,
                'end_date' => $this->input('end_date') ?: null,
                'is_active' => 1,
            ]);
            $this->flash('success', 'Voucher ditambahkan.'); $this->redirect('/admin/vouchers');
        }
        $this->view('admin.vouchers.form', ['pageTitle' => 'Tambah Voucher', 'v' => null], 'admin');
    }
    public function edit($id) {
        $v = Database::row("SELECT * FROM vouchers WHERE id=?", [(int)$id]);
        if (!$v) $this->redirect('/admin/vouchers');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Database::update('vouchers', [
                'name' => $this->input('name'), 'type' => $this->input('type'),
                'value' => (float)$this->input('value'), 'min_purchase' => (float)$this->input('min_purchase'),
                'max_discount' => $this->input('max_discount') ? (float)$this->input('max_discount') : null,
                'quota' => $this->input('quota') ? (int)$this->input('quota') : null,
                'start_date' => $this->input('start_date') ?: null,
                'end_date' => $this->input('end_date') ?: null,
                'is_active' => $this->input('is_active') ? 1 : 0,
            ], 'id=?', [(int)$id]);
            $this->flash('success', 'Diperbarui.'); $this->redirect('/admin/vouchers');
        }
        $this->view('admin.vouchers.form', ['pageTitle' => 'Edit Voucher', 'v' => $v], 'admin');
    }
    public function delete($id) {
        Database::delete('vouchers', 'id=?', [(int)$id]);
        $this->flash('success', 'Dihapus.'); $this->redirect('/admin/vouchers');
    }
}

class AdminBannersController extends AdminBaseController {
    public function index() {
        $list = Database::all("SELECT * FROM banners ORDER BY position, sort_order");
        $this->view('admin.banners.index', ['pageTitle' => 'Banner', 'list' => $list], 'admin');
    }
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $img = $this->upload('image_desktop', 'banners');
            Database::insert('banners', [
                'title' => $this->input('title'), 'subtitle' => $this->input('subtitle'),
                'image_desktop' => $img ?: 'placeholder.svg', 'image_mobile' => $img,
                'link_url' => $this->input('link_url'), 'link_text' => $this->input('link_text'),
                'position' => $this->input('position', 'hero'),
                'sort_order' => (int)$this->input('sort_order'), 'is_active' => 1,
            ]);
            $this->flash('success', 'Banner ditambahkan.'); $this->redirect('/admin/banners');
        }
        $this->view('admin.banners.form', ['pageTitle' => 'Tambah Banner', 'b' => null], 'admin');
    }
    public function edit($id) {
        $b = Database::row("SELECT * FROM banners WHERE id=?", [(int)$id]);
        if (!$b) $this->redirect('/admin/banners');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $this->input('title'), 'subtitle' => $this->input('subtitle'),
                'link_url' => $this->input('link_url'), 'link_text' => $this->input('link_text'),
                'position' => $this->input('position', 'hero'),
                'sort_order' => (int)$this->input('sort_order'),
                'is_active' => $this->input('is_active') ? 1 : 0,
            ];
            $img = $this->upload('image_desktop', 'banners');
            if ($img) { $data['image_desktop'] = $img; $data['image_mobile'] = $img; }
            Database::update('banners', $data, 'id=?', [(int)$id]);
            $this->flash('success', 'Diperbarui.'); $this->redirect('/admin/banners');
        }
        $this->view('admin.banners.form', ['pageTitle' => 'Edit Banner', 'b' => $b], 'admin');
    }
    public function delete($id) {
        Database::delete('banners', 'id=?', [(int)$id]);
        $this->flash('success', 'Dihapus.'); $this->redirect('/admin/banners');
    }
    private function upload(string $field, string $folder): ?string {
        if (empty($_FILES[$field]['tmp_name'])) return null;
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) return null;
        $fname = $folder . '_' . time() . '_' . random_int(1000, 9999) . '.' . $ext;
        $dest = BASE_PATH . '/uploads/' . $folder . '/' . $fname;
        if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0777, true);
        return move_uploaded_file($_FILES[$field]['tmp_name'], $dest) ? $fname : null;
    }
}

class AdminReviewsController extends AdminBaseController {
    public function index() {
        $status = $_GET['status'] ?? 'pending';
        $where = $status === 'approved' ? 'r.is_approved=1' : 'r.is_approved=0';
        $list = Database::all("SELECT r.*, p.name AS product_name, u.name AS user_name FROM reviews r JOIN products p ON p.id=r.product_id JOIN users u ON u.id=r.user_id WHERE $where ORDER BY r.created_at DESC");
        $this->view('admin.reviews.index', ['pageTitle' => 'Ulasan', 'list' => $list, 'status' => $status], 'admin');
    }
    public function approve($id) { Database::update('reviews', ['is_approved' => 1], 'id=?', [(int)$id]); $this->json(['success' => true]); }
    public function reject($id) { Database::delete('reviews', 'id=?', [(int)$id]); $this->json(['success' => true]); }
    public function reply($id) {
        Database::update('reviews', ['reply' => $this->input('reply'), 'replied_at' => date('Y-m-d H:i:s')], 'id=?', [(int)$id]);
        $this->json(['success' => true]);
    }
}

class AdminShippingController extends AdminBaseController {
    public function index() {
        $zones = Database::all("SELECT * FROM shipping_zones ORDER BY CASE WHEN max_distance=0 THEN 999999 ELSE max_distance END");
        $this->view('admin.shipping.index', ['pageTitle' => 'Zona Pengiriman', 'zones' => $zones], 'admin');
    }
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Database::insert('shipping_zones', [
                'name' => $this->input('name'), 'base_price' => (float)$this->input('base_price'),
                'price_per_km' => (float)$this->input('price_per_km'),
                'max_distance' => (int)$this->input('max_distance'),
                'courier' => $this->input('courier', 'Internal'),
                'etd_days' => $this->input('etd_days', '1-2'),
                'is_active' => 1,
            ]);
            $this->flash('success', 'Zona ditambahkan.'); $this->redirect('/admin/shipping-zones');
        }
        $this->view('admin.shipping.form', ['pageTitle' => 'Tambah Zona', 'z' => null], 'admin');
    }
    public function edit($id) {
        $z = Database::row("SELECT * FROM shipping_zones WHERE id=?", [(int)$id]);
        if (!$z) $this->redirect('/admin/shipping-zones');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Database::update('shipping_zones', [
                'name' => $this->input('name'), 'base_price' => (float)$this->input('base_price'),
                'price_per_km' => (float)$this->input('price_per_km'),
                'max_distance' => (int)$this->input('max_distance'),
                'courier' => $this->input('courier'), 'etd_days' => $this->input('etd_days'),
                'is_active' => $this->input('is_active') ? 1 : 0,
            ], 'id=?', [(int)$id]);
            $this->flash('success', 'Diperbarui.'); $this->redirect('/admin/shipping-zones');
        }
        $this->view('admin.shipping.form', ['pageTitle' => 'Edit Zona', 'z' => $z], 'admin');
    }
    public function delete($id) {
        Database::delete('shipping_zones', 'id=?', [(int)$id]);
        $this->flash('success', 'Dihapus.'); $this->redirect('/admin/shipping-zones');
    }
}

class AdminSettingsController extends AdminBaseController {
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $k => $v) {
                if ($k === '_csrf') continue;
                Database::q("INSERT INTO store_settings (key_name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value=VALUES(value)", [$k, (string)$v]);
            }
            $this->flash('success', 'Pengaturan tersimpan.');
            $this->redirect('/admin/settings');
        }
        $rows = Database::all("SELECT * FROM store_settings ORDER BY group_name, key_name");
        $settings = [];
        foreach ($rows as $r) $settings[$r['group_name']][$r['key_name']] = $r['value'];
        $this->view('admin.settings.index', ['pageTitle' => 'Pengaturan Toko', 'groups' => $settings], 'admin');
    }
}

class AdminUsersController extends AdminBaseController {
    public function index() {
        $users = Database::all("SELECT * FROM users WHERE role IN ('admin','owner') ORDER BY id");
        $this->view('admin.users.index', ['pageTitle' => 'User Admin', 'users' => $users], 'admin');
    }
    public function create() {
        if ($this->user['role'] !== 'owner') { $this->flash('danger', 'Hanya owner.'); $this->redirect('/admin/users'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Database::insert('users', [
                'name' => $this->input('name'), 'email' => $this->input('email'),
                'phone' => $this->input('phone'),
                'password' => password_hash($this->input('password'), PASSWORD_BCRYPT, ['cost' => 10]),
                'role' => $this->input('role', 'admin'), 'is_active' => 1,
            ]);
            $this->flash('success', 'User dibuat.'); $this->redirect('/admin/users');
        }
        $this->view('admin.users.form', ['pageTitle' => 'Tambah User', 'u' => null], 'admin');
    }
    public function edit($id) {
        $u = Database::row("SELECT * FROM users WHERE id=?", [(int)$id]);
        if (!$u || $u['role'] === 'buyer') $this->redirect('/admin/users');
        if ($u['role'] === 'owner' && $this->user['role'] !== 'owner') { $this->flash('danger', 'Tidak diizinkan.'); $this->redirect('/admin/users'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = ['name' => $this->input('name'), 'phone' => $this->input('phone'), 'is_active' => $this->input('is_active') ? 1 : 0];
            if ($this->input('password')) $data['password'] = password_hash($this->input('password'), PASSWORD_BCRYPT, ['cost' => 10]);
            if ($this->user['role'] === 'owner') $data['role'] = $this->input('role', $u['role']);
            Database::update('users', $data, 'id=?', [(int)$id]);
            $this->flash('success', 'Diperbarui.'); $this->redirect('/admin/users');
        }
        $this->view('admin.users.form', ['pageTitle' => 'Edit User', 'u' => $u], 'admin');
    }
    public function delete($id) {
        if ($this->user['role'] !== 'owner') $this->redirect('/admin/users');
        Database::delete('users', 'id=? AND role<>?', [(int)$id, 'owner']);
        $this->flash('success', 'Dihapus.'); $this->redirect('/admin/users');
    }
}
