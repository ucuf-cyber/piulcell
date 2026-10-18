<?php
// ============================================
// Simple Router with named params
// ============================================

class Router {
    private array $routes = [];

    public function get(string $path, $handler): void { $this->add('GET', $path, $handler); }
    public function post(string $path, $handler): void { $this->add('POST', $path, $handler); }
    public function any(string $path, $handler): void {
        $this->add('GET', $path, $handler);
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, $handler): void {
        $regex = preg_replace('#\{([a-z_]+)\}#i', '(?P<$1>[^/]+)', $path);
        $this->routes[] = ['method' => $method, 'regex' => '#^' . $regex . '$#', 'handler' => $handler];
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $r) {
            if ($r['method'] !== $method) continue;
            if (preg_match($r['regex'], $uri, $m)) {
                $params = array_filter($m, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
                $this->invoke($r['handler'], $params);
                return;
            }
        }
        http_response_code(404);
        $c = new Controller();
        require APP_PATH . '/Views/static/404.php';
    }

    private function invoke($handler, array $params): void {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }
        // "Controller@method"
        [$ctrlName, $method] = explode('@', $handler);
        if (!class_exists($ctrlName)) {
            // Try admin/owner namespaces
            $tryPaths = [
                APP_PATH . '/Controllers/' . $ctrlName . '.php',
                APP_PATH . '/Controllers/Admin/' . str_replace('Admin', '', $ctrlName) . '.php',
                APP_PATH . '/Controllers/Owner/' . str_replace('Owner', '', $ctrlName) . '.php',
            ];
            foreach ($tryPaths as $p) {
                if (file_exists($p)) { require_once $p; break; }
            }
        }
        $ctrl = new $ctrlName();
        call_user_func_array([$ctrl, $method], array_values($params));
    }
}
