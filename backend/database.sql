-- ===== BANAVI FASHION STORE - DATABASE SETUP =====

-- Create Database
CREATE DATABASE IF NOT EXISTS banavi_db;
USE banavi_db;

-- ===== USERS TABLE =====
CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(120) UNIQUE NOT NULL,
  phone VARCHAR(15),
  password_hash VARCHAR(255),
  profile_avatar VARCHAR(255),
  date_of_birth DATE,
  gender ENUM('Male', 'Female', 'Other'),
  is_verified BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_phone (phone)
);

-- ===== PRODUCTS TABLE =====
CREATE TABLE IF NOT EXISTS products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  category VARCHAR(50) NOT NULL,
  description TEXT,
  price DECIMAL(10, 2) NOT NULL,
  original_price DECIMAL(10, 2),
  emoji VARCHAR(10),
  badge VARCHAR(20),
  image_url VARCHAR(255),
  stock INT DEFAULT 100,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_price (price)
);

-- ===== PRODUCT SIZES TABLE =====
CREATE TABLE IF NOT EXISTS product_sizes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT NOT NULL,
  size VARCHAR(10) NOT NULL,
  stock INT DEFAULT 50,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  UNIQUE KEY unique_product_size (product_id, size)
);

-- ===== WISHLIST TABLE =====
CREATE TABLE IF NOT EXISTS wishlist (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  UNIQUE KEY unique_wishlist (user_id, product_id)
);

-- ===== CART TABLE =====
CREATE TABLE IF NOT EXISTS cart (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  session_id VARCHAR(255),
  product_id INT NOT NULL,
  quantity INT DEFAULT 1,
  size VARCHAR(10),
  price DECIMAL(10, 2),
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  UNIQUE KEY unique_cart (user_id, product_id, size)
);

-- ===== ORDERS TABLE =====
CREATE TABLE IF NOT EXISTS orders (
  id INT PRIMARY KEY AUTO_INCREMENT,
  order_number VARCHAR(50) UNIQUE NOT NULL,
  user_id INT,
  customer_name VARCHAR(150) NOT NULL,
  customer_email VARCHAR(120),
  customer_phone VARCHAR(15),
  shipping_address TEXT NOT NULL,
  city VARCHAR(100),
  state VARCHAR(100),
  pincode VARCHAR(10),
  total_amount DECIMAL(10, 2) NOT NULL,
  discount_amount DECIMAL(10, 2) DEFAULT 0,
  final_amount DECIMAL(10, 2) NOT NULL,
  payment_method VARCHAR(50),
  payment_status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
  order_status ENUM('Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Processing',
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_order_number (order_number),
  INDEX idx_user_id (user_id),
  INDEX idx_order_status (order_status)
);

-- ===== ORDER ITEMS TABLE =====
CREATE TABLE IF NOT EXISTS order_items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  order_id INT NOT NULL,
  product_id INT,
  product_name VARCHAR(255),
  quantity INT NOT NULL,
  size VARCHAR(10),
  price DECIMAL(10, 2) NOT NULL,
  total DECIMAL(10, 2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- ===== PROMO CODES TABLE =====
CREATE TABLE IF NOT EXISTS promo_codes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(50) UNIQUE NOT NULL,
  discount_type ENUM('percentage', 'fixed') DEFAULT 'percentage',
  discount_value DECIMAL(10, 2) NOT NULL,
  max_uses INT DEFAULT -1,
  used_count INT DEFAULT 0,
  min_order_amount DECIMAL(10, 2) DEFAULT 0,
  valid_from DATETIME,
  valid_until DATETIME,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== REVIEWS TABLE =====
CREATE TABLE IF NOT EXISTS reviews (
  id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT NOT NULL,
  user_id INT,
  rating INT CHECK (rating >= 1 AND rating <= 5),
  review_text TEXT,
  is_verified_purchase BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ===== ADDRESSES TABLE =====
CREATE TABLE IF NOT EXISTS addresses (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  phone VARCHAR(15),
  address_line1 VARCHAR(255) NOT NULL,
  address_line2 VARCHAR(255),
  city VARCHAR(100) NOT NULL,
  state VARCHAR(100) NOT NULL,
  pincode VARCHAR(10) NOT NULL,
  is_default BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ===== INSERT SAMPLE PRODUCTS =====
INSERT INTO products (name, category, price, original_price, emoji, badge, stock) VALUES
('Floral Block Print Kurti', 'Kurti', 899, 1299, '👗', 'Sale', 50),
('Banarasi Silk Saree', 'Saree', 3499, NULL, '🥻', 'New', 30),
('Embroidered Lehenga Set', 'Lehenga', 5999, 7999, '👘', 'Sale', 20),
('Cotton Printed Crop Top', 'Tops', 499, NULL, '👚', 'New', 40),
('Chanderi Palazzo Kurti', 'Kurti', 1199, NULL, '👗', NULL, 35),
('Georgette Party Saree', 'Saree', 2899, 3999, '🥻', 'Sale', 25),
('Mirror Work Kurti', 'Kurti', 1599, NULL, '👗', 'New', 28),
('Co-ord Set Printed', 'Tops', 1099, 1499, '👚', 'Sale', 45),
('Rajwadi Lehenga Choli', 'Lehenga', 8499, NULL, '👘', 'New', 15),
('Ikat Fusion Kurti', 'Kurti', 749, 999, '👗', 'Sale', 60),
('Pure Mysore Silk Saree', 'Saree', 6999, NULL, '🥻', NULL, 18),
('Embroidered Peplum Top', 'Tops', 699, NULL, '👚', 'New', 50);

-- ===== INSERT SAMPLE PROMO CODES =====
INSERT INTO promo_codes (code, discount_type, discount_value, valid_until, is_active) VALUES
('BANAVI15', 'percentage', 15, '2026-12-31 23:59:59', TRUE),
('SAVE500', 'fixed', 500, '2026-12-31 23:59:59', TRUE),
('FLAT20', 'percentage', 20, '2026-12-31 23:59:59', TRUE);

-- ===== CREATE INDEXES =====
CREATE INDEX idx_created_at ON orders(created_at);
CREATE INDEX idx_product_category ON products(category);

-- ===== VIEWS =====
CREATE OR REPLACE VIEW order_summary AS
SELECT 
  o.id,
  o.order_number,
  o.customer_name,
  o.total_amount,
  o.final_amount,
  o.order_status,
  o.created_at,
  COUNT(oi.id) as item_count,
  SUM(oi.quantity) as total_quantity
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;
