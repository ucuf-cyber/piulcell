<?php

// Common base for admin
class AdminBaseController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireAuth('admin');
    }
}

class AdminDashboardController extends AdminBaseController {
    public function index() {
        $today = date('Y-m-d');
        $stats = [
            'orders_today' => (int) Database::val("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=?", [$today]),
            'revenue_today' => (float) Database::val("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE(delivered_at)=? AND status='delivered'", [$today]),
            'orders_pending' => (int) Database::val("SELECT COUNT(*) FROM orders WHERE status IN ('pending','confirmed')"),
            'payments_to_verify' => (int) Database::val("SELECT COUNT(*) FROM payments WHERE status='waiting_verification'"),
            'low_stock' => (int) Database::val("SELECT COUNT(*) FROM product_variants WHERE stock < 5 AND is_active=1"),
            'total_products' => (int) Database::val("SELECT COUNT(*) FROM products WHERE is_active=1"),
            'total_customers' => (int) Database::val("SELECT COUNT(*) FROM users WHERE role='buyer'"),
            'total_revenue' => (float) Database::val("SELECT COALESCE(SUM(total),0) FROM orders WHERE status='delivered'"),
        ];
        $recentOrders = Database::all("SELECT o.*, u.name AS buyer_name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC LIMIT 8");
        $lowStock = Database::all("SELECT pv.*, p.name AS product_name FROM product_variants pv JOIN products p ON p.id=pv.product_id WHERE pv.stock < 5 AND pv.is_active=1 ORDER BY pv.stock ASC LIMIT 8");
        $chartData = Database::all("SELECT DATE(delivered_at) d, COALESCE(SUM(total),0) total FROM orders WHERE status='delivered' AND delivered_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(delivered_at) ORDER BY d");
        $this->view('admin.dashboard', ['pageTitle' => 'Dashboard Admin', 'stats' => $stats, 'recentOrders' => $recentOrders, 'lowStock' => $lowStock, 'chartData' => $chartData], 'admin');
    }
}

class AdminProductsController extends AdminBaseController {
    public function index() {
        $products = Database::all("SELECT p.*, b.name AS brand_name, c.name AS category_name FROM products p JOIN brands b ON b.id=p.brand_id JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC");
        $this->view('admin.products.index', ['pageTitle' => 'Produk', 'products' => $products], 'admin');
    }

