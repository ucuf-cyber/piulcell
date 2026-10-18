<?php

class ProductController extends Controller {
    public function catalog() {
        $brand = $_GET['brand'] ?? '';
        $category = $_GET['kategori'] ?? '';
        $promo = !empty($_GET['promo']);
        $minPrice = (int)($_GET['min'] ?? 0);
        $maxPrice = (int)($_GET['max'] ?? 0);
        $sort = $_GET['sort'] ?? 'newest';
        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 16;

        $where = ['p.is_active=1'];
        $params = [];
        if ($brand)    { $where[] = 'b.slug = ?'; $params[] = $brand; }
        if ($category) { $where[] = 'c.slug = ?'; $params[] = $category; }
        if ($q)        { $where[] = '(p.name LIKE ? OR p.description LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
        if ($promo) {
            $where[] = 'EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id=p.id AND pv.price_promo IS NOT NULL AND pv.price_promo > 0 AND pv.price_promo < pv.price)';
        }
        $whereSql = implode(' AND ', $where);

        switch ($sort) {
            case 'price_asc':
                $sortSql = '(SELECT MIN(IFNULL(price_promo, price)) FROM product_variants WHERE product_id=p.id) ASC';
                break;
            case 'price_desc':
                $sortSql = '(SELECT MIN(IFNULL(price_promo, price)) FROM product_variants WHERE product_id=p.id) DESC';
                break;
            case 'popular':
                $sortSql = 'p.sold_count DESC';
                break;
            case 'rating':
                $sortSql = '(SELECT AVG(rating) FROM reviews WHERE product_id=p.id AND is_approved=1) DESC';
                break;
            default:
                $sortSql = 'p.id DESC';
        }

        $countSql = "SELECT COUNT(DISTINCT p.id) FROM products p JOIN brands b ON b.id=p.brand_id JOIN categories c ON c.id=p.category_id WHERE $whereSql";
        $total = (int) Database::val($countSql, $params);
        $pages = max(1, (int) ceil($total / $perPage));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT p.*, b.name AS brand_name, b.slug AS brand_slug, c.name AS category_name
                FROM products p JOIN brands b ON b.id=p.brand_id JOIN categories c ON c.id=p.category_id
                WHERE $whereSql ORDER BY $sortSql LIMIT $perPage OFFSET $offset";
        $products = Database::all($sql, $params);

        $allBrands = Database::all("SELECT * FROM brands ORDER BY sort_order");
        $allCats = Database::all("SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order");

        $this->view('product.catalog', [
            'pageTitle' => 'Katalog Produk — Piul Cell',
            'products' => $products,
            'allBrands' => $allBrands,
            'allCats' => $allCats,
            'filters' => compact('brand','category','promo','minPrice','maxPrice','sort','q'),
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    public function detail(string $slug) {
        $product = Database::row("SELECT p.*, b.name AS brand_name, b.slug AS brand_slug, c.name AS category_name, c.slug AS category_slug
                                  FROM products p JOIN brands b ON b.id=p.brand_id JOIN categories c ON c.id=p.category_id
                                  WHERE p.slug=? AND p.is_active=1", [$slug]);
        if (!$product) {
            http_response_code(404);
            $this->view('static.404', ['pageTitle' => 'Tidak ditemukan']);
            return;
        }
        Database::q("UPDATE products SET view_count = view_count + 1 WHERE id=?", [$product['id']]);

        $attrs = Database::all("SELECT * FROM product_variant_attributes WHERE product_id=?", [$product['id']]);
        foreach ($attrs as &$a) { $a['values'] = json_decode($a['values'], true); }
        unset($a);

        $variants = Database::all("SELECT * FROM product_variants WHERE product_id=? AND is_active=1 ORDER BY id", [$product['id']]);
        foreach ($variants as &$v) {
            $v['combination'] = json_decode($v['combination'], true);
            $v['images'] = json_decode($v['images'], true) ?: [];
            $v['final'] = !empty($v['price_promo']) ? (float)$v['price_promo'] : (float)$v['price'];
        }
        unset($v);

        $reviews = Database::all("SELECT r.*, u.name AS user_name FROM reviews r JOIN users u ON u.id=r.user_id WHERE r.product_id=? AND r.is_approved=1 ORDER BY r.created_at DESC LIMIT 10", [$product['id']]);
        $ratingInfo = product_avg_rating($product['id']);
        $related = Database::all("SELECT p.* FROM products p WHERE p.category_id=? AND p.id<>? AND p.is_active=1 ORDER BY RAND() LIMIT 5", [$product['category_id'], $product['id']]);
        $product['specifications'] = json_decode($product['specifications'], true) ?: [];

        $inWishlist = false;
        if ($this->user) {
            $inWishlist = (bool) Database::val("SELECT 1 FROM wishlists WHERE user_id=? AND product_id=?", [$this->user['id'], $product['id']]);
        }

        $this->view('product.detail', [
            'pageTitle' => $product['name'] . ' — ' . ($this->shared['settings']['store_name'] ?? 'Piul Cell'),
            'product' => $product,
            'attributes' => $attrs,
            'variants' => $variants,
            'reviews' => $reviews,
            'ratingInfo' => $ratingInfo,
            'related' => $related,
            'inWishlist' => $inWishlist,
        ]);
    }

    public function search() {
        $q = trim($_GET['q'] ?? '');
        if (!empty($_GET['ajax'])) {
            if (strlen($q) < 2) { $this->json(['items' => []]); return; }
            $rows = Database::all("SELECT p.id, p.name, p.slug, p.thumbnail, b.name AS brand
                                   FROM products p JOIN brands b ON b.id=p.brand_id
                                   WHERE p.is_active=1 AND p.name LIKE ? ORDER BY p.sold_count DESC LIMIT 6", ["%$q%"]);
            $items = [];
            foreach ($rows as $r) {
                $mp = product_min_price($r['id']);
                $items[] = [
                    'name' => $r['name'],
                    'slug' => $r['slug'],
                    'brand' => $r['brand'],
                    'thumb' => product_image($r['thumbnail']),
                    'price' => number_format($mp['min'] ?? 0, 0, ',', '.'),
                ];
            }
            $this->json(['items' => $items]);
            return;
        }
        // Redirect to catalog with q
        $this->redirect('/katalog?q=' . urlencode($q));
    }
}
