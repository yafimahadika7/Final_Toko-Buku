-- Store Admin (v0-style) schema
CREATE DATABASE IF NOT EXISTS toko_komputer
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE toko_komputer;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','kasir','owner') NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, username, password_hash, role) VALUES
('Administrator','admin','admin123','admin'),
('Kasir','kasir','kasir123','kasir'),
('Owner','owner','owner123','owner');

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  category_id INT NULL,
  sell_price INT NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NULL,
  email VARCHAR(150) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE suppliers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NULL,
  email VARCHAR(150) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_no VARCHAR(50) NOT NULL UNIQUE,
  sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  user_id INT NULL,
  customer_id INT NULL,
  grand_total INT NOT NULL DEFAULT 0,
  status ENUM('PAID','CANCEL') DEFAULT 'PAID',
  CONSTRAINT fk_sales_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_sales_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

CREATE TABLE sales_detail (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sale_id INT NOT NULL,
  product_id INT NOT NULL,
  price INT NOT NULL,
  qty INT NOT NULL,
  subtotal INT NOT NULL,
  CONSTRAINT fk_detail_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
  CONSTRAINT fk_detail_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

CREATE TABLE stock_movements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  type ENUM('IN','OUT','ADJUST') NOT NULL,
  qty INT NOT NULL,
  note VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mov_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Seed minimal data
INSERT INTO categories (name) VALUES ('Computers'),('Accessories'),('Components');

INSERT INTO products (sku, name, category_id, sell_price, stock) VALUES
('PC-001','PC Rakitan Office', 1, 5500000, 10),
('LAP-001','Laptop Core i5', 1, 8500000, 5),
('ACC-001','Mouse Wireless', 2, 150000, 50);

INSERT INTO customers (name, phone, email) VALUES
('Budi', '08123456789', 'budi@example.com'),
('Siti', '08129876543', 'siti@example.com');

INSERT INTO suppliers (name, phone, email) VALUES
('PT Supplier A', '021123456', 'sales@supplier-a.com');
