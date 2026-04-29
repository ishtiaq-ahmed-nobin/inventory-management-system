-- OptiStock Inventory Management System
-- Database Schema + Seed Data

CREATE DATABASE IF NOT EXISTS `optistock` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `optistock`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `activity_log`;
DROP TABLE IF EXISTS `sale_items`;
DROP TABLE IF EXISTS `sales`;
DROP TABLE IF EXISTS `stock_in`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `suppliers`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `locations`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. users
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','manager','staff') NOT NULL DEFAULT 'staff',
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `dark_mode` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. categories
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. locations
CREATE TABLE `locations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `address` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. suppliers
CREATE TABLE `suppliers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `due_balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5. customers
CREATE TABLE `customers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `due_balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 6. products
CREATE TABLE `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(200) NOT NULL,
  `sku` VARCHAR(50) NOT NULL UNIQUE,
  `barcode` VARCHAR(100) DEFAULT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `supplier_id` INT UNSIGNED DEFAULT NULL,
  `location_id` INT UNSIGNED DEFAULT NULL,
  `purchase_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `selling_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `quantity` INT NOT NULL DEFAULT 0,
  `low_stock_alert` INT NOT NULL DEFAULT 5,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_sku` (`sku`),
  INDEX `idx_barcode` (`barcode`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. stock_in (purchases)
CREATE TABLE `stock_in` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `supplier_id` INT UNSIGNED DEFAULT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `unit_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `note` TEXT DEFAULT NULL,
  `received_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_received_date` (`received_date`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. sales
CREATE TABLE `sales` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_no` VARCHAR(30) NOT NULL UNIQUE,
  `customer_id` INT UNSIGNED DEFAULT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `due_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_type` ENUM('cash','card','mobile_banking') NOT NULL DEFAULT 'cash',
  `note` TEXT DEFAULT NULL,
  `sale_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_sale_date` (`sale_date`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. sale_items
CREATE TABLE `sale_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sale_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. activity_log
CREATE TABLE `activity_log` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(255) NOT NULL,
  `module` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================
-- SEED DATA
-- =====================

-- Admin user: admin@optistock.com / admin123
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`, `status`) VALUES
('Admin User', 'admin@optistock.com', '01700000000', '$2y$10$b7Q.ozYbBtrEEpG7n2TKpOvOyhKvkEKmG5UgQs6M1Fj6MxCe8l5/6', 'admin', 'active'),
('Manager One', 'manager@optistock.com', '01711111111', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 'active'),
('Staff User', 'staff@optistock.com', '01722222222', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 'active');

-- Categories
INSERT INTO `categories` (`name`, `description`) VALUES
('Electronics', 'Electronic devices and accessories'),
('Clothing', 'Apparel and garments'),
('Food', 'Food and beverage items'),
('Stationery', 'Office and school stationery'),
('Other', 'Miscellaneous items');

-- Location
INSERT INTO `locations` (`name`, `address`) VALUES
('Main Warehouse', 'Dhaka, Bangladesh'),
('Branch Store', 'Chittagong, Bangladesh');

-- Suppliers
INSERT INTO `suppliers` (`name`, `phone`, `email`, `address`) VALUES
('Tech Supplies Ltd', '01800000001', 'tech@supplier.com', 'Dhaka, Bangladesh'),
('Fashion House', '01800000002', 'fashion@supplier.com', 'Narayanganj, Bangladesh'),
('Food Distributors', '01800000003', 'food@supplier.com', 'Gazipur, Bangladesh');

-- Customers
INSERT INTO `customers` (`name`, `phone`, `email`, `address`) VALUES
('Walk-in Customer', '00000000000', '', 'N/A'),
('Rahim Ahmed', '01900000001', 'rahim@email.com', 'Dhaka'),
('Karim Hossain', '01900000002', 'karim@email.com', 'Chittagong');

-- Products
INSERT INTO `products` (`name`, `sku`, `barcode`, `category_id`, `supplier_id`, `location_id`, `purchase_price`, `selling_price`, `quantity`, `low_stock_alert`, `description`, `status`) VALUES
('Samsung Galaxy A54', 'PRD-20240101-001', '8801234567890', 1, 1, 1, 25000.00, 30000.00, 15, 3, '6.4-inch AMOLED display smartphone', 'active'),
('Apple iPhone 15', 'PRD-20240101-002', '8801234567891', 1, 1, 1, 80000.00, 95000.00, 5, 2, 'Latest iPhone model', 'active'),
('Men T-Shirt (Blue)', 'PRD-20240101-003', '8801234567892', 2, 2, 1, 200.00, 400.00, 50, 10, 'Cotton t-shirt, sizes S-XL', 'active'),
('Women Salwar Kameez', 'PRD-20240101-004', '8801234567893', 2, 2, 1, 800.00, 1500.00, 30, 5, 'Traditional dress set', 'active'),
('Mineral Water 500ml', 'PRD-20240101-005', '8801234567894', 3, 3, 1, 12.00, 20.00, 200, 50, 'Pure mineral water', 'active'),
('A4 Paper Ream', 'PRD-20240101-006', '8801234567895', 4, 1, 2, 350.00, 500.00, 4, 5, '500 sheets per ream', 'active'),
('Wireless Mouse', 'PRD-20240101-007', '8801234567896', 1, 1, 1, 500.00, 800.00, 20, 5, 'USB wireless mouse', 'active');

-- ============================================================
-- DEMO DATA (included for fresh installs)
-- ============================================================

-- Additional Suppliers
INSERT INTO `suppliers` (`name`, `phone`, `email`, `address`) VALUES
('Mobile World BD',       '01811000001', 'mobile@mwbd.com',   'Mirpur, Dhaka'),
('Garment Factory Direct','01811000002', 'gfd@garment.com',   'Narayanganj'),
('Wholesale Foods BD',    '01811000003', 'info@wfbd.com',     'Gazipur'),
('Office Supplies Co',    '01811000004', 'sales@offco.com',   'Motijheel, Dhaka');

-- Additional Customers
INSERT INTO `customers` (`name`, `phone`, `email`, `address`) VALUES
('Fatema Begum', '01911000001','fatema@mail.com','Uttara, Dhaka'),
('Sohel Rana',   '01911000002','sohel@mail.com', 'Sylhet'),
('Nasrin Akter', '01911000003','nasrin@mail.com','Rajshahi'),
('Jamal Uddin',  '01911000004','jamal@mail.com', 'Khulna'),
('Mitu Khatun',  '01911000005','mitu@mail.com',  'Comilla'),
('Rafiq Islam',  '01911000006','rafiq@mail.com', 'Barisal'),
('Shirin Akter', '01911000007','shirin@mail.com','Mymensingh'),
('Arif Hossain', '01911000008','arif@mail.com',  'Gazipur');

-- Additional Products (IDs 8-18)
INSERT INTO `products` (`name`,`sku`,`barcode`,`category_id`,`supplier_id`,`location_id`,`purchase_price`,`selling_price`,`quantity`,`low_stock_alert`,`description`,`status`) VALUES
('Laptop HP 14 Inch',         'PRD-20260101-008','8802000000001',1,4,1,45000.00,55000.00,10,2,'HP 14-inch Intel i5 laptop','active'),
('Bluetooth Headphones',      'PRD-20260101-009','8802000000002',1,4,1, 1200.00, 2000.00,22,5,'Wireless over-ear headphones','active'),
('USB Wired Keyboard',        'PRD-20260101-010','8802000000003',1,1,2,  300.00,  550.00,28,5,'Full-size USB keyboard','active'),
('LED Monitor 22 Inch',       'PRD-20260101-011','8802000000004',1,4,1, 8000.00,11000.00, 7,2,'22-inch Full HD monitor','active'),
('Ladies Kurti (Cotton)',      'PRD-20260101-012','8802000000005',2,5,1,  350.00,  700.00,45,8,'Assorted cotton kurtis','active'),
('Kids Printed T-Shirt',      'PRD-20260101-013','8802000000006',2,5,1,  120.00,  250.00,55,10,'Kids round-neck printed tee','active'),
('Chocolate Box (24 pcs)',    'PRD-20260101-014','8802000000007',3,6,2,  240.00,  380.00,90,20,'Assorted chocolate gift box','active'),
('Oreo Biscuit Pack',         'PRD-20260101-015','8802000000008',3,6,2,   40.00,   65.00,140,30,'Oreo family pack 432g','active'),
('Ballpoint Pen Box (12 pcs)','PRD-20260101-016','8802000000009',4,7,2,   60.00,  100.00,85,20,'Blue ink ballpoint box','active'),
('A5 Notebook Set (5 pcs)',   'PRD-20260101-017','8802000000010',4,7,2,   80.00,  150.00,70,15,'Ruled A5 notebooks set','active'),
('Power Bank 10000mAh',       'PRD-20260101-018','8802000000011',1,4,1,  800.00, 1400.00,20,4,'Slim 10000mAh power bank','active');

-- Stock In Records
INSERT INTO `stock_in` (`product_id`,`supplier_id`,`user_id`,`quantity`,`unit_cost`,`total_cost`,`note`,`received_date`) VALUES
(1,1,1,20,25000.00,500000.00,'Samsung A54 restock','2026-02-03 10:00:00'),
(2,1,1,10,80000.00,800000.00,'iPhone 15 new batch','2026-02-05 11:30:00'),
(3,2,2,50,200.00,10000.00,'Blue t-shirt restock','2026-02-08 09:00:00'),
(5,3,2,200,12.00,2400.00,'Mineral water monthly supply','2026-02-10 10:00:00'),
(6,1,1,10,350.00,3500.00,'A4 paper restock','2026-02-12 14:00:00'),
(7,1,2,25,500.00,12500.00,'Wireless mouse restock','2026-02-15 11:00:00'),
(8,4,1,15,45000.00,675000.00,'HP Laptop new stock','2026-02-18 10:00:00'),
(9,4,2,30,1200.00,36000.00,'Bluetooth headphones batch','2026-02-20 09:30:00'),
(10,1,3,30,300.00,9000.00,'USB keyboards','2026-02-22 10:00:00'),
(11,4,1,10,8000.00,80000.00,'LED Monitors','2026-02-25 11:00:00'),
(12,5,2,50,350.00,17500.00,'Ladies kurti new collection','2026-03-01 09:00:00'),
(13,5,2,60,120.00,7200.00,'Kids t-shirt fresh stock','2026-03-03 10:30:00'),
(14,6,3,100,240.00,24000.00,'Chocolate box bulk order','2026-03-05 11:00:00'),
(15,6,3,200,40.00,8000.00,'Oreo biscuit packs','2026-03-07 09:00:00'),
(16,7,2,100,60.00,6000.00,'Pen box stock','2026-03-10 10:00:00'),
(17,7,2,80,80.00,6400.00,'Notebook sets','2026-03-12 11:00:00'),
(18,4,1,25,800.00,20000.00,'Power bank stock','2026-03-15 10:00:00'),
(1,1,1,15,25000.00,375000.00,'Samsung A54 top-up','2026-03-20 09:00:00'),
(4,2,2,30,800.00,24000.00,'Salwar kameez restock','2026-03-22 10:00:00'),
(5,3,3,300,12.00,3600.00,'Water monthly bulk','2026-04-01 09:00:00'),
(9,4,1,20,1200.00,24000.00,'Headphones reorder','2026-04-05 11:00:00'),
(14,6,3,50,240.00,12000.00,'Chocolate restock','2026-04-10 10:00:00'),
(15,6,3,100,40.00,4000.00,'Biscuit restock','2026-04-12 09:30:00'),
(3,2,2,30,200.00,6000.00,'T-shirt reorder','2026-04-15 10:00:00'),
(7,1,1,15,500.00,7500.00,'Mouse reorder','2026-04-20 11:00:00');
