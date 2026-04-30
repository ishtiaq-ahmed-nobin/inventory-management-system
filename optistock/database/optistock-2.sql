-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 07:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `optistock`
--
CREATE DATABASE IF NOT EXISTS `optistock` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `optistock`;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `module`, `created_at`) VALUES
(1, 1, 'Logged in', 'auth', '2026-04-29 10:12:07'),
(2, 1, 'Completed sale INV-20260429-5691 total ৳95000', 'sales', '2026-04-29 10:16:43'),
(3, 1, 'Completed sale INV-20260429-2479 total ৳950', 'sales', '2026-04-29 10:17:25'),
(4, 1, 'Updated product: Apple iPhone 15', 'inventory', '2026-04-29 10:21:47'),
(5, 1, 'Updated product: Apple iPhone 15', 'inventory', '2026-04-29 10:22:01'),
(6, 1, 'Logged in', 'auth', '2026-04-29 11:42:14'),
(7, 1, 'Completed sale INV-20260429-9653 total ৳93000', 'sales', '2026-04-29 12:14:12'),
(8, 1, 'Completed sale INV-20260429-4176 total ৳20000', 'sales', '2026-04-29 12:14:44'),
(9, 1, 'Updated product: Apple iPhone 15', 'inventory', '2026-04-29 12:34:20'),
(10, 1, 'Updated product: Apple iPhone 15', 'inventory', '2026-04-29 12:35:00'),
(11, 1, 'Added customer: SR Optics', 'customers', '2026-04-29 12:55:10'),
(12, 1, 'Updated customer: Sujon Molla', 'customers', '2026-04-29 12:55:26'),
(13, 1, 'Added supplier: SR Optics', 'suppliers', '2026-04-29 12:55:49'),
(14, 1, 'Logged in', 'auth', '2026-04-30 10:10:58'),
(15, 1, 'Logged out', 'auth', '2026-04-30 10:11:02'),
(16, 1, 'Logged in', 'auth', '2026-04-30 10:11:22'),
(17, 1, 'Completed sale INV-20260430-0811 total ৳57000', 'sales', '2026-04-30 10:18:27'),
(18, 1, 'Updated product: Apple iPhone 15', 'inventory', '2026-04-30 10:19:01'),
(19, 1, 'Updated product: Laptop HP 14 Inch', 'inventory', '2026-04-30 10:19:34'),
(20, 1, 'Completed sale INV-20260430-3331 total ৳60000', 'sales', '2026-04-30 10:20:41'),
(21, 1, 'Completed sale INV-20260430-3405 total ৳30000', 'sales', '2026-04-30 10:21:52'),
(22, 1, 'Completed sale INV-20260430-6948 total ৳2000', 'sales', '2026-04-30 10:22:16'),
(23, 1, 'Completed sale INV-20260430-2584 total ৳150', 'sales', '2026-04-30 10:26:13'),
(24, 1, 'Completed sale INV-20260430-3021 total ৳500', 'sales', '2026-04-30 10:26:44'),
(25, 1, 'Logged in', 'auth', '2026-04-30 11:05:56');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Electronics', 'Electronic devices and accessories', '2026-04-29 10:11:52'),
(2, 'Clothing', 'Apparel and garments', '2026-04-29 10:11:52'),
(3, 'Food', 'Food and beverage items', '2026-04-29 10:11:52'),
(4, 'Stationery', 'Office and school stationery', '2026-04-29 10:11:52'),
(5, 'Other', 'Miscellaneous items', '2026-04-29 10:11:52');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `due_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `due_balance`, `created_at`) VALUES
(1, 'Walk-in Customer', '00000000000', '', 'N/A', 7550.00, '2026-04-29 10:11:52'),
(2, 'Rahim Ahmed', '01900000001', 'rahim@email.com', 'Dhaka', 0.00, '2026-04-29 10:11:52'),
(3, 'Karim Hossain', '01900000002', 'karim@email.com', 'Chittagong', 0.00, '2026-04-29 10:11:52'),
(4, 'Fatema Begum', '01911000001', 'fatema@mail.com', 'Uttara, Dhaka', 0.00, '2026-04-29 10:11:52'),
(5, 'Sohel Rana', '01911000002', 'sohel@mail.com', 'Sylhet', 0.00, '2026-04-29 10:11:52'),
(6, 'Nasrin Akter', '01911000003', 'nasrin@mail.com', 'Rajshahi', 0.00, '2026-04-29 10:11:52'),
(7, 'Jamal Uddin', '01911000004', 'jamal@mail.com', 'Khulna', 0.00, '2026-04-29 10:11:52'),
(8, 'Mitu Khatun', '01911000005', 'mitu@mail.com', 'Comilla', 0.00, '2026-04-29 10:11:52'),
(9, 'Rafiq Islam', '01911000006', 'rafiq@mail.com', 'Barisal', 0.00, '2026-04-29 10:11:52'),
(10, 'Shirin Akter', '01911000007', 'shirin@mail.com', 'Mymensingh', 0.00, '2026-04-29 10:11:52'),
(11, 'Arif Hossain', '01911000008', 'arif@mail.com', 'Gazipur', 0.00, '2026-04-29 10:11:52'),
(12, 'Sujon Molla', '0123456789', '', 'Jatrabari Dhaka', 0.00, '2026-04-29 12:55:10');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `address`, `created_at`) VALUES
(1, 'Main Warehouse', 'Dhaka, Bangladesh', '2026-04-29 10:11:52'),
(2, 'Branch Store', 'Chittagong, Bangladesh', '2026-04-29 10:11:52');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `supplier_id` int(10) UNSIGNED DEFAULT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `purchase_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock_alert` int(11) NOT NULL DEFAULT 5,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `sku`, `barcode`, `category_id`, `supplier_id`, `location_id`, `purchase_price`, `selling_price`, `quantity`, `low_stock_alert`, `description`, `status`, `created_at`) VALUES
(1, 'Samsung Galaxy A54', 'PRD-20240101-001', '8801234567890', 1, 1, 1, 25000.00, 30000.00, 12, 3, '6.4-inch AMOLED display smartphone', 'active', '2026-04-29 10:11:52'),
(2, 'Apple iPhone 15', 'PRD-20240101-002', '8801234567891', 1, 1, 1, 80000.00, 95000.00, 4, 5, 'Latest iPhone model', 'active', '2026-04-29 10:11:52'),
(3, 'Men T-Shirt (Blue)', 'PRD-20240101-003', '8801234567892', 2, 2, 1, 200.00, 400.00, 50, 10, 'Cotton t-shirt, sizes S-XL', 'active', '2026-04-29 10:11:52'),
(4, 'Women Salwar Kameez', 'PRD-20240101-004', '8801234567893', 2, 2, 1, 800.00, 1500.00, 30, 5, 'Traditional dress set', 'active', '2026-04-29 10:11:52'),
(5, 'Mineral Water 500ml', 'PRD-20240101-005', '8801234567894', 3, 3, 1, 12.00, 20.00, 200, 50, 'Pure mineral water', 'active', '2026-04-29 10:11:52'),
(6, 'A4 Paper Ream', 'PRD-20240101-006', '8801234567895', 4, 1, 2, 350.00, 500.00, 4, 5, '500 sheets per ream', 'active', '2026-04-29 10:11:52'),
(7, 'Wireless Mouse', 'PRD-20240101-007', '8801234567896', 1, 1, 1, 500.00, 800.00, 20, 5, 'USB wireless mouse', 'active', '2026-04-29 10:11:52'),
(8, 'Laptop HP 14 Inch', 'PRD-20260101-008', '8802000000001', 1, 4, 1, 45000.00, 55000.00, 22, 2, 'HP 14-inch Intel i5 laptop', 'active', '2026-04-29 10:11:52'),
(9, 'Bluetooth Headphones', 'PRD-20260101-009', '8802000000002', 1, 4, 1, 1200.00, 2000.00, 10, 5, 'Wireless over-ear headphones', 'active', '2026-04-29 10:11:52'),
(10, 'USB Wired Keyboard', 'PRD-20260101-010', '8802000000003', 1, 1, 2, 300.00, 550.00, 27, 5, 'Full-size USB keyboard', 'active', '2026-04-29 10:11:52'),
(11, 'LED Monitor 22 Inch', 'PRD-20260101-011', '8802000000004', 1, 4, 1, 8000.00, 11000.00, 7, 2, '22-inch Full HD monitor', 'active', '2026-04-29 10:11:52'),
(12, 'Ladies Kurti (Cotton)', 'PRD-20260101-012', '8802000000005', 2, 5, 1, 350.00, 700.00, 44, 8, 'Assorted cotton kurtis', 'active', '2026-04-29 10:11:52'),
(13, 'Kids Printed T-Shirt', 'PRD-20260101-013', '8802000000006', 2, 5, 1, 120.00, 250.00, 54, 10, 'Kids round-neck printed tee', 'active', '2026-04-29 10:11:52'),
(14, 'Chocolate Box (24 pcs)', 'PRD-20260101-014', '8802000000007', 3, 6, 2, 240.00, 380.00, 90, 20, 'Assorted chocolate gift box', 'active', '2026-04-29 10:11:52'),
(15, 'Oreo Biscuit Pack', 'PRD-20260101-015', '8802000000008', 3, 6, 2, 40.00, 65.00, 140, 30, 'Oreo family pack 432g', 'active', '2026-04-29 10:11:52'),
(16, 'Ballpoint Pen Box (12 pcs)', 'PRD-20260101-016', '8802000000009', 4, 7, 2, 60.00, 100.00, 85, 20, 'Blue ink ballpoint box', 'active', '2026-04-29 10:11:52'),
(17, 'A5 Notebook Set (5 pcs)', 'PRD-20260101-017', '8802000000010', 4, 7, 2, 80.00, 150.00, 69, 15, 'Ruled A5 notebooks set', 'active', '2026-04-29 10:11:52'),
(18, 'Power Bank 10000mAh', 'PRD-20260101-018', '8802000000011', 1, 4, 1, 800.00, 1400.00, 20, 4, 'Slim 10000mAh power bank', 'active', '2026-04-29 10:11:52');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_no` varchar(30) NOT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_type` enum('cash','card','mobile_banking') NOT NULL DEFAULT 'cash',
  `note` text DEFAULT NULL,
  `sale_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `invoice_no`, `customer_id`, `user_id`, `subtotal`, `discount`, `total_amount`, `paid_amount`, `due_amount`, `payment_type`, `note`, `sale_date`, `created_at`) VALUES
