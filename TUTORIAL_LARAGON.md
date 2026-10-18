# 🚀 PIUL CELL — Tutorial Install di Laragon (Windows)

Panduan **lengkap step-by-step** menjalankan Piul Cell di komputer lokal Anda menggunakan **Laragon** (recommended untuk Windows).

## 📥 1. Download & Install Laragon

1. Buka https://laragon.org/download/
2. Download **Laragon Full** (~150 MB)
3. Install ke `C:\laragon` (default)
4. Buka Laragon dan klik **Start All** — pastikan **Apache** dan **MySQL** keduanya nyala (lampu hijau)

> ✅ Laragon Full sudah bundle: PHP 8.x, Apache, MySQL/MariaDB, Nginx, HeidiSQL, Composer

## 📦 2. Extract Source Code

1. Download file `piulcell.zip` (atau `piulcell.tar.gz`)
2. Klik kanan → **Extract Here** ke folder `C:\laragon\www\`
3. Hasil akhir folder: `C:\laragon\www\piulcell\`

```
C:\laragon\www\piulcell\
├── public\
├── app\
├── assets\
├── uploads\
├── database\
└── README.md
```

## 🗃️ 3. Import Database

### Opsi A — via HeidiSQL (GUI, mudah)
1. Di Laragon, klik tombol **Database** → buka HeidiSQL
2. Session sudah otomatis ke `127.0.0.1` user `root` password kosong → **Open**
3. Klik kanan area sidebar kiri → **Create new** → **Database**
   - Nama: `piulcell_db`
   - Collation: `utf8mb4_unicode_ci`
   - Klik **OK**
4. Pilih database `piulcell_db` (klik 2× di sidebar)
5. Menu **File → Load SQL file** → buka `C:\laragon\www\piulcell\database\schema.sql` → tekan **F9** (Execute)
6. Ulangi untuk `database\seed.sql` → tekan **F9**

> ✅ Berhasil kalau di sidebar muncul ~17 tabel di `piulcell_db`

### Opsi B — via Command Line (untuk yang lebih advanced)
```bash
# Buka Terminal Laragon (Menu → Terminal)
cd C:\laragon\www\piulcell\database
mysql -u root piulcell_db < schema.sql
mysql -u root piulcell_db < seed.sql
```

## ⚙️ 4. Konfigurasi Database

**WAJIB** edit `app/Core/Database.php` agar cocok dengan Windows.

Buka file `C:\laragon\www\piulcell\app\Core\Database.php`. Cari baris di sekitar baris 9–14:

**SEBELUM** (Linux):
```php
self::$pdo = new PDO(
    'mysql:unix_socket=/run/mysqld/mysqld.sock;dbname=piulcell_db;charset=utf8mb4',
    'root',
    '',
```

**GANTI MENJADI** (Windows/Laragon):
```php
self::$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=piulcell_db;charset=utf8mb4',
    'root',
    '', // <- biarkan kosong kalau Laragon default. Isi password kalau Anda set password MySQL
```

Save file (Ctrl+S).

## ▶️ 5. Jalankan App (2 cara)

### 🅰️ Cara MUDAH — pakai PHP Built-in Server (REKOMENDASI)

1. Buka Terminal Laragon (Menu → **Terminal**) atau Command Prompt
2. Jalankan:
   ```bash
   cd C:\laragon\www\piulcell
   php -S localhost:8080 -t public public/router.php
   ```
3. Akan muncul output: `PHP Development Server (http://localhost:8080) started`
4. Buka browser → http://localhost:8080
5. **Selesai!** 🎉

> ⚠️ Jangan tutup terminal — kalau ditutup, server mati. Biarkan jalan selama Anda pakai aplikasi.

### 🅱️ Cara Apache — Virtual Host

1. Di Laragon, klik kanan icon Laragon di system tray → **Apache** → **httpd.conf** (untuk verifikasi)
2. Atau lebih mudah, akses langsung lewat: http://localhost/piulcell/public/
3. Jika 404 saat klik link internal:
   - Klik kanan icon Laragon → **Apache** → **URL Rewrite** → centang **ON**
   - Klik **Reload** atau Restart Laragon

## 🔐 6. Login & Pakai App

Buka http://localhost:8080 dan klik **Masuk**. Coba akun demo:

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| 🛒 **Buyer** | `buyer@piulcell.com` | `Buyer2024!` | Belanja normal |
| 🛠️ **Admin** | `admin@piulcell.com` | `PiulAdmin2024!` | Panel admin lengkap |
| 👑 **Owner** | `owner@piulcell.com` | `PiulOwner2024!` | Owner + analitik bisnis |

### 🎟️ Voucher Demo (coba di checkout)
- **PIULCELL10** — Diskon 10% maks Rp 200rb, min belanja Rp 1jt
- **GRATIS50** — Potongan Rp 50rb, min Rp 500rb
- **FREEONGKIR** — Gratis ongkos kirim

## 🧪 7. Test Flow Lengkap

### Test 1: Buyer Belanja
1. Login sebagai **buyer**
2. Klik salah satu produk (mis: iPhone 15 Pro Max)
3. Pilih varian (Storage, Warna)
4. Klik **Beli Sekarang**
5. Di checkout: pilih alamat default → ongkir auto-hitung (Haversine)
6. Masukkan voucher `PIULCELL10` → klik **Pakai**
7. Pilih **Transfer Bank** → **Buat Pesanan**
8. Di halaman pembayaran → Upload bukti (file apapun)

### Test 2: Admin Verifikasi
1. Logout, login sebagai **admin**
2. Buka **Verifikasi Bayar** di sidebar
3. Lihat order yg menunggu verifikasi → klik tombol ✓ **Approve**
4. Order otomatis pindah ke status "Diproses"

### Test 3: Cetak Invoice
1. Sebagai buyer atau admin, buka detail order
2. Klik **Cetak Invoice / Save PDF**
3. Browser akan buka invoice → klik **Print** → bisa **Save as PDF**

## 🐛 Troubleshooting

| Masalah | Solusi |
|---------|--------|
| **"Class 'PDO' not found"** | PHP belum punya ekstensi PDO. Di Laragon: Menu → PHP → Extensions → centang `pdo_mysql` |
| **"Could not connect: Access denied"** | Password MySQL salah. Edit `app/Core/Database.php` isi password Anda |
| **404 di semua halaman selain /** | Pakai cara A (PHP built-in server) atau aktifkan URL Rewrite di Apache |
| **Gambar produk tidak muncul** | Pastikan folder `assets/img/products/` ter-extract. Tidak masalah kalau placeholder yg keluar |
| **Upload bukti bayar gagal** | Folder `uploads/payment_proofs/` writable. Di Windows biasanya OK, di Linux: `chmod -R 777 uploads/` |
| **Session error** | Hapus isi folder `storage/sessions/` |
| **Port 8080 sudah dipakai** | Ganti port: `php -S localhost:9090 -t public public/router.php` |

## 📝 Tip Lanjutan

### Reset database ulang
```bash
cd C:\laragon\www\piulcell\database
mysql -u root piulcell_db < schema.sql && mysql -u root piulcell_db < seed.sql
```

### Buat akun admin baru manual
Lewat HeidiSQL → tabel `users` → tambah row dengan:
- `role` = `admin`
- `password` = hash bcrypt (generate via http://www.bcrypt-generator.com/)
- `is_active` = 1

### Backup database
HeidiSQL → klik kanan database `piulcell_db` → **Export database as SQL** → save .sql file

---

## 💡 Tips Production

Kalau mau deploy ke hosting biasa (cPanel / shared hosting):
1. Upload SEMUA file ke `public_html/piulcell/`
2. Di cPanel buat database MySQL → import `schema.sql` + `seed.sql`
3. Edit `app/Core/Database.php` dengan kredensial database hosting
4. Set document root ke `public_html/piulcell/public/` (atau pakai .htaccess redirect)
5. Pastikan PHP versi **7.4** atau lebih baru

---

**Selamat mencoba! 🚀**  
Pertanyaan? Lihat `README.md` untuk dokumentasi lebih lengkap.
