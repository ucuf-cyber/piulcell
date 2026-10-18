USE piulcell_db;

-- ============ USERS ============
-- Owner: owner@piulcell.com / PiulOwner2024!
-- Admin: admin@piulcell.com / PiulAdmin2024!
-- Buyer: buyer@piulcell.com / Buyer2024!
INSERT INTO users (name, email, phone, password, role, is_active, email_verified_at) VALUES
('Pemilik Piul Cell', 'owner@piulcell.com', '081234567890', '$2y$12$mhSbtJjKp3c1gThhr3Up7eD.IpvomoDJWJJOZuGuPeAAyQYrcNLNq', 'owner', 1, NOW()),
('Admin Toko', 'admin@piulcell.com', '081298765432', '$2y$12$Oyev4P93GFwIHcJc3JcN9.62LDLCu0u2rv6j/IbB7UvswlyFyHzRG', 'admin', 1, NOW()),
('Andi Pembeli', 'buyer@piulcell.com', '081377889900', '$2y$12$HX7DgxYCdsoS/gX9cI2MgOScFGTb3w.so9E1a1uez5KgXl32GsajK', 'buyer', 1, NOW());

INSERT INTO user_addresses (user_id, label, recipient, phone, province, city, district, postal_code, address_detail, latitude, longitude, is_primary) VALUES
(3, 'Rumah', 'Andi Pembeli', '081377889900', 'DKI Jakarta', 'Jakarta Selatan', 'Cilandak', '12430', 'Jl. RS Fatmawati No. 12, RT 02/RW 05', -6.297538, 106.795959, 1);

-- ============ BRANDS ============
INSERT INTO brands (name, slug, logo, description, is_featured, sort_order) VALUES
('Apple', 'apple', 'apple.svg', 'Premium smartphones dari Cupertino', 1, 1),
('Samsung', 'samsung', 'samsung.svg', 'Inovasi Android kelas atas', 1, 2),
('Xiaomi', 'xiaomi', 'xiaomi.svg', 'Spek tinggi, harga bersahabat', 1, 3),
('OPPO', 'oppo', 'oppo.svg', 'Kamera selfie terbaik', 1, 4),
('Vivo', 'vivo', 'vivo.svg', 'Stylish & powerful', 1, 5),
('Realme', 'realme', 'realme.svg', 'Dare to leap', 1, 6),
('Infinix', 'infinix', 'infinix.svg', 'Smartphone gaming terjangkau', 0, 7),
('Google', 'google', 'google.svg', 'Pixel — pure Android experience', 0, 8);

-- ============ CATEGORIES ============
INSERT INTO categories (parent_id, name, slug, icon, description, is_active, sort_order) VALUES
(NULL, 'Smartphone', 'smartphone', 'smartphone', 'Semua jenis HP terbaru', 1, 1),
(NULL, 'Tablet', 'tablet', 'tablet', 'iPad & tablet Android', 1, 2),
(NULL, 'Aksesoris', 'aksesoris', 'plug', 'Charger, kabel, headset, dll', 1, 3),
(NULL, 'Audio', 'audio', 'headphones', 'TWS, headset, speaker', 1, 4),
(NULL, 'Wearable', 'wearable', 'watch', 'Smartwatch & smartband', 1, 5);

-- ============ PRODUCTS ============
INSERT INTO products (brand_id, category_id, name, slug, sku_base, description, specifications, thumbnail, is_featured, is_new, is_active) VALUES
(1, 1, 'iPhone 15 Pro Max', 'iphone-15-pro-max', 'IP15PM',
 'iPhone 15 Pro Max hadir dengan desain titanium premium, chip A17 Pro, kamera 48MP, dan layar Super Retina XDR 6.7 inci. Pengalaman tertinggi Apple.',
 JSON_OBJECT('Layar','6.7" OLED ProMotion 120Hz','Chipset','Apple A17 Pro','Kamera','48MP + 12MP UW + 12MP Tele 5x','Baterai','4422 mAh','Sistem Operasi','iOS 17','Konektivitas','5G, Wi-Fi 6E, USB-C','Material','Titanium','Berat','221 g'),
 'iphone15promax.jpg', 1, 1, 1),
