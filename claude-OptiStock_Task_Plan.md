# OptiStock — Inventory Management System
## Complete Task Plan for Code Editor Bot

> **IMPORTANT:** Do NOT start writing any code until the user explicitly confirms.
> Read this entire document first, then wait for the "go ahead" instruction.

---

## Project Overview

| Field | Value |
|---|---|
| Project Name | OptiStock |
| Type | Web-based Inventory Management System |
| Stack | PHP 8+, MySQL 8, Bootstrap 4, jQuery, JavaScript |
| Icons | Google Material Icons (CDN) |
| Language | English |
| No frameworks | No Laravel, no React, no Vue, no Composer |
| Auth | PHP session-based login |
| Target | Class project / medium system, 20+ users |
| Color Theme | Theme 2 — Dark Slate + Teal |

---

## Folder Structure to Create

```
optistock/
├── assets/
│   ├── css/
│   │   ├── bootstrap.min.css        (Bootstrap 4 CDN or local)
│   │   ├── style.css                (custom styles)
│   │   └── dark.css                 (dark mode overrides)
│   ├── js/
│   │   ├── jquery.min.js
│   │   ├── bootstrap.bundle.min.js
│   │   ├── app.js                   (global JS helpers)
│   │   ├── barcode.js               (JsBarcode integration)
│   │   └── darkmode.js              (dark mode toggle + localStorage)
│   └── img/
│       └── logo.png
├── config/
│   └── db.php                       (PDO MySQL connection)
├── includes/
│   ├── header.php                   (HTML head + navbar)
│   ├── sidebar.php                  (left nav with Material Icons)
│   ├── footer.php                   (closing tags + scripts)
│   └── auth_check.php               (session role guard)
├── modules/
│   ├── auth/
│   │   ├── login.php
│   │   └── logout.php
│   ├── dashboard/
│   │   └── index.php
│   ├── inventory/
│   │   ├── index.php                (product list)
│   │   ├── add.php
│   │   ├── edit.php
│   │   └── delete.php
│   ├── stock_in/
│   │   ├── index.php                (purchase list)
│   │   └── add.php
│   ├── sales/
│   │   ├── index.php                (sales list)
│   │   ├── pos.php                  (POS cart UI)
│   │   └── invoice.php              (printable invoice)
│   ├── suppliers/
│   │   ├── index.php
│   │   ├── add.php
│   │   └── edit.php
│   ├── customers/
│   │   ├── index.php
│   │   ├── add.php
│   │   └── edit.php
│   ├── reports/
│   │   ├── sales_report.php
│   │   ├── purchase_report.php
│   │   ├── stock_report.php
│   │   └── profit_loss.php
│   └── users/
│       ├── index.php
│       ├── add.php
│       └── edit.php
├── settings/
│   └── save_darkmode.php            (AJAX endpoint — save dark mode preference)
├── database/
│   └── optistock.sql                (full schema + seed data)
└── index.php                        (redirects to login or dashboard)
```

---

## Database Schema (MySQL)

### Tables to create in `optistock.sql`

#### 1. `users`
```sql
id, name, email, phone, password (bcrypt), role ENUM('admin','manager','staff'),
status ENUM('active','inactive'), created_at
```

#### 2. `categories`
```sql
id, name, description, created_at
```

#### 3. `locations` (multi-warehouse)
```sql
id, name, address, created_at
```

#### 4. `suppliers`
```sql
id, name, phone, email, address, due_balance DECIMAL(10,2), created_at
```

#### 5. `customers`
```sql
id, name, phone, email, address, due_balance DECIMAL(10,2), created_at
```

#### 6. `products`
```sql
id, name, sku VARCHAR(50) UNIQUE, barcode VARCHAR(100),
category_id FK, supplier_id FK, location_id FK,
purchase_price DECIMAL(10,2), selling_price DECIMAL(10,2),
quantity INT DEFAULT 0, low_stock_alert INT DEFAULT 5,
description TEXT, status ENUM('active','inactive'), created_at
```

#### 7. `stock_in` (purchases)
```sql
id, product_id FK, supplier_id FK, user_id FK,
quantity INT, unit_cost DECIMAL(10,2), total_cost DECIMAL(10,2),
note TEXT, received_date DATETIME, created_at
```

#### 8. `sales`
```sql
id, invoice_no VARCHAR(30) UNIQUE, customer_id FK, user_id FK,
subtotal DECIMAL(10,2), discount DECIMAL(10,2), total_amount DECIMAL(10,2),
paid_amount DECIMAL(10,2), due_amount DECIMAL(10,2),
payment_type ENUM('cash','card','mobile_banking'),
note TEXT, sale_date DATETIME, created_at
```

