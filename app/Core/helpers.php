<?php
// ============================================
// Global helpers
// ============================================

function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function rupiah($n): string {
    return 'Rp ' . number_format((float) $n, 0, ',', '.');
}

function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}

function flash_messages(): array {
    $msgs = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $msgs;
}

function active_when(string $path, string $exact = ''): string {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($exact === 'exact') return $uri === $path ? 'active' : '';
    return strpos($uri, $path) === 0 ? 'active' : '';
}

function get_setting(string $key, $default = null) {
    $v = Database::val("SELECT value FROM store_settings WHERE key_name=?", [$key]);
    return $v ?? $default;
}

function asset(string $path): string {
    return '/assets/' . ltrim($path, '/');
}

function upload(string $path): string {
    return '/uploads/' . ltrim($path, '/');
}

function product_image(?string $name, string $folder = 'products'): string {
    if (!$name) return placeholder_image();
    // Try .svg version first
    $svgName = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '.svg', $name);
    $up = BASE_PATH . '/uploads/' . $folder . '/' . $name;
    if (file_exists($up)) return upload($folder . '/' . $name);
    $imgSvg = BASE_PATH . '/assets/img/' . $folder . '/' . $svgName;
    if (file_exists($imgSvg)) return asset('img/' . $folder . '/' . $svgName);
    $img = BASE_PATH . '/assets/img/' . $folder . '/' . $name;
    if (file_exists($img)) return asset('img/' . $folder . '/' . $name);
    return placeholder_image();
}

function placeholder_image(): string {
    return asset('img/placeholder.svg');
}

function brand_logo(?string $name): string {
    if (!$name) return placeholder_image();
    $f = BASE_PATH . '/assets/img/brands/' . $name;
    if (file_exists($f)) return asset('img/brands/' . $name);
    return placeholder_image();
}

function star_rating(float $rating, int $count = 0): string {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    $html = '<span class="rating-stars">';
    for ($i = 0; $i < $full; $i++) $html .= '<i class="bi bi-star-fill"></i>';
    if ($half) $html .= '<i class="bi bi-star-half"></i>';
    for ($i = 0; $i < $empty; $i++) $html .= '<i class="bi bi-star"></i>';
    $html .= '</span>';
    if ($count) $html .= ' <span class="rating-count">(' . $count . ')</span>';
    return $html;
}

function variant_price(array $v): array {
    $promo = !empty($v['price_promo']) && $v['price_promo'] > 0 && $v['price_promo'] < $v['price'];
    return [
        'price' => (float) $v['price'],
        'price_promo' => $promo ? (float) $v['price_promo'] : null,
        'final' => $promo ? (float) $v['price_promo'] : (float) $v['price'],
        'discount_pct' => $promo ? round((1 - $v['price_promo'] / $v['price']) * 100) : 0,
    ];
}

function product_default_variant(int $productId): ?array {
    return Database::row("SELECT * FROM product_variants WHERE product_id=? AND is_active=1 ORDER BY id LIMIT 1", [$productId]);
}

function product_min_price(int $productId): ?array {
    $rows = Database::all("SELECT price, price_promo FROM product_variants WHERE product_id=? AND is_active=1", [$productId]);
    if (!$rows) return null;
    $finals = array_map(fn($r) => !empty($r['price_promo']) ? (float)$r['price_promo'] : (float)$r['price'], $rows);
    $min = min($finals);
    $max = max($finals);
    return ['min' => $min, 'max' => $max];
}

function product_avg_rating(int $productId): array {
    $r = Database::row("SELECT AVG(rating) avg_r, COUNT(*) cnt FROM reviews WHERE product_id=? AND is_approved=1", [$productId]);
    return ['avg' => (float)($r['avg_r'] ?? 0), 'count' => (int)($r['cnt'] ?? 0)];
}

function generate_order_number(): string {
    return 'PIUL-' . date('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
}

function time_ago(string $datetime): string {
    $t = strtotime($datetime);
    $diff = time() - $t;
    if ($diff < 60) return 'Baru saja';
    if ($diff < 3600) return floor($diff/60) . ' menit lalu';
    if ($diff < 86400) return floor($diff/3600) . ' jam lalu';
    if ($diff < 2592000) return floor($diff/86400) . ' hari lalu';
    return date('d M Y', $t);
}

function slugify(string $s): string {
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9-]+/', '-', $s);
    return trim($s, '-');
}

function haversine_km(float $lat1, float $lng1, float $lat2, float $lng2): float {
    $R = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) ** 2;
    return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

function order_status_label(string $status): string {
    return [
        'pending' => 'Menunggu Pembayaran',
        'confirmed' => 'Menunggu Verifikasi',
        'processing' => 'Sedang Diproses',
        'shipped' => 'Dikirim',
        'delivered' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'refunded' => 'Refund',
    ][$status] ?? $status;
}

function order_status_badge(string $status): string {
    $cls = [
        'pending' => 'badge-warning',
        'confirmed' => 'badge-info',
        'processing' => 'badge-primary',
        'shipped' => 'badge-primary',
        'delivered' => 'badge-success',
        'cancelled' => 'badge-danger',
        'refunded' => 'badge-secondary',
    ][$status] ?? 'badge-secondary';
    return '<span class="badge ' . $cls . '">' . order_status_label($status) . '</span>';
}

function payment_method_label(string $m): string {
    return ['transfer_bank' => 'Transfer Bank', 'qris' => 'QRIS', 'cod' => 'Bayar di Tempat (COD)'][$m] ?? $m;
}

function payment_status_label(string $s): string {
    return [
        'pending' => 'Menunggu Bukti',
        'waiting_verification' => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi',
        'rejected' => 'Ditolak',
        'cod_pending' => 'COD - Belum Lunas',
        'cod_done' => 'COD - Lunas',
    ][$s] ?? $s;
}