(2, 1, 'Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'SGS24U',
 'Galaxy S24 Ultra dengan AI fotografi, S-Pen, layar Dynamic AMOLED 2X 6.8 inci, dan chipset Snapdragon 8 Gen 3 paling powerful.',
 JSON_OBJECT('Layar','6.8" Dynamic AMOLED 2X 120Hz','Chipset','Snapdragon 8 Gen 3','Kamera','200MP + 50MP Tele + 12MP UW + 10MP Tele','Baterai','5000 mAh','Sistem Operasi','One UI 6.1 (Android 14)','Fitur','S-Pen, Galaxy AI','Material','Titanium','Berat','233 g'),
 'galaxys24ultra.jpg', 1, 1, 1),
(3, 1, 'Xiaomi 14 Pro', 'xiaomi-14-pro', 'X14P',
 'Xiaomi 14 Pro dengan Leica Optics, chipset Snapdragon 8 Gen 3, layar All-Around Liquid AMOLED, dan pengisian 120W HyperCharge.',
 JSON_OBJECT('Layar','6.73" AMOLED 120Hz','Chipset','Snapdragon 8 Gen 3','Kamera','Leica Triple 50MP','Baterai','4880 mAh','Pengisian','120W HyperCharge','Sistem Operasi','HyperOS','Berat','223 g'),
 'xiaomi14pro.jpg', 1, 1, 1),
(4, 1, 'OPPO Reno 11 Pro', 'oppo-reno-11-pro', 'ORN11P',
 'OPPO Reno 11 Pro hadir dengan desain premium, kamera portrait 50MP, dan pengisian SUPERVOOC 80W.',
 JSON_OBJECT('Layar','6.7" AMOLED 120Hz','Chipset','Snapdragon 8s Gen 3','Kamera','50MP + 8MP UW + 32MP Tele','Baterai','4700 mAh','Pengisian','80W SUPERVOOC','Sistem Operasi','ColorOS 14','Berat','190 g'),
 'opporeno11pro.jpg', 1, 0, 1),
(5, 1, 'Vivo X100 Pro', 'vivo-x100-pro', 'VX100P',
 'Vivo X100 Pro dengan kamera ZEISS, telefoto 100mm, dan chipset Dimensity 9300.',
 JSON_OBJECT('Layar','6.78" AMOLED 120Hz','Chipset','MediaTek Dimensity 9300','Kamera','50MP ZEISS + 50MP UW + 50MP Tele','Baterai','5400 mAh','Pengisian','100W FlashCharge','Sistem Operasi','Funtouch OS 14','Berat','221 g'),
 'vivox100pro.jpg', 1, 1, 1),
(6, 1, 'Realme GT 5 Pro', 'realme-gt-5-pro', 'RGT5P',
 'Realme GT 5 Pro flagship dengan Snapdragon 8 Gen 3, kamera periscope, dan layar 144Hz untuk gaming maksimal.',
 JSON_OBJECT('Layar','6.78" AMOLED 144Hz','Chipset','Snapdragon 8 Gen 3','Kamera','50MP + 50MP UW + 50MP Tele 3x','Baterai','5400 mAh','Pengisian','100W SuperVOOC','Sistem Operasi','realme UI 5.0','Berat','218 g'),
 'realmegt5pro.jpg', 1, 1, 1),
(2, 1, 'Samsung Galaxy A55 5G', 'samsung-galaxy-a55-5g', 'SGA55',
 'Galaxy A55 5G dengan desain Awesome, kamera OIS, dan baterai tahan lama.',
 JSON_OBJECT('Layar','6.6" Super AMOLED 120Hz','Chipset','Exynos 1480','Kamera','50MP + 12MP UW + 5MP Macro','Baterai','5000 mAh','Sistem Operasi','One UI 6.1','Berat','213 g'),
 'galaxya55.jpg', 0, 1, 1),