(1, 'INV-20260429-5691', 1, 1, 95000.00, 0.00, 95000.00, 95000.00, 0.00, 'mobile_banking', '', '2026-04-28 10:16:43', '2026-04-29 10:16:43'),
(2, 'INV-20260429-2479', 1, 1, 950.00, 0.00, 950.00, 950.00, 0.00, 'cash', '', '2026-04-27 10:17:25', '2026-04-29 10:17:25'),
(3, 'INV-20260429-9653', 9, 1, 95000.00, 2000.00, 93000.00, 93000.00, 0.00, 'cash', '', '2026-04-29 12:14:12', '2026-04-29 12:14:12'),
(4, 'INV-20260429-4176', 1, 1, 20000.00, 0.00, 20000.00, 20000.00, 0.00, 'card', '', '2026-04-27 12:14:43', '2026-04-29 12:14:43'),
(5, 'INV-20260430-0811', 1, 1, 57000.00, 0.00, 57000.00, 57000.00, 0.00, 'cash', '', '2026-04-25 10:18:27', '2026-04-30 10:18:27'),
(6, 'INV-20260430-3331', 1, 1, 60000.00, 0.00, 60000.00, 60000.00, 0.00, 'cash', '', '2026-04-26 10:20:41', '2026-04-30 10:20:41'),
(7, 'INV-20260430-3405', 1, 1, 30000.00, 0.00, 30000.00, 25000.00, 5000.00, 'cash', '', '2026-04-27 10:21:52', '2026-04-30 10:21:52'),
(8, 'INV-20260430-6948', 1, 1, 2000.00, 0.00, 2000.00, 100.00, 1900.00, 'cash', '', '2026-04-30 10:22:16', '2026-04-30 10:22:16'),
(9, 'INV-20260430-2584', 1, 1, 150.00, 0.00, 150.00, 0.00, 150.00, 'cash', '', '2026-04-30 10:26:13', '2026-04-30 10:26:13'),
(10, 'INV-20260430-3021', 1, 1, 550.00, 50.00, 500.00, 0.00, 500.00, 'cash', '', '2026-04-30 10:26:44', '2026-04-30 10:26:44');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `sale_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 1, 2, 1, 95000.00, 95000.00),
(2, 2, 12, 1, 700.00, 700.00),
(3, 2, 13, 1, 250.00, 250.00),
(4, 3, 2, 1, 95000.00, 95000.00),
(5, 4, 9, 10, 2000.00, 20000.00),
(6, 5, 9, 1, 2000.00, 2000.00),
(7, 5, 8, 1, 55000.00, 55000.00),
(8, 6, 1, 2, 30000.00, 60000.00),
(9, 7, 1, 1, 30000.00, 30000.00),
(10, 8, 9, 1, 2000.00, 2000.00),
(11, 9, 17, 1, 150.00, 150.00),
(12, 10, 10, 1, 550.00, 550.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_in`
--