#### 9. `sale_items`
```sql
id, sale_id FK, product_id FK,
quantity INT, unit_price DECIMAL(10,2), total_price DECIMAL(10,2)
```

#### 10. `activity_log` (optional)
```sql
id, user_id FK, action VARCHAR(255), module VARCHAR(50), created_at
```

---

## Module-by-Module Task List

---

### TASK 1 — Database + Config
**File:** `database/optistock.sql`, `config/db.php`

- Create all 10 tables with correct foreign keys
- Add indexes on: `sku`, `barcode`, `sale_date`, `received_date`
- Seed 1 default admin user: `admin@optistock.com / admin123` (bcrypt hashed)
- Seed sample categories: Electronics, Clothing, Food, Stationery, Other
- Seed 1 default location: "Main Warehouse"
- `config/db.php`: PDO connection with error handling, charset utf8mb4

---

### TASK 2 — Auth (Login / Logout)
**Files:** `modules/auth/login.php`, `modules/auth/logout.php`, `includes/auth_check.php`

- Login form: email + password, Bootstrap 4 card layout, Material Icons
- PHP: verify bcrypt password, store `$_SESSION['user_id']`, `role`, `name`
- `auth_check.php`: include at top of every protected page; redirect to login if not logged in
- Role check function: `require_role(['admin','manager'])` — redirect with error if insufficient
- Logout: destroy session, redirect to login
- Show error message on wrong credentials

---

### TASK 3 — Layout (Header, Sidebar, Footer)
**Files:** `includes/header.php`, `includes/sidebar.php`, `includes/footer.php`

- Bootstrap 4 fixed sidebar layout (left nav, top navbar, content area)
- Google Material Icons loaded from CDN
- Sidebar links with icons:
  - dashboard — `dashboard`
  - Inventory — `inventory`
  - Stock In — `add_box`
  - Sales / POS — `point_of_sale`
  - Suppliers — `local_shipping`
  - Customers — `people`
  - Reports — `bar_chart`
  - Users — `manage_accounts` (admin only)
  - Logout — `logout`
- Active link highlight based on current page
- Top navbar: show logged-in user name + role badge
- Low stock alert count badge on sidebar Inventory link
- Responsive: sidebar collapses on mobile (hamburger toggle)

---

### TASK 4 — Dashboard
**File:** `modules/dashboard/index.php`

Summary cards (Bootstrap 4 cards, row of 4):
- Total products
- Total stock value (sum of quantity × purchase_price)
- Today's sales total
- Low stock items count (quantity <= low_stock_alert)

Below cards:
- Recent 10 sales table
- Low stock alert table (products below threshold, red badge)
- Bar chart: last 7 days sales (Chart.js CDN)

---

### TASK 5 — Inventory (Products)
**Files:** `modules/inventory/`

**index.php — Product list:**
- DataTable (jQuery DataTables CDN) with: Name, SKU, Category, Qty, Sell Price, Status, Actions
- Qty column: red badge if low stock, green if OK
- Search + filter by category
- Buttons: Add Product, Edit, Delete (with confirmation)
- Barcode display: show barcode number, click to view barcode image (JsBarcode)

**add.php / edit.php — Product form:**
- Fields: Name, SKU (auto-suggest or manual), Category (dropdown), Supplier (dropdown),
  Location (dropdown), Barcode (auto-generate button), Purchase Price, Selling Price,
  Quantity, Low Stock Alert, Description, Status
- Auto-generate SKU: `PRD-YYYYMMDD-RANDOM`
- Auto-generate barcode: JsBarcode renders preview, save value to hidden input
- Validation: required fields, SKU unique check via AJAX

**delete.php:** soft delete (set status = inactive) or hard delete with confirmation

---

### TASK 6 — Stock In (Purchase)
**Files:** `modules/stock_in/`

**index.php — Purchase list:**
- Table: Date, Product, Supplier, Qty, Unit Cost, Total Cost, Added by, Actions
- Date range filter

**add.php — Receive stock form:**
- Fields: Product (searchable dropdown), Supplier, Quantity, Unit Cost (auto-fill from product), Date, Note
- On save: INSERT into `stock_in`, UPDATE `products.quantity += qty`
- Show printable GRN (Goods Received Note) after save — `window.print()` button

---

### TASK 7 — Sales / POS
**Files:** `modules/sales/`

