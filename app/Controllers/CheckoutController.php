<?php

class CheckoutController extends Controller {
    public function index() {
        $this->requireAuth('buyer');
        $cart = new CartController();
        $items = $cart->fetchItems();
        if (!$items) { $this->flash('warning', 'Keranjang Anda kosong.'); $this->redirect('/katalog'); }

        $subtotal = 0;
        foreach ($items as $i) $subtotal += $i['line_total'];

        $addresses = Database::all("SELECT * FROM user_addresses WHERE user_id=? ORDER BY is_primary DESC, id ASC", [$this->user['id']]);
        $codMax = (int) get_setting('cod_max_distance', 15);
        $codEnabled = (int) get_setting('cod_enabled', 1);
        $transferEnabled = (int) get_setting('transfer_enabled', 1);
        $qrisEnabled = (int) get_setting('qris_enabled', 1);
        $freeMin = max(7500000, (int) get_setting('free_shipping_min', 7500000));

        $this->view('checkout.index', [
            'pageTitle' => 'Checkout — Piul Cell',
            'items' => $items,
            'subtotal' => $subtotal,
            'addresses' => $addresses,
            'codMax' => $codMax,
            'codEnabled' => $codEnabled,
            'transferEnabled' => $transferEnabled,
            'qrisEnabled' => $qrisEnabled,
            'freeMin' => $freeMin,
        ]);
    }