(3, 1, 'Redmi Note 13 Pro', 'redmi-note-13-pro', 'RN13P',
 'Redmi Note 13 Pro dengan kamera 200MP, layar 1.5K AMOLED, dan pengisian 67W.',
 JSON_OBJECT('Layar','6.67" AMOLED 1.5K 120Hz','Chipset','Snapdragon 7s Gen 2','Kamera','200MP + 8MP UW + 2MP Macro','Baterai','5100 mAh','Pengisian','67W Turbo','Sistem Operasi','MIUI 14','Berat','187 g'),
 'redminote13pro.jpg', 0, 1, 1),
(1, 4, 'AirPods Pro 2', 'airpods-pro-2', 'APP2',
 'AirPods Pro 2 dengan Active Noise Cancellation, Spatial Audio, dan case USB-C.',
 JSON_OBJECT('Driver','Custom high-excursion Apple','ANC','Active Noise Cancellation','Konektivitas','Bluetooth 5.3','Baterai','6 jam (30 jam dengan case)','Case','USB-C charging','Tahan','IPX4'),
 'airpodspro2.jpg', 1, 0, 1),
(2, 5, 'Samsung Galaxy Watch 6', 'galaxy-watch-6', 'SGW6',
 'Galaxy Watch 6 dengan layar AMOLED bezel tipis, sensor BioActive, dan Wear OS 4.',
 JSON_OBJECT('Layar','1.5" Super AMOLED','Chipset','Exynos W930','Baterai','425 mAh','OS','Wear OS 4 + One UI Watch 5','Sensor','BioActive (PPG + ECG + BIA)','Tahan','5ATM + IP68'),
 'galaxywatch6.jpg', 0, 0, 1);

-- ============ VARIANT ATTRIBUTES ============
INSERT INTO product_variant_attributes (product_id, attribute, `values`) VALUES
(1, 'Storage', '["256GB","512GB","1TB"]'),
(1, 'Warna', '["Natural Titanium","Blue Titanium","White Titanium","Black Titanium"]'),
(2, 'RAM', '["12GB"]'),
(2, 'Storage', '["256GB","512GB","1TB"]'),
(2, 'Warna', '["Titanium Black","Titanium Gray","Titanium Violet"]'),
(3, 'RAM', '["12GB","16GB"]'),
(3, 'Storage', '["256GB","512GB"]'),
(3, 'Warna', '["Black","White"]'),
(4, 'RAM', '["12GB"]'),
(4, 'Storage', '["256GB","512GB"]'),
(4, 'Warna', '["Pearl White","Rock Grey"]'),
(5, 'RAM', '["12GB","16GB"]'),
(5, 'Storage', '["256GB","512GB"]'),
(5, 'Warna', '["Asteroid Black","Sunset Orange"]'),
(6, 'RAM', '["12GB","16GB"]'),
(6, 'Storage', '["256GB","512GB","1TB"]'),
(6, 'Warna', '["Red Rock","Blue Sea"]'),
(7, 'RAM', '["8GB","12GB"]'),
(7, 'Storage', '["128GB","256GB"]'),
(7, 'Warna', '["Awesome Iceblue","Awesome Lilac","Awesome Navy"]'),
(8, 'RAM', '["8GB","12GB"]'),
(8, 'Storage', '["256GB","512GB"]'),
(8, 'Warna', '["Midnight Black","Aurora Purple","Forest Green"]'),
(9, 'Warna', '["White"]'),
(10, 'Warna', '["Graphite","Silver","Gold"]');

