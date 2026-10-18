-- ============================================
-- PIUL CELL — Database Schema
-- ============================================

DROP DATABASE IF EXISTS piulcell_db;
CREATE DATABASE piulcell_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE piulcell_db;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  role ENUM('buyer','admin','owner') DEFAULT 'buyer',
  avatar VARCHAR(255),
  is_active TINYINT(1) DEFAULT 1,
  email_verified_at DATETIME NULL,
  remember_token VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE user_addresses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  label VARCHAR(50) DEFAULT 'Rumah',
  recipient VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  province VARCHAR(100) NOT NULL,
  city VARCHAR(100) NOT NULL,
  district VARCHAR(100) NOT NULL,
  postal_code VARCHAR(10) NOT NULL,
  address_detail TEXT NOT NULL,
  latitude DECIMAL(10,8),
  longitude DECIMAL(11,8),
  is_primary TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE brands (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  logo VARCHAR(255),
  description TEXT,
  is_featured TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id INT UNSIGNED NULL,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  icon VARCHAR(255),
  image VARCHAR(255),
  description TEXT,
  is_active TINYINT(1) DEFAULT 1,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  brand_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  sku_base VARCHAR(100),
  description TEXT,
  specifications JSON,
  thumbnail VARCHAR(255),
  is_featured TINYINT(1) DEFAULT 0,
  is_new TINYINT(1) DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  meta_title VARCHAR(200),
  meta_desc VARCHAR(300),
  sold_count INT UNSIGNED DEFAULT 0,
  view_count INT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (brand_id) REFERENCES brands(id),
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE product_variant_attributes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  attribute VARCHAR(50) NOT NULL,
  `values` JSON NOT NULL,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE product_variants (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  sku VARCHAR(120) NOT NULL UNIQUE,
  combination JSON NOT NULL,
  price DECIMAL(12,0) NOT NULL,
  price_promo DECIMAL(12,0) NULL,
  stock INT UNSIGNED DEFAULT 0,
  weight INT UNSIGNED DEFAULT 300,
  images JSON,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE product_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  variant_id INT UNSIGNED NULL,
  image_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(200),
  sort_order INT DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
);

CREATE TABLE carts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  session_id VARCHAR(100) NULL,
  variant_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (variant_id) REFERENCES product_variants(id)
);

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(30) NOT NULL UNIQUE,
  user_id INT UNSIGNED NOT NULL,
  status ENUM('pending','confirmed','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  subtotal DECIMAL(14,0) NOT NULL,
  shipping_cost DECIMAL(10,0) NOT NULL DEFAULT 0,
  discount DECIMAL(10,0) NOT NULL DEFAULT 0,
  tax DECIMAL(10,0) NOT NULL DEFAULT 0,
  total DECIMAL(14,0) NOT NULL,
  voucher_code VARCHAR(50) NULL,
  voucher_id INT UNSIGNED NULL,
  notes TEXT,
  ship_name VARCHAR(100) NOT NULL,
  ship_phone VARCHAR(20) NOT NULL,
  ship_province VARCHAR(100) NOT NULL,
  ship_city VARCHAR(100) NOT NULL,
  ship_district VARCHAR(100) NOT NULL,
  ship_postal VARCHAR(10) NOT NULL,
  ship_address TEXT NOT NULL,
  ship_lat DECIMAL(10,8),
  ship_lng DECIMAL(11,8),
  courier VARCHAR(50),
  courier_service VARCHAR(50),
  tracking_number VARCHAR(100),
  shipped_at DATETIME NULL,
  delivered_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  variant_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  product_name VARCHAR(200) NOT NULL,
  variant_combination JSON NOT NULL,
  price DECIMAL(12,0) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  subtotal DECIMAL(14,0) NOT NULL,
  image VARCHAR(255),
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (variant_id) REFERENCES product_variants(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  payment_method ENUM('transfer_bank','qris','cod') NOT NULL,
  bank_name VARCHAR(50) NULL,
  account_number VARCHAR(30) NULL,
  account_name VARCHAR(100) NULL,
  amount DECIMAL(14,0) NOT NULL,
  proof_image VARCHAR(255) NULL,
  status ENUM('pending','waiting_verification','verified','rejected','cod_pending','cod_done') DEFAULT 'pending',
  verified_by INT UNSIGNED NULL,
  verified_at DATETIME NULL,
  reject_reason VARCHAR(255) NULL,
  expire_at DATETIME NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE payment_accounts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type ENUM('transfer_bank','qris') NOT NULL,
  bank_name VARCHAR(50) NULL,
  account_number VARCHAR(30) NULL,
  account_name VARCHAR(100) NOT NULL,
  qris_image VARCHAR(255) NULL,
  is_active TINYINT(1) DEFAULT 1,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vouchers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  type ENUM('percentage','fixed','free_shipping') NOT NULL,
  value DECIMAL(10,2) NOT NULL,
  min_purchase DECIMAL(12,0) DEFAULT 0,
  max_discount DECIMAL(10,0) NULL,
  quota INT UNSIGNED,
  used_count INT UNSIGNED DEFAULT 0,
  user_specific INT UNSIGNED NULL,
  start_date DATE,
  end_date DATE,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_specific) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  variant_id INT UNSIGNED NULL,
  user_id INT UNSIGNED NOT NULL,
  order_item_id INT UNSIGNED NULL,
  rating TINYINT(1) NOT NULL CHECK (rating BETWEEN 1 AND 5),
  title VARCHAR(200),
  body TEXT NOT NULL,
  images JSON,
  is_approved TINYINT(1) DEFAULT 0,
  helpful_count INT UNSIGNED DEFAULT 0,
  reply TEXT NULL,
  replied_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE wishlists (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_wishlist (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE banners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200),
  subtitle VARCHAR(300),
  image_desktop VARCHAR(255) NOT NULL,
  image_mobile VARCHAR(255),
  link_url VARCHAR(255),
  link_text VARCHAR(50),
  position ENUM('hero','promo','brand') DEFAULT 'hero',
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  start_date DATE NULL,
  end_date DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shipping_zones (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  province VARCHAR(100),
  city_keywords JSON,
  base_price DECIMAL(10,0) NOT NULL,
  price_per_km DECIMAL(8,0) DEFAULT 0,
  max_distance INT DEFAULT 0,
  courier VARCHAR(50) DEFAULT 'internal',
  etd_days VARCHAR(20) DEFAULT '1-2',
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE store_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  key_name VARCHAR(100) NOT NULL UNIQUE,
  value TEXT,
  group_name VARCHAR(50) DEFAULT 'general',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE notifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  type VARCHAR(50) NOT NULL,
  title VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  url VARCHAR(255),
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  status VARCHAR(50) NOT NULL,
  note TEXT,
  created_by INT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE static_pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(100) NOT NULL UNIQUE,
  title VARCHAR(200) NOT NULL,
  content LONGTEXT,
  is_active TINYINT(1) DEFAULT 1,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