**pos.php — Point of Sale:**
- Left panel: product search by name or barcode (text input, USB scanner friendly — keypress enter triggers search)
- Product card grid results
- Right panel: cart table (product, qty, unit price, subtotal, remove button)
- Cart totals: subtotal, discount input, grand total
- Payment section: Cash / Card / Mobile Banking (bKash/Nagad/Other) radio buttons
- Customer selector (optional, dropdown with "Walk-in" default)
- "Complete Sale" button: POST to save, deduct stock, generate invoice
- Validation: cannot sell more than available stock

**invoice.php — Printable invoice:**
- Invoice number, date, customer, items table, totals, payment method
- Print button (`window.print()`)
- Print CSS: hide sidebar/navbar on print

**index.php — Sales history list:**
- Table: Invoice No, Customer, Total, Payment Type, Date, Staff, Actions (view invoice)
- Date range filter, search

---

### TASK 8 — Suppliers
**Files:** `modules/suppliers/`

- List: Name, Phone, Email, Due Balance, Actions
- Add/Edit form: Name, Phone, Email, Address, Note
- View supplier: show purchase history linked to this supplier
- Due balance: updated when stock_in is recorded on credit (optional field)

---

### TASK 9 — Customers
**Files:** `modules/customers/`

- List: Name, Phone, Email, Due Balance, Actions
- Add/Edit form: Name, Phone, Email, Address
- View customer: show sales history linked to this customer
- Due balance: auto-calculated from sales where paid_amount < total_amount

---

### TASK 10 — Reports
**Files:** `modules/reports/`

All reports have: date range filter (from/to), search, print button.

**sales_report.php:**
- Table: Date, Invoice No, Customer, Items, Total, Payment Type
- Summary: total sales count, total revenue for selected period

**purchase_report.php:**
- Table: Date, Product, Supplier, Qty, Unit Cost, Total Cost
- Summary: total purchases, total cost

**stock_report.php:**
- Table: Product, SKU, Category, Current Qty, Purchase Price, Sell Price, Stock Value
- Filter by category, location
- Highlight low stock rows in red

**profit_loss.php:**
- Summary: total revenue - total cost of goods sold = gross profit
- Monthly breakdown table
- Simple bar chart (Chart.js)

---

### TASK 11 — User Management
**Files:** `modules/users/` (admin only)

- List users: Name, Email, Role, Status, Actions
- Add user form: Name, Email, Phone, Password, Role (admin/manager/staff), Status
- Edit user: same fields, password field optional (leave blank = no change)
- Cannot delete own account
- Password stored as bcrypt hash

---

### TASK 12 — Dark Mode
**Files:** `assets/css/dark.css`, `assets/js/darkmode.js`, update `includes/header.php`, `includes/sidebar.php`, `config/db.php` (save preference)

#### Overview
Implement a full dark mode toggle that persists per user using `localStorage` + a server-side preference saved in the `users` table. No extra libraries — pure CSS variables + jQuery toggle.

---

#### Step 12.1 — Database change
Add a column to the `users` table:
```sql
ALTER TABLE users ADD COLUMN dark_mode TINYINT(1) DEFAULT 0;
```

---

#### Step 12.2 — CSS variables (`assets/css/dark.css`)
Define all dark mode overrides using a `body.dark-mode` class selector. Do NOT modify `bootstrap.min.css`.

Cover these elements:
```css
body.dark-mode                        /* page background: #0D1117, text: #CDD9E5 */
body.dark-mode .sidebar               /* sidebar bg: #0D1117, link color: #768390 */
body.dark-mode .navbar                /* navbar bg: #161B22, border-bottom: #2D3748 */
body.dark-mode .card                  /* bg: #1C2333, border: #2D3748, text: #CDD9E5 */
body.dark-mode .table                 /* bg: #1C2333, text: #CDD9E5, border: #2D3748 */
body.dark-mode .table thead th        /* bg: #161B22, color: #CDD9E5 */
body.dark-mode .table-striped tbody tr:nth-of-type(odd) /* bg: #21293D */
body.dark-mode .table-hover tbody tr:hover              /* bg: #2D3748 */
body.dark-mode .form-control          /* bg: #1C2333, border: #2D3748, color: #CDD9E5 */
body.dark-mode .form-control:focus    /* bg: #21293D, border: #26C6DA */
body.dark-mode .btn-light             /* bg: #2D3748, color: #CDD9E5 */
body.dark-mode .modal-content         /* bg: #1C2333, border: #2D3748 */
body.dark-mode .dropdown-menu         /* bg: #1C2333, border: #2D3748 */
body.dark-mode .dropdown-item         /* color: #CDD9E5 */
body.dark-mode .dropdown-item:hover   /* bg: #2D3748 */
body.dark-mode .alert-success         /* bg: #0D2818, color: #69DB7C, border: #1A5C33 */
body.dark-mode .alert-danger          /* bg: #2D0A12, color: #FF6B6B, border: #6A1A24 */
body.dark-mode .badge-secondary       /* bg: #2D3748 */
body.dark-mode .dataTables_wrapper    /* color: #CDD9E5 */
body.dark-mode .page-item .page-link  /* bg: #1C2333, color: #CDD9E5, border: #2D3748 */
body.dark-mode .page-item.active .page-link /* bg: #00BCD4, border: #00BCD4 */
body.dark-mode select option          /* bg: #1C2333, color: #CDD9E5 */
```