-- ============ PRODUCT VARIANTS (subset of meaningful combinations) ============
INSERT INTO product_variants (product_id, sku, combination, price, price_promo, stock, weight, images, is_active) VALUES
-- iPhone 15 Pro Max
(1,'IP15PM-256-NT', JSON_OBJECT('Storage','256GB','Warna','Natural Titanium'), 22999000, 21499000, 12, 221, JSON_ARRAY('iphone15promax-natural.jpg'),1),
(1,'IP15PM-512-NT', JSON_OBJECT('Storage','512GB','Warna','Natural Titanium'), 26499000, 24999000, 8, 221, JSON_ARRAY('iphone15promax-natural.jpg'),1),
(1,'IP15PM-1TB-BT', JSON_OBJECT('Storage','1TB','Warna','Blue Titanium'), 30499000, NULL, 5, 221, JSON_ARRAY('iphone15promax-blue.jpg'),1),
(1,'IP15PM-256-BT', JSON_OBJECT('Storage','256GB','Warna','Black Titanium'), 22999000, 21499000, 10, 221, JSON_ARRAY('iphone15promax-black.jpg'),1),
-- Samsung S24 Ultra
(2,'SGS24U-12-256-K', JSON_OBJECT('RAM','12GB','Storage','256GB','Warna','Titanium Black'), 21999000, 19999000, 15, 233, JSON_ARRAY('galaxys24ultra-black.jpg'),1),
(2,'SGS24U-12-512-K', JSON_OBJECT('RAM','12GB','Storage','512GB','Warna','Titanium Black'), 23999000, 22499000, 7, 233, JSON_ARRAY('galaxys24ultra-black.jpg'),1),
(2,'SGS24U-12-256-V', JSON_OBJECT('RAM','12GB','Storage','256GB','Warna','Titanium Violet'), 21999000, 19999000, 6, 233, JSON_ARRAY('galaxys24ultra-violet.jpg'),1),
-- Xiaomi 14 Pro
(3,'X14P-12-256-K', JSON_OBJECT('RAM','12GB','Storage','256GB','Warna','Black'), 14999000, 13499000, 20, 223, JSON_ARRAY('xiaomi14pro-black.jpg'),1),
(3,'X14P-16-512-P', JSON_OBJECT('RAM','16GB','Storage','512GB','Warna','White'), 17999000, 16499000, 8, 223, JSON_ARRAY('xiaomi14pro-white.jpg'),1),
-- OPPO Reno 11 Pro
(4,'ORN11P-12-256-P', JSON_OBJECT('RAM','12GB','Storage','256GB','Warna','Pearl White'), 8999000, 8499000, 18, 190, JSON_ARRAY('opporeno11pro-white.jpg'),1),
(4,'ORN11P-12-512-R', JSON_OBJECT('RAM','12GB','Storage','512GB','Warna','Rock Grey'), 9999000, 9499000, 9, 190, JSON_ARRAY('opporeno11pro-grey.jpg'),1),
-- Vivo X100 Pro
(5,'VX100P-12-256-K', JSON_OBJECT('RAM','12GB','Storage','256GB','Warna','Asteroid Black'), 14999000, 13999000, 12, 221, JSON_ARRAY('vivox100pro-black.jpg'),1),
(5,'VX100P-16-512-O', JSON_OBJECT('RAM','16GB','Storage','512GB','Warna','Sunset Orange'), 16999000, 15999000, 5, 221, JSON_ARRAY('vivox100pro-orange.jpg'),1),
-- Realme GT 5 Pro
(6,'RGT5P-12-256-R', JSON_OBJECT('RAM','12GB','Storage','256GB','Warna','Red Rock'), 11999000, 10999000, 22, 218, JSON_ARRAY('realmegt5pro-red.jpg'),1),
(6,'RGT5P-16-512-B', JSON_OBJECT('RAM','16GB','Storage','512GB','Warna','Blue Sea'), 13999000, 12999000, 11, 218, JSON_ARRAY('realmegt5pro-blue.jpg'),1),
-- Galaxy A55
(7,'SGA55-8-128-I', JSON_OBJECT('RAM','8GB','Storage','128GB','Warna','Awesome Iceblue'), 5499000, 4999000, 30, 213, JSON_ARRAY('galaxya55-ice.jpg'),1),
(7,'SGA55-8-256-N', JSON_OBJECT('RAM','8GB','Storage','256GB','Warna','Awesome Navy'), 5999000, 5499000, 25, 213, JSON_ARRAY('galaxya55-navy.jpg'),1),
(7,'SGA55-12-256-L', JSON_OBJECT('RAM','12GB','Storage','256GB','Warna','Awesome Lilac'), 6499000, 5999000, 20, 213, JSON_ARRAY('galaxya55-lilac.jpg'),1),
-- Redmi Note 13 Pro
(8,'RN13P-8-256-K', JSON_OBJECT('RAM','8GB','Storage','256GB','Warna','Midnight Black'), 4299000, 3899000, 40, 187, JSON_ARRAY('redminote13pro-black.jpg'),1),
(8,'RN13P-12-512-A', JSON_OBJECT('RAM','12GB','Storage','512GB','Warna','Aurora Purple'), 5099000, 4699000, 18, 187, JSON_ARRAY('redminote13pro-purple.jpg'),1),
(8,'RN13P-12-256-F', JSON_OBJECT('RAM','12GB','Storage','256GB','Warna','Forest Green'), 4699000, 4299000, 22, 187, JSON_ARRAY('redminote13pro-green.jpg'),1),
-- AirPods Pro 2
(9,'APP2-W', JSON_OBJECT('Warna','White'), 4399000, 3999000, 50, 50, JSON_ARRAY('airpodspro2.jpg'),1),
-- Galaxy Watch 6
(10,'SGW6-G', JSON_OBJECT('Warna','Graphite'), 4499000, 3999000, 25, 60, JSON_ARRAY('galaxywatch6-graphite.jpg'),1),
(10,'SGW6-S', JSON_OBJECT('Warna','Silver'), 4499000, 3999000, 20, 60, JSON_ARRAY('galaxywatch6-silver.jpg'),1),
(10,'SGW6-GD', JSON_OBJECT('Warna','Gold'), 4699000, 4199000, 8, 60, JSON_ARRAY('galaxywatch6-gold.jpg'),1);