    public function process() {
        $this->requireAuth('buyer');
        $cart = new CartController();
        $items = $cart->fetchItems();
        if (!$items) $this->json(['success' => false, 'message' => 'Keranjang kosong']);

        $addressId = (int) $this->input('address_id');
        $address = Database::row("SELECT * FROM user_addresses WHERE id=? AND user_id=?", [$addressId, $this->user['id']]);
        if (!$address) {
            // Use a new address from form
            $address = [
                'recipient' => $this->input('recipient'),
                'phone' => $this->input('ship_phone'),
                'province' => $this->input('province'),
                'city' => $this->input('city'),
                'district' => $this->input('district'),
                'postal_code' => $this->input('postal_code'),
                'address_detail' => $this->input('address_detail'),
                'latitude' => $this->input('latitude'),
                'longitude' => $this->input('longitude'),
            ];
            if (!$address['recipient'] || !$address['phone'] || !$address['city']) {
                $this->json(['success' => false, 'message' => 'Lengkapi alamat pengiriman']);
            }
            // Save as new address
            $newAddrId = Database::insert('user_addresses', array_merge($address, [
                'user_id' => $this->user['id'], 'label' => 'Alamat ' . date('d/m H:i'), 'is_primary' => 0,
            ]));
            $address = Database::row("SELECT * FROM user_addresses WHERE id=?", [$newAddrId]);
        }

        $subtotal = 0;
        foreach ($items as $i) $subtotal += $i['line_total'];

        $shippingCost = (float) $this->input('shipping_cost', 0);
        $courier = (string) $this->input('courier_name', 'Internal');
        $voucherCode = trim((string) $this->input('voucher_code', ''));
        $discount = 0;
        $voucherId = null;
        $voucher = null;
        if ($voucherCode) {
            $voucher = $this->fetchValidVoucher($voucherCode, $subtotal);
            if ($voucher) {
                if ($voucher['type'] === 'percentage') {
                    $discount = floor($subtotal * $voucher['value'] / 100);
                    if (!empty($voucher['max_discount']) && $discount > $voucher['max_discount']) $discount = (float) $voucher['max_discount'];
                } elseif ($voucher['type'] === 'fixed') {
                    $discount = (float) $voucher['value'];
                } elseif ($voucher['type'] === 'free_shipping') {
                    $discount = $shippingCost;
                }
                $voucherId = (int) $voucher['id'];
            }
        }

        $paymentMethod = (string) $this->input('payment_method', 'transfer_bank');
        if (!in_array($paymentMethod, ['transfer_bank', 'qris', 'cod'])) $paymentMethod = 'transfer_bank';

        $total = max(0, $subtotal + $shippingCost - $discount);

        // Validate stock
        foreach ($items as $it) {
            if ($it['stock'] < $it['quantity']) {
                $this->json(['success' => false, 'message' => 'Stok tidak cukup untuk ' . $it['name']]);
            }
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $orderId = Database::insert('orders', [
                'order_number' => generate_order_number(),
                'user_id' => $this->user['id'],
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'tax' => 0,
                'total' => $total,
                'voucher_code' => $voucherCode ?: null,
                'voucher_id' => $voucherId,
                'notes' => $this->input('notes', ''),
                'ship_name' => $address['recipient'],
                'ship_phone' => $address['phone'],
                'ship_province' => $address['province'],
                'ship_city' => $address['city'],
                'ship_district' => $address['district'],
                'ship_postal' => $address['postal_code'],
                'ship_address' => $address['address_detail'],
                'ship_lat' => $address['latitude'] ?: null,
                'ship_lng' => $address['longitude'] ?: null,
                'courier' => $courier,
                'courier_service' => null,
            ]);
            foreach ($items as $it) {
                Database::insert('order_items', [
                    'order_id' => $orderId,
                    'variant_id' => $it['variant_id'],
                    'product_id' => $it['product_id'],
                    'product_name' => $it['name'],
                    'variant_combination' => json_encode($it['combination'], JSON_UNESCAPED_UNICODE),
                    'price' => $it['final_price'],
                    'quantity' => $it['quantity'],
                    'subtotal' => $it['line_total'],
                    'image' => $it['image'],
                ]);
                // Reduce stock (sold_count will be updated when order is delivered)
                Database::q("UPDATE product_variants SET stock = stock - ? WHERE id=?", [$it['quantity'], $it['variant_id']]);
            }
            // Create payment record
            $expireHours = (int) get_setting('payment_expire_hours', 24);
            $expireAt = $paymentMethod === 'cod' ? null : date('Y-m-d H:i:s', time() + $expireHours * 3600);
            $bankInfo = null;
            if ($paymentMethod === 'transfer_bank') {
                $bankInfo = Database::row("SELECT * FROM payment_accounts WHERE type='transfer_bank' AND is_active=1 ORDER BY sort_order LIMIT 1");
            }
            Database::insert('payments', [
                'order_id' => $orderId,
                'payment_method' => $paymentMethod,
                'bank_name' => $bankInfo['bank_name'] ?? null,
                'account_number' => $bankInfo['account_number'] ?? null,
                'account_name' => $bankInfo['account_name'] ?? null,
                'amount' => $total,
                'status' => $paymentMethod === 'cod' ? 'cod_pending' : 'pending',
                'expire_at' => $expireAt,
            ]);
            Database::insert('order_logs', [
                'order_id' => $orderId, 'status' => 'pending',
                'note' => 'Pesanan dibuat. Metode: ' . payment_method_label($paymentMethod),
                'created_by' => $this->user['id'],
            ]);
            if ($voucher && !empty($voucher['quota'])) {
                Database::q("UPDATE vouchers SET used_count = used_count + 1 WHERE id=?", [$voucher['id']]);
            }
            // Clear cart
            Database::delete('carts', 'user_id=?', [$this->user['id']]);
            // Notify admins
            $admins = Database::all("SELECT id FROM users WHERE role IN ('admin','owner') AND is_active=1");
            foreach ($admins as $a) {
                Database::insert('notifications', [
                    'user_id' => $a['id'], 'type' => 'new_order',
                    'title' => 'Pesanan Baru',
                    'message' => 'Pesanan #' . Database::val("SELECT order_number FROM orders WHERE id=?", [$orderId]) . ' dari ' . $this->user['name'],
                    'url' => '/admin/orders/' . $orderId,
                ]);
            }
            $pdo->commit();
            $this->json(['success' => true, 'order_id' => $orderId, 'redirect' => '/payment/instructions/' . $orderId]);
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log('Checkout error: ' . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Gagal memproses pesanan: ' . $e->getMessage()]);
        }
    }

    public function validateVoucher() {
        $code = trim((string) $this->input('code', ''));
        $subtotal = (float) $this->input('subtotal', 0);
        $v = $this->fetchValidVoucher($code, $subtotal);
        if (!$v) $this->json(['success' => false, 'message' => 'Voucher tidak valid / kadaluarsa / minimal belum tercapai']);
        $discount = 0;
        if ($v['type'] === 'percentage') {
            $discount = floor($subtotal * $v['value'] / 100);
            if (!empty($v['max_discount']) && $discount > $v['max_discount']) $discount = (float) $v['max_discount'];
        } elseif ($v['type'] === 'fixed') {
            $discount = (float) $v['value'];
        } elseif ($v['type'] === 'free_shipping') {
            $discount = 0; // applied to shipping
        }
        $this->json(['success' => true, 'voucher' => [
            'code' => $v['code'], 'name' => $v['name'], 'type' => $v['type'],
            'discount' => $discount, 'discount_display' => rupiah($discount),
            'free_shipping' => $v['type'] === 'free_shipping',
        ]]);
    }

    private function fetchValidVoucher(string $code, float $subtotal): ?array {
        $v = Database::row("SELECT * FROM vouchers WHERE code=? AND is_active=1
                            AND (start_date IS NULL OR start_date <= CURDATE())
                            AND (end_date IS NULL OR end_date >= CURDATE())
                            AND (quota IS NULL OR used_count < quota)", [strtoupper($code)]);
        if (!$v) return null;
        if ($subtotal < (float) $v['min_purchase']) return null;
        if (!empty($v['user_specific']) && (int)$v['user_specific'] !== (int)$this->user['id']) return null;
        return $v;
    }
}