    public function create() {
        $brands = Database::all("SELECT * FROM brands ORDER BY name");
        $cats = Database::all("SELECT * FROM categories ORDER BY name");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->input('name');
            $slug = slugify($name);
            // Ensure unique slug
            $i = 1; $base = $slug;
            while (Database::row("SELECT id FROM products WHERE slug=?", [$slug])) { $slug = $base . '-' . (++$i); }
            $id = Database::insert('products', [
                'brand_id' => (int)$this->input('brand_id'),
                'category_id' => (int)$this->input('category_id'),
                'name' => $name,
                'slug' => $slug,
                'sku_base' => $this->input('sku_base'),
                'description' => $this->input('description'),
                'specifications' => json_encode($this->parseSpecs($this->input('specifications', '')), JSON_UNESCAPED_UNICODE),
                'thumbnail' => $this->handleUpload('thumbnail', 'products') ?: null,
                'is_featured' => $this->input('is_featured') ? 1 : 0,
                'is_new' => $this->input('is_new') ? 1 : 0,
                'is_active' => 1,
            ]);
            $this->flash('success', 'Produk ditambahkan. Silakan atur varian.');
            $this->redirect('/admin/products/variants/' . $id);
        }
        $this->view('admin.products.form', ['pageTitle' => 'Tambah Produk', 'brands' => $brands, 'cats' => $cats, 'product' => null], 'admin');
    }

    public function edit($id) {
        $product = Database::row("SELECT * FROM products WHERE id=?", [(int)$id]);
        if (!$product) { $this->flash('danger', 'Tidak ditemukan'); $this->redirect('/admin/products'); }
        $brands = Database::all("SELECT * FROM brands ORDER BY name");
        $cats = Database::all("SELECT * FROM categories ORDER BY name");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'brand_id' => (int)$this->input('brand_id'),
                'category_id' => (int)$this->input('category_id'),
                'name' => $this->input('name'),
                'sku_base' => $this->input('sku_base'),
                'description' => $this->input('description'),
                'specifications' => json_encode($this->parseSpecs($this->input('specifications', '')), JSON_UNESCAPED_UNICODE),
                'is_featured' => $this->input('is_featured') ? 1 : 0,
                'is_new' => $this->input('is_new') ? 1 : 0,
                'is_active' => $this->input('is_active') ? 1 : 0,
            ];
            $thumb = $this->handleUpload('thumbnail', 'products');
            if ($thumb) $data['thumbnail'] = $thumb;
            Database::update('products', $data, 'id=?', [(int)$id]);
            $this->flash('success', 'Produk diperbarui.');
            $this->redirect('/admin/products');
        }
        $product['specifications_text'] = $this->specsToText(json_decode($product['specifications'], true));
        $this->view('admin.products.form', ['pageTitle' => 'Edit Produk', 'brands' => $brands, 'cats' => $cats, 'product' => $product], 'admin');
    }

    public function delete($id) {
        Database::delete('products', 'id=?', [(int)$id]);
        $this->flash('success', 'Produk dihapus.');
        $this->redirect('/admin/products');
    }

    public function variants($id) {
        $product = Database::row("SELECT * FROM products WHERE id=?", [(int)$id]);
        if (!$product) { $this->redirect('/admin/products'); }
        $attrs = Database::all("SELECT * FROM product_variant_attributes WHERE product_id=?", [(int)$id]);
        foreach ($attrs as &$a) $a['values'] = json_decode($a['values'], true);
        $variants = Database::all("SELECT * FROM product_variants WHERE product_id=?", [(int)$id]);
        foreach ($variants as &$v) {
            $v['combination'] = json_decode($v['combination'], true);
        }
        unset($v);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Save attributes & generate combinations
            $attrJson = $this->input('attributes_json', '[]');
            $attributes = json_decode($attrJson, true) ?: [];
            Database::delete('product_variant_attributes', 'product_id=?', [(int)$id]);
            foreach ($attributes as $a) {
                Database::insert('product_variant_attributes', [
                    'product_id' => (int)$id, 'attribute' => $a['name'],
                    'values' => json_encode($a['values']),
                ]);
            }
            $this->flash('success', 'Atribut varian disimpan. Sekarang Anda bisa generate kombinasi.');
            $this->redirect('/admin/products/variants/' . $id);
        }
        $this->view('admin.products.variants', ['pageTitle' => 'Varian: ' . $product['name'], 'product' => $product, 'attrs' => $attrs, 'variants' => $variants], 'admin');
    }

    public function saveVariant() {
        $productId = (int) $this->input('product_id');
        $id = (int) $this->input('id', 0);
        $combination = $this->input('combination');
        $data = [
            'product_id' => $productId,
            'sku' => $this->input('sku'),
            'combination' => is_string($combination) ? $combination : json_encode($combination),
            'price' => (float)$this->input('price'),
            'price_promo' => $this->input('price_promo') ? (float)$this->input('price_promo') : null,
            'stock' => (int)$this->input('stock'),
            'weight' => (int)$this->input('weight', 300),
            'is_active' => 1,
        ];
        if ($id) {
            Database::update('product_variants', $data, 'id=?', [$id]);
        } else {
            // Check duplicate SKU
            if (Database::row("SELECT id FROM product_variants WHERE sku=?", [$data['sku']])) {
                $this->json(['success' => false, 'message' => 'SKU sudah dipakai']);
            }
            $id = Database::insert('product_variants', $data);
        }
        $this->json(['success' => true, 'id' => $id]);
    }

    public function deleteVariant($id) {
        $v = Database::row("SELECT product_id FROM product_variants WHERE id=?", [(int)$id]);
        Database::delete('product_variants', 'id=?', [(int)$id]);
        $this->json(['success' => true]);
    }

    private function parseSpecs(string $text): array {
        $arr = [];
        foreach (explode("\n", $text) as $line) {
            if (strpos($line, ':') === false) continue;
            [$k, $v] = explode(':', $line, 2);
            $arr[trim($k)] = trim($v);
        }
        return $arr;
    }
    private function specsToText(?array $arr): string {
        if (!$arr) return '';
        $out = [];
        foreach ($arr as $k => $v) $out[] = "$k: $v";
        return implode("\n", $out);
    }

    private function handleUpload(string $field, string $folder): ?string {
        if (empty($_FILES[$field]['tmp_name'])) return null;
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp','gif','svg'])) return null;
        if ($_FILES[$field]['size'] > 5 * 1024 * 1024) return null;
        $fname = $folder . '_' . time() . '_' . random_int(1000, 9999) . '.' . $ext;
        $dest = BASE_PATH . '/uploads/' . $folder . '/' . $fname;
        if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0777, true);
        return move_uploaded_file($_FILES[$field]['tmp_name'], $dest) ? $fname : null;
    }
}