Chart.js dark mode: when dark mode is active, re-render charts with:
- `color: '#CDD9E5'` for labels
- `gridColor: '#2D3748'` for grid lines
- `backgroundColor: 'rgba(0, 188, 212, 0.5)'` for teal bars (sales)
- `backgroundColor: 'rgba(38, 198, 218, 0.4)'` for accent bars (purchases)

---

#### Step 12.3 — Toggle JS (`assets/js/darkmode.js`)

```javascript
// Logic to implement (write in jQuery):

// 1. On page load:
//    - Check localStorage for 'optistock_dark_mode' key
//    - If '1': add class 'dark-mode' to <body> immediately (before page renders)
//      to avoid flash of light mode
//    - Set toggle button icon accordingly

// 2. Toggle button click (#darkModeToggle in navbar):
//    - Toggle 'dark-mode' class on <body>
//    - Save preference to localStorage
//    - Send AJAX POST to modules/settings/save_darkmode.php with value (0 or 1)
//      so preference is saved in DB for this user
//    - Swap toggle icon: 'dark_mode' (moon) ↔ 'light_mode' (sun) Material Icon
//    - If Chart.js charts are on the page, call a refreshCharts() function

// 3. refreshCharts() function:
//    - Destroy and re-init all Chart.js instances on the page
//      with dark or light color config depending on current mode
```

---

#### Step 12.4 — Toggle button in navbar (`includes/header.php`)

Add this button inside the navbar, before the user dropdown:
```html
<button id="darkModeToggle" class="btn btn-sm btn-outline-secondary mr-2" title="Toggle dark mode">
  <span class="material-icons" style="font-size:18px; vertical-align:middle;">dark_mode</span>
</button>
```

- Icon shows `dark_mode` (moon) in light mode, `light_mode` (sun) in dark mode
- Add `dark.css` link in `<head>` of `header.php` after `style.css`
- Add `darkmode.js` script in `footer.php` after `app.js`
- On PHP side: read `$_SESSION['dark_mode']` and echo `dark-mode` class on `<body>` tag as server-side fallback (prevents flash on first load after login)

---

#### Step 12.5 — Save preference endpoint
**File:** `modules/settings/save_darkmode.php`

```php
// Accepts POST: { dark_mode: 0 or 1 }
// Validates: user must be logged in (auth_check)
// Updates: users SET dark_mode = ? WHERE id = $_SESSION['user_id']
// Updates: $_SESSION['dark_mode'] = value
// Returns: JSON { success: true }
```

---

#### Step 12.6 — Load preference on login
In `modules/auth/login.php`, after successful login, fetch `dark_mode` column and store in session:
```php
$_SESSION['dark_mode'] = $user['dark_mode']; // 0 or 1
```

In `includes/header.php`, apply to body tag:
```php
<body class="<?= ($_SESSION['dark_mode'] ?? 0) ? 'dark-mode' : '' ?>">
```

---

#### Step 12.7 — Login page dark mode
The login page has no session yet, so use `localStorage` only:
- `darkmode.js` runs on login page too
- Adds `dark-mode` to body if localStorage says so
- Style the login card and form inputs with the same dark CSS variables

---

#### Dark mode color palette reference

