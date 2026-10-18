<?php

class CartController extends Controller {
    private function key(): array {
        if ($this->user) return ['user_id', $this->user['id']];
        return ['session_id', session_id()];
    }

    public function add() {
        if (!$this->csrfCheck()) $this->json(['success' => false, 'message' => 'CSRF token tidak valid']);
        $variantId = (int) $this->input('variant_id');
        $qty = max(1, (int) $this->input('quantity', 1));
        $variant = Database::row("SELECT * FROM product_variants WHERE id=? AND is_active=1", [$variantId]);
        if (!$variant) $this->json(['success' => false, 'message' => 'Produk tidak ditemukan']);
        if ($variant['stock'] < $qty) $this->json(['success' => false, 'message' => 'Stok tidak mencukupi']);
        [$col, $val] = $this->key();
        $existing = Database::row("SELECT * FROM carts WHERE $col=? AND variant_id=?", [$val, $variantId]);
        if ($existing) {
            $newQty = min($variant['stock'], $existing['quantity'] + $qty);
            Database::update('carts', ['quantity' => $newQty], 'id=?', [$existing['id']]);
        } else {
            Database::insert('carts', [
                'user_id' => $this->user ? $this->user['id'] : null,
                'session_id' => $this->user ? null : session_id(),
                'variant_id' => $variantId,
                'quantity' => $qty,
            ]);
        }
        $this->json(['success' => true, 'cart_count' => $this->cartCount(), 'message' => 'Berhasil ditambahkan']);
    }

    public function update() {
        if (!$this->csrfCheck()) $this->json(['success' => false, 'message' => 'CSRF tidak valid']);
        $variantId = (int) $this->input('variant_id');
        $qty = max(1, (int) $this->input('quantity', 1));
        $variant = Database::row("SELECT * FROM product_variants WHERE id=?", [$variantId]);
        if (!$variant) $this->json(['success' => false, 'message' => 'Tidak ditemukan']);
        if ($qty > $variant['stock']) $this->json(['success' => false, 'message' => 'Stok hanya ' . $variant['stock']]);
        [$col, $val] = $this->key();
        Database::q("UPDATE carts SET quantity=? WHERE $col=? AND variant_id=?", [$qty, $val, $variantId]);
        $this->json(['success' => true, 'cart_count' => $this->cartCount()]);
    }

    public function remove() {
        if (!$this->csrfCheck()) $this->json(['success' => false, 'message' => 'CSRF tidak valid']);
        $variantId = (int) $this->input('variant_id');
        [$col, $val] = $this->key();
        Database::q("DELETE FROM carts WHERE $col=? AND variant_id=?", [$val, $variantId]);
        $this->json(['success' => true, 'cart_count' => $this->cartCount()]);
    }

    public function count() {
        $this->json(['cart_count' => $this->cartCount()]);
    }

    public function mini() {
        $items = $this->fetchItems();
        ob_start();
        include APP_PATH . '/Views/cart/mini.php';
        $html = ob_get_clean();
        $this->json(['html' => $html, 'count' => count($items)]);
    }

    public function index() {
        $items = $this->fetchItems();
        $subtotal = 0;
        foreach ($items as $it) $subtotal += $it['line_total'];
        $this->view('cart.index', [
            'pageTitle' => 'Keranjang — Piul Cell',
            'items' => $items,
            'subtotal' => $subtotal,
        ]);
    }

    public function fetchItems(): array {
        [$col, $val] = $this->key();
        $rows = Database::all("SELECT c.id AS cart_id, c.quantity, c.variant_id,
                                      v.sku, v.combination, v.price, v.price_promo, v.stock, v.weight, v.images AS v_images,
                                      p.id AS product_id, p.name, p.slug, p.thumbnail,
                                      b.name AS brand_name
                               FROM carts c
                               JOIN product_variants v ON v.id = c.variant_id
                               JOIN products p ON p.id = v.product_id
                               JOIN brands b ON b.id = p.brand_id
                               WHERE c.$col = ?", [$val]);
        $items = [];
        foreach ($rows as $r) {
            $r['combination'] = json_decode($r['combination'], true);
            $imgs = json_decode($r['v_images'], true) ?: [];
            $r['image'] = $imgs[0] ?? $r['thumbnail'];
            $r['final_price'] = !empty($r['price_promo']) && $r['price_promo'] > 0 ? (float)$r['price_promo'] : (float)$r['price'];
            $r['line_total'] = $r['final_price'] * $r['quantity'];
            $items[] = $r;
        }
        return $items;
    }
}
