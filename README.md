# TWA Reports

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Version">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Framework">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5.3">
  <img src="https://img.shields.io/badge/DataTables-2.x-005E99?style=for-the-badge&logo=datatable&logoColor=white" alt="DataTables">
  <img src="https://img.shields.io/badge/Vite-8.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/Tests-Passing%20(73%2F73)-10B981?style=for-the-badge&logo=phpunit&logoColor=white" alt="Test Status">
  <img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" alt="License">
</p>

---

## 📌 Overview

**TWA Reports** is an enterprise-grade reporting, business intelligence, and access management system built with **Laravel 12**, **PHP 8.3**, and the **BizLite** responsive design system.

The platform provides business controllers, accountants, auditors, and sales managers with real-time visibility across operational transactions, financial health metrics, and ledger integrity.

---

## ✨ Key Features

### 1. 📊 Financial Intelligence Suite (5 Core Reports)
Equipped with executive KPI summary cards, interactive report definitions/purpose drawers, and standardized currency & date formatting:

- **Balance Sheet** (`/reports/financial/balance-sheet`):
  - Assets, Liabilities, and Shareholders' Equity analysis.
  - Working Capital, Current Ratio, Debt-to-Equity, and Solvency metrics.
  - Interactive audit inspection modal with verified cutoff dates.
- **Profit & Loss Statement** (`/reports/financial/profit-loss`):
  - Revenue, Cost of Goods Sold (COGS), Gross Profit, Operating Expenses, and Net Margin.
  - Drill-down voucher verification and categorized ledger postings.
- **Cash Flow Statement** (`/reports/financial/cash-flow`):
  - Inflows and outflows across Operating, Investing, and Financing activities.
  - Direct cash position tracking and liquidity status indicators.
- **Trial Balance** (`/reports/financial/trial-balance`):
  - Real-time double-entry debit/credit validation and reconciliation status.
  - Multi-account classification with opening/closing balance summaries.
- **General Ledger** (`/reports/financial/general-ledger`):
  - Immutable, chronological audit trail linking high-level figures to source documents.
  - Journal Voucher (JV), AR invoice, and AP bill traceability.

### 2. 🛒 Operational Reports (7 Modules)
High-performance server-side DataTables with responsive layouts, custom status badges, and data export (Excel, CSV, Print):

- **Purchase Reports**:
  - Purchase Orders (`/reports/purchase/purchase-orders`)
  - AP Invoices (`/reports/purchase/ap-invoices`)
  - Outgoing Payments (`/reports/purchase/outgoing-payments`)
- **Sales Reports**:
  - Sales Orders (`/reports/sales/purchase-orders`)
  - AR Invoices (`/reports/sales/ar-invoices`)
  - Incoming Payments (`/reports/sales/incoming-payments`)
  - Customer Deposits (`/reports/sales/deposits`)

### 3. 📅 Advanced Filtering & Preset Date Selector
- **Preset Options**: Quick filter dropdown right beside date inputs with instant presets:
  - `Today`, `This week`, `This month`, `Last month`, `Last 3 months`, and `Custom Range`.
- **Status & Dimension Filters**: Filter by Customer, Vendor, Branch, Payment Method, and Document Status.
- **Collapsible Control Panels**: Dedicated Filter and Definition toggles in the page header with instant state management.

### 4. 🛡️ Role-Based Access Control (RBAC)
- **User Management** (`/users`): Account creation, role assignments, profile edits, and instant active/inactive toggle.
- **Role Management** (`/roles`): Create customized role hierarchies with assigned permissions.
- **Permission Management** (`/permissions`): Granular access control powered by **Spatie Laravel-Permission**.
- **Security Middleware**: `active` middleware prevents deactivated accounts from accessing protected routes.

### 5. 🎨 BizLite Design System & Theme Customization
- **Theme Personalization Drawer**:
  - **Color Schemes**: Light and Dark mode with instantaneous `<head>` restoration (no FOUC).
  - **Navigation Layouts**:
    - *Vertical Navigation Rail*: Collapsible fixed 250px sidebar with popovers.
    - *Horizontal Header Navigation*: Modern mega-dropdowns accommodating 3-column operational and financial categories.
  - **Curated Color Palettes**: Blue (`#2563EB`), Sky (`#0EA5E9`), Purple (`#7C3AED`), Emerald (`#059669`), Royal (`#1D4ED8`), Orange (`#EA580C`), Cyan (`#06B6D4`), Mint (`#10B981`), Coral (`#F43F5E`), or arbitrary custom 6-digit hex.
  - **Typography Selector**: Inter, Roboto, Poppins, Public Sans, and Noto Sans Khmer.
  - **Control Radius**: Configurable corner rounding from sharp (0px) to rounded (12px).
- **Internationalization (i18n)**:
  - English and Khmer (`km`) support.
  - Automatic locale persistence via session and `twa_locale` cookie.
- **UI Libraries**: Lucide icons, Select2 with Bootstrap 5 theme, Flatpickr datepicker, and SweetAlert2 notifications.

---

## 🛠️ Technology Stack