-- ============ PAYMENT ACCOUNTS ============
INSERT INTO payment_accounts (type, bank_name, account_number, account_name, qris_image, is_active, sort_order) VALUES
('transfer_bank','BCA','1234567890','Piul Cell Store', NULL, 1, 1),
('transfer_bank','BRI','0987654321','Piul Cell Store', NULL, 1, 2),
('transfer_bank','Mandiri','1122334455','Piul Cell Store', NULL, 1, 3),
('transfer_bank','BNI','9988776655','Piul Cell Store', NULL, 1, 4),
('qris', NULL, NULL, 'Piul Cell Store', 'qris-default.png', 1, 5);

-- ============ BANNERS ============
INSERT INTO banners (title, subtitle, image_desktop, image_mobile, link_url, link_text, position, sort_order, is_active) VALUES
('iPhone 15 Pro Max','Titanium. So strong. So light. So Pro.', 'banner-iphone.jpg', 'banner-iphone-m.jpg', '/produk/iphone-15-pro-max', 'Beli Sekarang', 'hero', 1, 1),
('Samsung Galaxy S24 Ultra','AI is now. Galaxy AI is here.', 'banner-s24.jpg', 'banner-s24-m.jpg', '/produk/samsung-galaxy-s24-ultra', 'Lihat Detail', 'hero', 2, 1),
('Flash Sale Weekend','Diskon hingga 30% untuk semua aksesoris', 'banner-promo.jpg', 'banner-promo-m.jpg', '/katalog?kategori=aksesoris', 'Belanja Sekarang', 'promo', 1, 1);

-- ============ VOUCHERS ============
INSERT INTO vouchers (code, name, type, value, min_purchase, max_discount, quota, start_date, end_date, is_active) VALUES
('PIULCELL10','Diskon 10% Maks Rp 200.000','percentage', 10, 1000000, 200000, 100, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1),
('GRATIS50','Potongan Rp 50.000','fixed', 50000, 500000, NULL, 200, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1),
('FREEONGKIR','Gratis Ongkos Kirim','free_shipping', 100, 0, NULL, 500, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 1);

-- ============ SHIPPING ZONES ============
INSERT INTO shipping_zones (name, base_price, price_per_km, max_distance, courier, etd_days, is_active) VALUES
('Lokal (0–5 km)', 5000, 500, 5, 'Kurir Toko', '1', 1),
('Sekitar Kota (5–15 km)', 10000, 800, 15, 'Kurir Toko', '1', 1),
('Dalam Kota (15–30 km)', 15000, 0, 30, 'J&T Express', '1-2', 1),
('Jabodetabek (30–60 km)', 20000, 0, 60, 'J&T / JNE REG', '1-2', 1),
('Luar Kota (>60 km)', 25000, 0, 0, 'JNE REG', '2-3', 1);