class AdminBrandsController extends AdminBaseController {
    public function index() {
        $brands = Database::all("SELECT * FROM brands ORDER BY sort_order, name");
        $this->view('admin.brands.index', ['pageTitle' => 'Brand', 'brands' => $brands], 'admin');
    }
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $slug = slugify($this->input('name'));
            Database::insert('brands', [
                'name' => $this->input('name'), 'slug' => $slug,
                'description' => $this->input('description'),
                'is_featured' => $this->input('is_featured') ? 1 : 0,
                'sort_order' => (int)$this->input('sort_order'),
            ]);
            $this->flash('success', 'Brand ditambahkan.');
            $this->redirect('/admin/brands');
        }
        $this->view('admin.brands.form', ['pageTitle' => 'Tambah Brand', 'brand' => null], 'admin');
    }
    public function edit($id) {
        $brand = Database::row("SELECT * FROM brands WHERE id=?", [(int)$id]);
        if (!$brand) $this->redirect('/admin/brands');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Database::update('brands', [
                'name' => $this->input('name'),
                'description' => $this->input('description'),
                'is_featured' => $this->input('is_featured') ? 1 : 0,
                'sort_order' => (int)$this->input('sort_order'),
            ], 'id=?', [(int)$id]);
            $this->flash('success', 'Brand diperbarui.');
            $this->redirect('/admin/brands');
        }
        $this->view('admin.brands.form', ['pageTitle' => 'Edit Brand', 'brand' => $brand], 'admin');
    }
    public function delete($id) {
        try { Database::delete('brands', 'id=?', [(int)$id]); $this->flash('success', 'Brand dihapus.'); }
        catch (Throwable $e) { $this->flash('danger', 'Tidak dapat dihapus, masih dipakai produk.'); }
        $this->redirect('/admin/brands');
    }
}

class AdminCategoriesController extends AdminBaseController {
    public function index() {
        $cats = Database::all("SELECT * FROM categories ORDER BY sort_order, name");
        $this->view('admin.categories.index', ['pageTitle' => 'Kategori', 'cats' => $cats], 'admin');
    }
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Database::insert('categories', [
                'name' => $this->input('name'), 'slug' => slugify($this->input('name')),
                'icon' => $this->input('icon', 'box'),
                'description' => $this->input('description'),
                'is_active' => 1, 'sort_order' => (int)$this->input('sort_order'),
            ]);
            $this->flash('success', 'Kategori ditambahkan.'); $this->redirect('/admin/categories');
        }
        $this->view('admin.categories.form', ['pageTitle' => 'Tambah Kategori', 'cat' => null], 'admin');
    }
    public function edit($id) {
        $cat = Database::row("SELECT * FROM categories WHERE id=?", [(int)$id]);
        if (!$cat) $this->redirect('/admin/categories');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Database::update('categories', [
                'name' => $this->input('name'),
                'icon' => $this->input('icon', 'box'),
                'description' => $this->input('description'),
                'is_active' => $this->input('is_active') ? 1 : 0,
                'sort_order' => (int)$this->input('sort_order'),
            ], 'id=?', [(int)$id]);
            $this->flash('success', 'Diperbarui.'); $this->redirect('/admin/categories');
        }
        $this->view('admin.categories.form', ['pageTitle' => 'Edit Kategori', 'cat' => $cat], 'admin');
    }
    public function delete($id) {
        try { Database::delete('categories', 'id=?', [(int)$id]); $this->flash('success', 'Dihapus.'); }
        catch (Throwable $e) { $this->flash('danger', 'Masih dipakai produk.'); }
        $this->redirect('/admin/categories');
    }
}
