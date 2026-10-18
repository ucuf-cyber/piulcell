<?php
// ============================================
// Base Controller
// ============================================

class Controller {
    protected ?array $user = null;
    protected array $shared = [];

    public function __construct() {
        if (!empty($_SESSION['user_id'])) {
            $this->user = Database::row("SELECT * FROM users WHERE id=? AND is_active=1", [$_SESSION['user_id']]);
            if (!$this->user) {
                session_destroy();
                $this->user = null;
            }
        }
        $this->shared['user'] = $this->user;
        $this->shared['cart_count'] = $this->cartCount();
        $this->shared['wishlist_count'] = $this->wishlistCount();
        $this->shared['settings'] = $this->loadSettings();
        $this->shared['nav_brands'] = Database::all("SELECT id,name,slug FROM brands ORDER BY sort_order LIMIT 8");
        $this->shared['nav_categories'] = Database::all("SELECT id,name,slug FROM categories WHERE is_active=1 ORDER BY sort_order");
        if ($this->user) {
            $this->shared['notif_unread'] = (int) Database::val("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0", [$this->user['id']]);
        } else {
            $this->shared['notif_unread'] = 0;
        }
    }

    protected function loadSettings(): array {
        static $s = null;
        if ($s !== null) return $s;
        $rows = Database::all("SELECT key_name, value FROM store_settings");
        $s = [];
        foreach ($rows as $r) $s[$r['key_name']] = $r['value'];
        return $s;
    }

    protected function view(string $template, array $data = [], ?string $layout = 'main'): void {
        $data = array_merge($this->shared, $data);
        extract($data);
        $viewFile = APP_PATH . '/Views/' . str_replace('.', '/', $template) . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View not found: {$template}";
            return;
        }
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        if ($layout) {
            include APP_PATH . '/Views/layouts/' . $layout . '.php';
        } else {
            echo $content;
        }
    }

    protected function json($data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }

    protected function requireAuth(string $role = 'buyer'): void {
        if (!$this->user) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
            $this->redirect('/login');
        }
        if ($role === 'admin' && !in_array($this->user['role'], ['admin', 'owner'])) {
            $this->redirect('/');
        }
        if ($role === 'owner' && $this->user['role'] !== 'owner') {
            $this->redirect('/admin');
        }
    }

    protected function cartCount(): int {
        if ($this->user) {
            return (int) Database::val("SELECT COALESCE(SUM(quantity),0) FROM carts WHERE user_id=?", [$this->user['id']]);
        }
        $sid = session_id();
        return (int) Database::val("SELECT COALESCE(SUM(quantity),0) FROM carts WHERE session_id=?", [$sid]);
    }

    protected function wishlistCount(): int {
        if (!$this->user) return 0;
        return (int) Database::val("SELECT COUNT(*) FROM wishlists WHERE user_id=?", [$this->user['id']]);
    }

    protected function input(string $key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function csrfCheck(): bool {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return hash_equals($_SESSION['_csrf'] ?? '', $token);
    }

    protected function flash(string $type, string $msg): void {
        $_SESSION['_flash'][] = ['type' => $type, 'msg' => $msg];
    }
}