-- ============ STORE SETTINGS ============
INSERT INTO store_settings (key_name, value, group_name) VALUES
('store_name', 'Piul Cell', 'general'),
('store_tagline', 'Toko HP & Aksesoris Terpercaya', 'general'),
('store_email', 'piulcell@email.com', 'general'),
('store_phone', '081234567890', 'general'),
('store_whatsapp', '6281234567890', 'general'),
('store_address', 'Jl. Ir. H. Juanda No. 88, Ciputat, Tangerang Selatan', 'general'),
('store_latitude', '-6.289888', 'shipping'),
('store_longitude', '106.763866', 'shipping'),
('free_shipping_min', '7500000', 'shipping'),
('tax_rate', '0', 'finance'),
('payment_expire_hours', '24', 'payment'),
('cod_max_distance', '15', 'payment'),
('cod_enabled', '1', 'payment'),
('transfer_enabled', '1', 'payment'),
('qris_enabled', '1', 'payment'),
('social_instagram', 'https://instagram.com/piulcell', 'social'),
('social_facebook', 'https://facebook.com/piulcell', 'social'),
('social_tiktok', 'https://tiktok.com/@piulcell', 'social');

-- ============ STATIC PAGES ============
INSERT INTO static_pages (slug, title, content) VALUES
('tentang-kami','Tentang Kami','<h2>Tentang Piul Cell</h2><p>Piul Cell adalah toko HP dan aksesoris online terpercaya yang berkomitmen menyediakan smartphone dan aksesoris berkualitas dengan harga terbaik. Berdiri sejak 2020, kami telah melayani ribuan pelanggan di seluruh Indonesia.</p><p>Visi kami adalah menjadi destinasi belanja gadget online nomor satu di Indonesia dengan layanan premium dan pengalaman berbelanja kelas dunia.</p>'),
('cara-belanja','Cara Berbelanja','<h2>Cara Berbelanja di Piul Cell</h2><ol><li>Pilih produk yang Anda inginkan</li><li>Pilih varian (RAM, Storage, Warna)</li><li>Tambahkan ke keranjang atau klik Beli Sekarang</li><li>Isi alamat pengiriman</li><li>Pilih metode pembayaran (Transfer Bank / QRIS / COD)</li><li>Upload bukti bayar (untuk Transfer/QRIS)</li><li>Tunggu konfirmasi admin, pesanan akan diproses dan dikirim</li></ol>'),
('kebijakan-pengiriman','Kebijakan Pengiriman','<h2>Kebijakan Pengiriman</h2><p>Kami mengirim pesanan ke seluruh Indonesia melalui kurir internal & ekspedisi resmi (JNE, J&T, SiCepat). Estimasi tiba 1–3 hari kerja tergantung lokasi. Gratis ongkir untuk pembelian minimal Rp 1.500.000.</p>'),
('kebijakan-pengembalian','Kebijakan Pengembalian','<h2>Kebijakan Pengembalian</h2><p>Produk yang cacat dari pabrik dapat ditukar dalam 7 hari setelah diterima. Hubungi customer service kami untuk proses penukaran.</p>'),
('faq','FAQ','<h2>Pertanyaan yang Sering Ditanyakan</h2><h3>Apakah produk bergaransi?</h3><p>Ya, semua produk bergaransi resmi distributor.</p><h3>Berapa lama pengiriman?</h3><p>1–3 hari kerja tergantung lokasi.</p><h3>Bisa COD?</h3><p>Bisa, untuk area dalam radius 15 km dari toko.</p>'),
('hubungi-kami','Hubungi Kami','<h2>Hubungi Kami</h2><p>Email: piulcell@email.com<br>WhatsApp: 0812-3456-7890<br>Alamat: Jl. Ir. H. Juanda No. 88, Ciputat, Tangerang Selatan</p>');