| Element | Light mode | Dark mode |
|---|---|---|
| Page background | `#F4F6F8` | `#0D1117` |
| Sidebar | `#1A1F2E` | `#0D1117` |
| Navbar | `#FFFFFF` | `#161B22` |
| Card / panel bg | `#FFFFFF` | `#1C2333` |
| Table row | `#FFFFFF` | `#1C2333` |
| Table stripe | `#F4F6F8` | `#21293D` |
| Input bg | `#FFFFFF` | `#1C2333` |
| Input focus | `#FFFFFF` | `#21293D` |
| Body text | `#212529` | `#CDD9E5` |
| Muted text | `#6C757D` | `#768390` |
| Border / divider | `#E0E0E0` | `#2D3748` |
| Primary accent | `#00BCD4` | `#26C6DA` |
| Success badge | `#00C853` | `#69DB7C` |
| Warning badge | `#FFD600` | `#FFE066` |
| Danger badge | `#FF1744` | `#FF6B6B` |

---

## UI / UX Requirements

- Bootstrap 4 throughout — no Bootstrap 5
- Google Material Icons for all icons (loaded from CDN: `https://fonts.googleapis.com/icon?family=Material+Icons`)
- Responsive design: works on mobile and desktop
- jQuery for: AJAX calls, form validation, dynamic dropdowns, cart logic, DataTables
- DataTables CDN for all list/table pages (sorting, pagination, search)
- Chart.js CDN for dashboard and profit/loss charts
- JsBarcode CDN for barcode generation
- Flash messages: success/error using Bootstrap alerts (stored in `$_SESSION['flash']`)
- Color theme: **Theme 2 — Dark Slate + Teal** (see full palette below)
- Print-friendly CSS for invoices and GRN (hide sidebar/navbar)

---

## Color Theme — Theme 2: Dark Slate + Teal

Apply these exact hex values throughout `assets/css/style.css` using CSS custom properties.

### CSS Variables (add to `:root` in `style.css`)

```css
:root {
  /* Brand colors */
  --color-sidebar:      #1A1F2E;   /* sidebar background */
  --color-primary:      #00BCD4;   /* buttons, links, active states */
  --color-accent:       #26C6DA;   /* hover highlights, focus rings */

  /* Semantic colors */
  --color-success:      #00C853;   /* paid badges, stock OK, save buttons */
  --color-warning:      #FFD600;   /* low stock alerts, due badges */
  --color-danger:       #FF1744;   /* delete buttons, out of stock, errors */

  /* Derived / supporting */
  --color-primary-dark:  #0097A7;  /* button hover state for primary */
  --color-primary-light: #E0F7FA;  /* light tinted backgrounds (badge bg) */
  --color-sidebar-text:  #B0BEC5;  /* inactive sidebar link text */
  --color-sidebar-active-bg: rgba(0, 188, 212, 0.15); /* active link bg */
  --color-topbar:       #FFFFFF;   /* top navbar background */
  --color-page-bg:      #F4F6F8;   /* main content area background */
  --color-card-bg:      #FFFFFF;   /* card / panel background */
  --color-text-main:    #212529;   /* primary body text */
  --color-text-muted:   #6C757D;   /* secondary / helper text */
  --color-border:       #E0E0E0;   /* card borders, table borders */
}
```

### Where to Apply Each Color

| Element | Value | CSS property |
|---|---|---|
| `<body>` background | `#F4F6F8` | `background-color` |
| Sidebar background | `#1A1F2E` | `background-color` |
| Sidebar logo text | `#00BCD4` | `color` |
| Sidebar active link bg | `rgba(0,188,212,0.15)` | `background-color` |
| Sidebar active link text | `#00BCD4` | `color` |
| Sidebar active link left border | `#00BCD4` | `border-left: 3px solid` |
| Sidebar inactive link text | `#B0BEC5` | `color` |
| Sidebar inactive icon bg | `rgba(255,255,255,0.07)` | `background-color` |
| Top navbar background | `#FFFFFF` | `background-color` |
| Top navbar bottom border | `#E0E0E0` | `border-bottom` |
| Primary buttons (`.btn-primary`) | `#00BCD4` | `background-color` |
| Primary button hover | `#0097A7` | `background-color` |
| Danger buttons (`.btn-danger`) | `#FF1744` | `background-color` |
| Success badge / "Paid" | `#00C853` text on `#E0F7FA` bg | `color` + `background-color` |
| Warning badge / "Due" / "Low stock" | `#B8960C` text on `#FFF9C4` bg | `color` + `background-color` |
| Danger badge / "Out of stock" | `#FF1744` text on `#FFE5EA` bg | `color` + `background-color` |
| Card border | `#E0E0E0` | `border` |
| Table header background | `#F4F6F8` | `background-color` |
| DataTables pagination active | `#00BCD4` | `background-color` |
| Chart.js bar color (sales) | `#00BCD4` | `backgroundColor` |
| Chart.js bar color (purchases) | `#26C6DA` | `backgroundColor` |
| Login page card top border accent | `#00BCD4` | `border-top: 4px solid` |
| Focus ring on inputs | `#00BCD4` | `box-shadow: 0 0 0 0.2rem rgba(0,188,212,0.25)` |

