# 🏪 PIUL CELL — Toko HP & Aksesoris Online

E-commerce platform B2C lengkap dengan **Buyer + Admin + Owner** panel, dibangun dengan **PHP 8.x + MySQL/MariaDB** mengikuti spesifikasi resmi `PIUL_CELL_PROJECT.md`.

## ✨ Fitur Utama
- 🛍️ **Buyer**: Homepage hero slider, katalog dgn filter, detail produk dgn varian dinamis, cart AJAX, checkout, payment manual (Transfer Bank + QRIS + COD), wishlist, review, akun lengkap
- 🧰 **Admin**: Dashboard KPI, CRUD produk + varian, brand, kategori, pesanan, verifikasi pembayaran, voucher, banner, ulasan, pelanggan, zona ongkir, pengaturan toko
- 📊 **Owner**: KPI bisnis, analitik konversi, financial bulanan, reports periode
- 🖨️ **Print-friendly invoice** (PDF via browser Print)
- 🌗 Dark mode toggle (persistent)
- 📱 Responsive mobile

## 🛠️ Tech Stack
- **PHP 7.4+ / 8.x** (kompatibel keduanya) + custom CodeIgniter-style MVC framework
- **MariaDB 10.3+ / MySQL 5.7+** (PDO)
- **Bootstrap Icons** + Plus Jakarta Sans + Sora font (CDN)
- **Chart.js 4** untuk grafik admin/owner
- **Nominatim OpenStreetMap** untuk geocoding alamat (gratis, tanpa API key)
- **Haversine formula** untuk hitung jarak & ongkir
- Server-rendered views, AJAX untuk cart & checkout
- **Confetti** + animasi penuh di semua halaman

---

## 🚀 Cara Install di Lokal (Laragon — Windows)

