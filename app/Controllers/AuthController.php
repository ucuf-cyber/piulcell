<?php

class AuthController extends Controller {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($this->input('email', ''));
            $password = (string) $this->input('password', '');
            $user = Database::row("SELECT * FROM users WHERE email=? AND is_active=1", [$email]);
            if (!$user || !password_verify($password, $user['password'])) {
                $this->flash('danger', 'Email atau password salah.');
                $this->redirect('/login');
            }
            $_SESSION['user_id'] = (int) $user['id'];
            $this->flash('success', 'Selamat datang, ' . $user['name'] . '!');
            $redirect = $_SESSION['redirect_after_login'] ?? null;
            unset($_SESSION['redirect_after_login']);
            // Merge guest cart
            $this->mergeGuestCart((int) $user['id']);
            if ($user['role'] === 'admin') $this->redirect('/admin/dashboard');
            if ($user['role'] === 'owner') $this->redirect('/owner/dashboard');
            $this->redirect($redirect ?? '/');
        }
        $this->view('auth.login', ['pageTitle' => 'Masuk — Piul Cell']);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($this->input('name', ''));
            $email = trim($this->input('email', ''));
            $phone = trim($this->input('phone', ''));
            $password = (string) $this->input('password', '');
            $password2 = (string) $this->input('password_confirm', '');
            if (!$name || !$email || !$password) { $this->flash('danger', 'Lengkapi semua field.'); $this->redirect('/register'); }
            if (strlen($password) < 8) { $this->flash('danger', 'Password minimal 8 karakter.'); $this->redirect('/register'); }
            if ($password !== $password2) { $this->flash('danger', 'Konfirmasi password tidak cocok.'); $this->redirect('/register'); }
            if (Database::row("SELECT id FROM users WHERE email=?", [$email])) {
                $this->flash('danger', 'Email sudah terdaftar.'); $this->redirect('/register');
            }
            $uid = Database::insert('users', [
                'name' => $name, 'email' => $email, 'phone' => $phone,
                'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]),
                'role' => 'buyer', 'is_active' => 1,
            ]);
            $_SESSION['user_id'] = $uid;
            $this->mergeGuestCart($uid);
            $this->flash('success', 'Pendaftaran berhasil! Selamat datang.');
            $this->redirect('/');
        }
        $this->view('auth.register', ['pageTitle' => 'Daftar — Piul Cell']);
    }

    public function logout() {
        session_destroy();
        header('Location: /'); exit;
    }

    public function forgot() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($this->input('email', ''));
            $user = Database::row("SELECT id, name FROM users WHERE email=?", [$email]);
            if ($user) {
                $token = bin2hex(random_bytes(16));
                Database::update('users', ['remember_token' => $token], 'id=?', [$user['id']]);
                $this->flash('info', "Link reset password (DEMO): /reset-password?token=$token");
            } else {
                $this->flash('info', 'Jika email terdaftar, link reset akan dikirim.');
            }
            $this->redirect('/forgot-password');
        }
        $this->view('auth.forgot', ['pageTitle' => 'Lupa Password — Piul Cell']);
    }

    public function reset() {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';
        if (!$token) { $this->redirect('/forgot-password'); }
        $user = Database::row("SELECT id FROM users WHERE remember_token=?", [$token]);
        if (!$user) { $this->flash('danger', 'Token tidak valid.'); $this->redirect('/forgot-password'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $p = (string)$this->input('password', '');
            $p2 = (string)$this->input('password_confirm', '');
            if (strlen($p) < 8 || $p !== $p2) { $this->flash('danger', 'Password tidak valid / tidak cocok.'); $this->redirect('/reset-password?token=' . $token); }
            Database::update('users', ['password' => password_hash($p, PASSWORD_BCRYPT, ['cost'=>10]), 'remember_token' => null], 'id=?', [$user['id']]);
            $this->flash('success', 'Password berhasil diubah. Silakan masuk.');
            $this->redirect('/login');
        }
        $this->view('auth.reset', ['pageTitle' => 'Reset Password', 'token' => $token]);
    }

    private function mergeGuestCart(int $userId): void {
        $sid = session_id();
        $guestItems = Database::all("SELECT * FROM carts WHERE session_id=?", [$sid]);
        foreach ($guestItems as $gi) {
            $existing = Database::row("SELECT * FROM carts WHERE user_id=? AND variant_id=?", [$userId, $gi['variant_id']]);
            if ($existing) {
                Database::update('carts', ['quantity' => $existing['quantity'] + $gi['quantity']], 'id=?', [$existing['id']]);
            } else {
                Database::update('carts', ['user_id' => $userId, 'session_id' => null], 'id=?', [$gi['id']]);
            }
        }
        Database::delete('carts', 'session_id=?', [$sid]);
    }
}