CREATE TABLE `stock_in` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `supplier_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `received_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_in`
--

INSERT INTO `stock_in` (`id`, `product_id`, `supplier_id`, `user_id`, `quantity`, `unit_cost`, `total_cost`, `note`, `received_date`, `created_at`) VALUES
(1, 1, 1, 1, 20, 25000.00, 500000.00, 'Samsung A54 restock', '2026-02-03 10:00:00', '2026-04-29 10:11:52'),
(2, 2, 1, 1, 10, 80000.00, 800000.00, 'iPhone 15 new batch', '2026-02-05 11:30:00', '2026-04-29 10:11:52'),
(3, 3, 2, 2, 50, 200.00, 10000.00, 'Blue t-shirt restock', '2026-02-08 09:00:00', '2026-04-29 10:11:52'),
(4, 5, 3, 2, 200, 12.00, 2400.00, 'Mineral water monthly supply', '2026-02-10 10:00:00', '2026-04-29 10:11:52'),
(5, 6, 1, 1, 10, 350.00, 3500.00, 'A4 paper restock', '2026-02-12 14:00:00', '2026-04-29 10:11:52'),
(6, 7, 1, 2, 25, 500.00, 12500.00, 'Wireless mouse restock', '2026-02-15 11:00:00', '2026-04-29 10:11:52'),
(7, 8, 4, 1, 15, 45000.00, 675000.00, 'HP Laptop new stock', '2026-02-18 10:00:00', '2026-04-29 10:11:52'),
(8, 9, 4, 2, 30, 1200.00, 36000.00, 'Bluetooth headphones batch', '2026-02-20 09:30:00', '2026-04-29 10:11:52'),
(9, 10, 1, 3, 30, 300.00, 9000.00, 'USB keyboards', '2026-02-22 10:00:00', '2026-04-29 10:11:52'),
(10, 11, 4, 1, 10, 8000.00, 80000.00, 'LED Monitors', '2026-02-25 11:00:00', '2026-04-29 10:11:52'),
(11, 12, 5, 2, 50, 350.00, 17500.00, 'Ladies kurti new collection', '2026-03-01 09:00:00', '2026-04-29 10:11:52'),
(12, 13, 5, 2, 60, 120.00, 7200.00, 'Kids t-shirt fresh stock', '2026-03-03 10:30:00', '2026-04-29 10:11:52'),
(13, 14, 6, 3, 100, 240.00, 24000.00, 'Chocolate box bulk order', '2026-03-05 11:00:00', '2026-04-29 10:11:52'),
(14, 15, 6, 3, 200, 40.00, 8000.00, 'Oreo biscuit packs', '2026-03-07 09:00:00', '2026-04-29 10:11:52'),
(15, 16, 7, 2, 100, 60.00, 6000.00, 'Pen box stock', '2026-03-10 10:00:00', '2026-04-29 10:11:52'),
(16, 17, 7, 2, 80, 80.00, 6400.00, 'Notebook sets', '2026-03-12 11:00:00', '2026-04-29 10:11:52'),
(17, 18, 4, 1, 25, 800.00, 20000.00, 'Power bank stock', '2026-03-15 10:00:00', '2026-04-29 10:11:52'),
(18, 1, 1, 1, 15, 25000.00, 375000.00, 'Samsung A54 top-up', '2026-03-20 09:00:00', '2026-04-29 10:11:52'),
(19, 4, 2, 2, 30, 800.00, 24000.00, 'Salwar kameez restock', '2026-03-22 10:00:00', '2026-04-29 10:11:52'),
(20, 5, 3, 3, 300, 12.00, 3600.00, 'Water monthly bulk', '2026-04-01 09:00:00', '2026-04-29 10:11:52'),
(21, 9, 4, 1, 20, 1200.00, 24000.00, 'Headphones reorder', '2026-04-05 11:00:00', '2026-04-29 10:11:52'),
(22, 14, 6, 3, 50, 240.00, 12000.00, 'Chocolate restock', '2026-04-10 10:00:00', '2026-04-29 10:11:52'),
(23, 15, 6, 3, 100, 40.00, 4000.00, 'Biscuit restock', '2026-04-12 09:30:00', '2026-04-29 10:11:52'),
(24, 3, 2, 2, 30, 200.00, 6000.00, 'T-shirt reorder', '2026-04-15 10:00:00', '2026-04-29 10:11:52'),
(25, 7, 1, 1, 15, 500.00, 7500.00, 'Mouse reorder', '2026-04-20 11:00:00', '2026-04-29 10:11:52');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `due_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `phone`, `email`, `address`, `due_balance`, `created_at`) VALUES
(1, 'Tech Supplies Ltd', '01800000001', 'tech@supplier.com', 'Dhaka, Bangladesh', 0.00, '2026-04-29 10:11:52'),
(2, 'Fashion House', '01800000002', 'fashion@supplier.com', 'Narayanganj, Bangladesh', 0.00, '2026-04-29 10:11:52'),
(3, 'Food Distributors', '01800000003', 'food@supplier.com', 'Gazipur, Bangladesh', 0.00, '2026-04-29 10:11:52'),
(4, 'Mobile World BD', '01811000001', 'mobile@mwbd.com', 'Mirpur, Dhaka', 0.00, '2026-04-29 10:11:52'),
(5, 'Garment Factory Direct', '01811000002', 'gfd@garment.com', 'Narayanganj', 0.00, '2026-04-29 10:11:52'),
(6, 'Wholesale Foods BD', '01811000003', 'info@wfbd.com', 'Gazipur', 0.00, '2026-04-29 10:11:52'),
(7, 'Office Supplies Co', '01811000004', 'sales@offco.com', 'Motijheel, Dhaka', 0.00, '2026-04-29 10:11:52'),
(8, 'SR Optics', '0123456789', '', 'Jatrabari Dhaka', 0.00, '2026-04-29 12:55:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','staff') NOT NULL DEFAULT 'staff',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `dark_mode` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `status`, `dark_mode`, `created_at`) VALUES
(1, 'Admin User', 'admin@optistock.com', '01700000000', '$2y$10$b7Q.ozYbBtrEEpG7n2TKpOvOyhKvkEKmG5UgQs6M1Fj6MxCe8l5/6', 'admin', 'active', 0, '2026-04-29 10:11:51'),
(2, 'Manager One', 'manager@optistock.com', '01711111111', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 'active', 0, '2026-04-29 10:11:51'),
(3, 'Staff User', 'staff@optistock.com', '01722222222', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 'active', 0, '2026-04-29 10:11:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_sku` (`sku`),
  ADD KEY `idx_barcode` (`barcode`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `idx_sale_date` (`sale_date`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_in`
--
ALTER TABLE `stock_in`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_received_date` (`received_date`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stock_in`
--
ALTER TABLE `stock_in`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_in`
--
ALTER TABLE `stock_in`
  ADD CONSTRAINT `stock_in_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_in_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_in_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"optistock\",\"table\":\"sales\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2026-04-30 05:07:10', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