### 1. Prasyarat
- **Laragon Full** (download di https://laragon.org) — sudah include Apache/Nginx + PHP 8 + MySQL/MariaDB + HeidiSQL
- PHP 8.1 atau lebih baru
- Browser modern (Chrome/Edge/Firefox)

### 2. Extract Proyek
1. Extract `piulcell.zip` ke folder `C:\laragon\www\piulcell`  
   Struktur akhir:
   ```
   C:\laragon\www\piulcell\
   ├── public\
   ├── app\
   ├── assets\
   ├── uploads\
   ├── database\
   └── README.md
   ```

### 3. Buat Database
1. Buka Laragon → klik **Database** → buka HeidiSQL (atau via menu `Menu → MySQL → HeidiSQL`)
2. Login (default Laragon: user `root`, password kosong)
3. Buat database baru via menu **File → New → Database**:
   - Nama: `piulcell_db`
   - Collation: `utf8mb4_unicode_ci`
4. Pilih database `piulcell_db` di sidebar kiri, lalu **File → Load SQL file** → pilih:
   - `database/schema.sql` → eksekusi (F9)
   - `database/seed.sql` → eksekusi (F9)

> ✅ Selesai. DB sudah berisi 10 produk, 25 varian, 8 brand, 3 user demo, voucher, banner, dll.

### 4. Konfigurasi Database (HANYA jika password root ≠ kosong)
Edit file `app/Core/Database.php` baris 9–14, ganti baris:
```php
self::$pdo = new PDO(
    'mysql:unix_socket=/run/mysqld/mysqld.sock;dbname=piulcell_db;charset=utf8mb4',
    'root',
    '',
    [...]
);
```
menjadi (Windows Laragon default):
```php
self::$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=piulcell_db;charset=utf8mb4',
    'root',
    '', // ganti dengan password MySQL Anda kalau ada
    [...]
);
```

### 5. Jalankan via Laragon (Apache)
1. Restart Laragon (klik **Stop All** lalu **Start All**)
2. Buka browser ke `http://piulcell.test/public/`  
   (Laragon auto-create virtual host dari nama folder)

   **ATAU** akses langsung di `http://localhost/piulcell/public/`

3. Tab "Pretty URL" Laragon **HARUS ON** (Menu → Apache → URL Rewrite). 

### 6. (Alternatif lebih praktis) Jalankan PHP Built-in Server
Buka **Terminal** (Laragon → Menu → Terminal), lalu:

```bash
cd C:\laragon\www\piulcell
php -S 0.0.0.0:8080 -t public public/router.php
```

Buka browser: `http://localhost:8080`

> Cara ini **paling rekomended** karena routing langsung work tanpa Apache/Nginx config.

---

## 🔐 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| **Owner** | owner@piulcell.com | `PiulOwner2024!` |
| **Admin** | admin@piulcell.com | `PiulAdmin2024!` |
| **Buyer** | buyer@piulcell.com | `Buyer2024!` |

## 🎟️ Voucher Demo
- `PIULCELL10` → Diskon 10% (maks Rp 200.000, min belanja Rp 1.000.000)
- `GRATIS50` → Potongan Rp 50.000 (min Rp 500.000)
- `FREEONGKIR` → Gratis ongkir

---

## 📁 Struktur Folder

```
piulcell/
├── public/
│   ├── index.php       # Front controller (entry point)
│   └── router.php      # Router untuk php -S
├── app/
│   ├── Core/
│   │   ├── Database.php     # PDO singleton
│   │   ├── Controller.php   # Base controller (auth, view, csrf)
│   │   ├── Router.php       # Simple regex router
│   │   └── helpers.php      # Helper functions global
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── AuthController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── PaymentController.php
│   │   ├── ShippingController.php
│   │   ├── AccountController.php
│   │   ├── InvoiceController.php
│   │   ├── PageController.php
│   │   ├── Admin/
│   │   │   ├── AdminCore.php    # Dashboard, Products, Brands, Categories
│   │   │   └── AdminAll.php     # Orders, Payments, Customers, Vouchers, Banners, Reviews, Shipping, Settings, Users
│   │   └── Owner/
│   │       └── OwnerAll.php     # Dashboard, Analytics, Financial, Reports
│   ├── Views/
│   │   ├── layouts/    # main.php, admin.php
│   │   ├── partials/   # navbar.php, footer.php, sidebar_admin.php, account_sidebar.php, product_card.php
│   │   ├── home/, product/, cart/, checkout/, payment/, auth/, account/, static/
│   │   ├── admin/      # dashboard.php + subfolder per modul
│   │   └── owner/      # dashboard.php, analytics.php, financial.php, reports.php
│   └── routes.php
├── assets/
│   ├── css/main.css    # Single design system (Apple-style)
│   ├── js/app.js       # Global JS (cart, toast, theme, slider)
│   └── img/            # products/, brands/, banners/, placeholder.svg
├── uploads/            # products/, payment_proofs/, qris/, banners/, brands/, avatars/, reviews/
├── database/
│   ├── schema.sql      # Struktur tabel (DROP + CREATE)
│   └── seed.sql        # Data demo
├── logs/               # PHP error log
├── storage/sessions/   # PHP session files
└── README.md           # File ini
```

---

## 🌐 Routing (URL Map)

### Public
- `/` — Beranda
- `/katalog` — Katalog (filter: `?brand=`, `?kategori=`, `?promo=1`, `?sort=`, `?q=`)
- `/produk/{slug}` — Detail produk
- `/cari?q=...&ajax=1` — Search suggest (AJAX)
- `/page/{slug}` — Halaman statis (tentang-kami, faq, dll)

### Auth
- `/login`, `/register`, `/logout`, `/forgot-password`, `/reset-password`

### Buyer
- `/cart`, `/cart/add` (POST), `/cart/update`, `/cart/remove`
- `/checkout`, `/checkout/process` (POST)
- `/voucher/validate` (POST), `/shipping/calculate` (POST)
- `/payment/instructions/{id}`, `/payment/upload-proof/{id}` (POST)
- `/akun`, `/akun/alamat`, `/akun/pesanan`, `/akun/pesanan/{id}`, `/akun/wishlist`, `/akun/notifikasi`
- `/wishlist/toggle` (POST)
- `/invoice/{id}` — **Print-friendly invoice**

### Admin
- `/admin/dashboard`
- `/admin/products`, `/admin/products/create`, `/admin/products/edit/{id}`, `/admin/products/variants/{id}`
- `/admin/brands`, `/admin/categories`, `/admin/orders`, `/admin/orders/{id}`
- `/admin/payments/pending`, `/admin/payments`, `/admin/payment-accounts`
- `/admin/vouchers`, `/admin/banners`, `/admin/reviews`
- `/admin/customers`, `/admin/shipping-zones`, `/admin/settings`, `/admin/users`

### Owner
- `/owner/dashboard`, `/owner/analytics`, `/owner/financial`, `/owner/reports`

---

## 🐛 Troubleshooting

### "PDO::__construct(): mysql:unix_socket... not supported"
Anda di Windows. Edit `app/Core/Database.php` ganti dari `unix_socket=...` ke `host=127.0.0.1;port=3306` (lihat step 4 di atas).

### Halaman 404 saat akses route selain `/`
Aktifkan **URL Rewrite** di Laragon (Menu → Apache → URL Rewrite **ON**), atau pakai `php -S` (lihat step 6).

### "View not found" error
Pastikan folder `app/Views/...` ter-extract lengkap. Cek case-sensitive (Linux/Mac).

### Upload bukti bayar gagal
Pastikan folder `uploads/` writable. Di Windows biasanya tidak masalah. Di Linux: `chmod -R 777 uploads/`.

### Database connection refused
Cek MySQL/MariaDB running. Di Laragon: klik tombol **MySQL** harus hijau.

### Gambar produk tidak muncul
SVG mockup ada di `assets/img/products/`. Pastikan folder ini tidak hilang saat extract.

---

## 📜 Lisensi
Project belajar / demo. Bebas digunakan untuk keperluan komersial maupun non-komersial.

## 🙏 Credits
- Spesifikasi: `PIUL_CELL_PROJECT.md`
- Build by: **E1 (Emergent AI)** — Jun 2026
- Inspired by Apple.com design language

---

**Selamat berjualan! 🚀**