### Bootstrap 4 Override Classes (add to `style.css`)

```css
.btn-primary {
  background-color: #00BCD4;
  border-color: #00BCD4;
}
.btn-primary:hover, .btn-primary:focus {
  background-color: #0097A7;
  border-color: #0097A7;
}
.sidebar { background-color: #1A1F2E; }
.sidebar .nav-link { color: #B0BEC5; }
.sidebar .nav-link.active {
  color: #00BCD4;
  background-color: rgba(0,188,212,0.15);
  border-left: 3px solid #00BCD4;
}
.sidebar .brand-text { color: #00BCD4; }
a { color: #00BCD4; }
a:hover { color: #0097A7; }
.badge-success-custom { background-color: #E0F7FA; color: #007B8A; }
.badge-warning-custom { background-color: #FFF9C4; color: #B8960C; }
.badge-danger-custom  { background-color: #FFE5EA; color: #C0001A; }
.page-item.active .page-link { background-color: #00BCD4; border-color: #00BCD4; }
.page-link { color: #00BCD4; }
.page-link:hover { color: #0097A7; }
```

### Quick Reference Palette

| Name | Hex | Use |
|---|---|---|
| Sidebar | `#1A1F2E` | Sidebar bg |
| Primary | `#00BCD4` | Buttons, links, active nav, charts |
| Accent | `#26C6DA` | Hover, highlights |
| Primary dark | `#0097A7` | Button hover |
| Primary light | `#E0F7FA` | Badge backgrounds |
| Success | `#00C853` | Paid, in-stock, saved |
| Warning | `#FFD600` | Low stock, due balance |
| Danger | `#FF1744` | Delete, out-of-stock, errors |
| Page bg | `#F4F6F8` | Content area |
| Card bg | `#FFFFFF` | Cards, tables |
| Text | `#212529` | Body text |
| Muted | `#6C757D` | Labels, hints |
| Border | `#E0E0E0` | All borders |

---

## CDN Links to Use

```html
<!-- Bootstrap 4 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Google Material Icons -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- JsBarcode -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
```

---

## Security Requirements

- All user inputs: `htmlspecialchars()` on output, PDO prepared statements on DB queries
- Session check on every protected page via `auth_check.php`
- Role-based access: admin-only pages checked at the top of each file
- Passwords: `password_hash()` + `password_verify()` (bcrypt)
- No raw SQL string concatenation — always use PDO `?` placeholders

---

## Build Order (Day-by-Day)

| Day | Tasks |
|---|---|
| Day 1–2 | TASK 1 (DB schema), TASK 2 (Auth), TASK 3 (Layout) |
| Day 3–4 | TASK 5 (Inventory), TASK 8 (Suppliers) |
| Day 5–6 | TASK 6 (Stock In), TASK 9 (Customers) |
| Day 7–8 | TASK 7 (Sales / POS) |
| Day 9–10 | TASK 4 (Dashboard), TASK 10 (Reports) |
| Day 11–13 | TASK 11 (Users), TASK 12 (Dark Mode) |
| Day 14 | Testing, bug fixes, final polish |

---

## Instructions for Code Editor Bot

1. Read this entire document before writing any code.
2. Build one TASK at a time. Complete and test each task before moving to the next.
3. Follow the folder structure exactly as defined above.
4. Always use PDO prepared statements — never raw SQL string building.
5. Always include `auth_check.php` at the top of every protected module file.
6. Use Bootstrap 4 classes only — not Bootstrap 5.
7. Use Google Material Icons with `<span class="material-icons">icon_name</span>`.
8. Show flash messages (success/error) on every form submission.
9. All list pages must use DataTables for sorting/pagination/search.
10. Start with TASK 1 (database) and TASK 2 (auth) — nothing else works without these.
11. TASK 12 (Dark Mode) must be done AFTER TASK 3 (Layout) is complete — it depends on the sidebar, navbar, and header structure being finalized.
12. Dark mode must work on ALL pages including the login page.

---

*Generated for: OptiStock Inventory Management System*
*Stack: PHP + MySQL + Bootstrap 4 + jQuery (no frameworks)*
*Total modules: 12 | Estimated build time: 14 days*