| Layer | Technologies |
| :--- | :--- |
| **Backend Framework** | Laravel 12 (PHP 8.3+) |
| **Database** | MySQL / MariaDB / SQLite (for automated testing) |
| **Frontend Architecture** | Blade Components, Bootstrap 5.3, Vite 8, Alpine.js, jQuery 3.7 |
| **Data Tables** | DataTables 2.x (`datatables.net-bs5`), Yajra Laravel DataTables |
| **UI Components** | Flatpickr, Select2 (`select2-bootstrap-5-theme`), SweetAlert2, Lucide Icons |
| **Testing & Quality** | PHPUnit 11, Laravel Pint code formatter |

---

## 🚀 Quick Start Guide

### Prerequisites
- **PHP**: `^8.3` (with `pdo`, `mbstring`, `openssl`, `curl`, `xml`, `bcmath`)
- **Composer**: `^2.x`
- **Node.js**: `^18.x` or `^20.x` & **NPM**
- **Database**: MySQL 8.0+ or MariaDB 10.4+

### Installation Steps

1. **Clone the repository**:
   ```bash
   git clone https://github.com/rathakann/twareports.git
   cd twareports
   ```

2. **Install PHP and JavaScript dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Edit `.env` to configure your database connection parameters (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).*

4. **Run Migrations & Database Seeders**:
   ```bash
   php artisan migrate --seed
   ```
   *Seeds default permissions, roles, administrative accounts, operational transactions, and financial records.*

5. **Build Frontend Assets**:
   ```bash
   npm run build
   # For hot-reload local development:
   # npm run dev
   ```

6. **Serve the Application**:
   ```bash
   php artisan serve
   ```
   Visit `http://127.0.0.1:8000` in your web browser.

---

## 🔑 Default Credentials

After seeding the database (`php artisan migrate --seed`), you can log in with:

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@twareports.test` | `password` | Full system access (RBAC, Reports, Settings) |
| **Viewer** | `viewer@twareports.test` | `password` | Read-only access to operational & financial reports |

---

## 🧪 Testing & Code Quality

The repository includes a comprehensive test suite covering authentication, RBAC authorization, DataTables server-side responses, date filters, and navigation layouts:

```bash
# Run all automated tests
php artisan test --compact

# Run financial reports test suite specifically
php artisan test --filter=FinancialReportsTest

# Verify and fix code style using Laravel Pint
vendor/bin/pint --format agent
```

---

## 📁 Project Structure

```text
twareports/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                     # Authentication controllers
│   │   │   ├── Reports/
│   │   │   │   ├── Financial/            # 5 Financial report controllers
│   │   │   │   ├── Purchase/             # 3 Purchase report controllers
│   │   │   │   └── Sales/                # 4 Sales report controllers
│   │   │   ├── UserController.php        # User CRUD & status toggle
│   │   │   ├── RoleController.php        # Role management
│   │   │   └── PermissionController.php  # Permission management
│   │   └── Middleware/
│   │       ├── EnsureUserIsActive.php    # Account active status check
│   │       └── SetLocale.php             # Multi-language locale resolver
│   ├── Models/                           # Eloquent models (Financial, Sales, Purchase)
│   └── Support/                          # Navigation helpers & utilities
├── config/
│   └── navigation.php                    # Data-driven menu hierarchy
├── docs/
│   └── theme/                            # BizLite theme specifications & audits
├── lang/
│   ├── en/app.php                        # English translations
│   └── km/app.php                        # Khmer translations
├── resources/
│   ├── css/                              # Theme tokens, layout, components, responsive rules
│   ├── js/                               # Theme interactions, DataTables, Select2, Flatpickr
│   └── views/
│       ├── components/                   # Reusable Blade component library
│       ├── reports/                      # 12 report blade views
│       ├── users/                        # User management views
│       ├── roles/                        # Role management views
│       └── permissions/                  # Permission management views
├── routes/
│   └── web.php                           # Application routes
└── tests/
    └── Feature/                          # Feature and integration tests
```

---

## 💡 Strategic Recommendations & Roadmap

Based on the architectural review and the theme gap analysis in [`docs/theme/10-THEME-GAP-ANALYSIS.md`](docs/theme/10-THEME-GAP-ANALYSIS.md), the following enhancements are recommended:

1. **Locale Switching Auto-Reload**:
   - Update the topbar language toggle to trigger a page refresh (`window.location.href = '/locale/' + nextLang`) so all server-rendered Blade translations take effect immediately.
2. **Themed Custom Error Pages**:
   - Provide branded, accessible `403.blade.php`, `404.blade.php`, and `500.blade.php` pages using the BizLite layout.
3. **Multi-Tenant Organization Filtering**:
   - Connect the topbar organization switcher ("NonOil Report" vs. "Oil Report") to a global Eloquent query scope or session filter so reports automatically segment their data by business unit.
4. **Export Queueing for Large Datasets**:
   - For reports exceeding 10,000 records, introduce background job exports (using Laravel Excel or chunked CSV streaming) to preserve server responsiveness.
5. **PDF Report Generator**:
   - Introduce server-side PDF generation (via `barryvdh/laravel-dompdf` or `spatie/browsershot`) for executive financial statement distribution.

---

## 📄 License

This software is open-sourced under the [MIT license](LICENSE).
