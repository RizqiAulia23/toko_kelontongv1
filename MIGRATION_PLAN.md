# MIGRATION_PLAN.md - VLONIX Native PHP → Laravel 12 Migration

**Status**: AUDIT COMPLETE  
**Date**: 2026-09-13  
**Target**: Laravel 12 + PostgreSQL (toko_kelontong)

---

## DATABASE SCHEMA CONFLICT ANALYSIS

### Current PostgreSQL State (toko_kelontong)
Existing tables:
- `pelanggan` (id_pelanggan, nama_pelanggan, no_hp, alamat, poin) - Customer/member table
- `produk` (id_produk, nama_produk, kategori VARCHAR, harga, stok) - Product table (flat, no FK to category)
- `transaksi` (id_transaksi, id_pelanggan, id_produk, jumlah, total_harga, tanggal_transaksi, metode_pembayaran) - Transaction table
- Laravel framework tables: users, sessions, migrations, cache*, jobs, etc.

**Note**: These are NOT the same as native PHP schema. Different naming, different structure.

### Native PHP Schema (database.sql - vlonix_db intended)
Tables to migrate:
- `tb_admin` (admin_id, admin_name, username, password, admin_telp, admin_email, admin_address, level) - Admin authentication
- `tb_category` (category_id, category_name) - Product categories
- `tb_product` (product_id, category_id FK, product_name, product_price, product_description, product_image, product_status, date_created) - Products with category relation
- `tb_message` (message_id, name, email, message, date_sent) - Contact form messages

### CRITICAL CONFLICT
- ✗ `pelanggan` ≠ `tb_admin` (semantically different: customer loyalty vs admin auth)
- ✗ `produk` ≠ `tb_product` (schema incompatible: flat kategori vs FK category_id; no images in produk)
- ✗ `transaksi` ≠ `tb_message` (completely different: transactions vs messages)

**DECISION**: Do NOT map or merge. Create separate tb_* tables alongside existing pelanggan/produk/transaksi.
- Keep existing pelanggan/produk/transaksi untouched (potential future customer/POS system).
- Add tb_admin, tb_category, tb_product, tb_message for admin VLONIX retail cockpit.
- Same database, separate schema namespaces.

---

## NATIVE PHP FEATURE INVENTORY

### Authentication (`includes/auth.php`, `login.php`, `admin/logout.php`)
- Session-based auth: `$_SESSION['admin_logged_in']`, `$_SESSION['admin_id']`, etc.
- Login: query `tb_admin`, `password_verify()` against stored hash
- Check level='admin' for access
- Logout: destroy session, clear cookies
- CSRF: custom token in `$_SESSION['vlonix_csrf']`

### Database & Helpers (`includes/db.php`)
- PDO connection to PostgreSQL
- `h()` - htmlspecialchars escape
- `format_rupiah()` - IDR formatter
- `csrf_token()` / `verify_csrf()` - CSRF protection
- `set_flash()` / `get_flash()` - Flash messaging
- `url()` / `app_base()` - URL routing helpers

### File Upload (`includes/upload.php`)
- `safe_upload()` - MIME validation, size limit (5MB), unique filename, reencode image
- `reencode_image()` - GD re-encoding to prevent RCE
- `delete_product_image()` - Cleanup old images
- Allowed types: jpg, jpeg, png, webp, gif

### Dashboard (`admin/dashboard.php`)
- KPI: total products, total categories, total messages, active products
- Recent 5 messages (ORDER BY date_sent DESC)
- Recent 4 products (ORDER BY date_created DESC)
- Product search/filter on storefront (`index.php`)

### Products (`admin/products.php`, `admin/product-add.php`, `admin/product-edit.php`)
- List with search (LOWER(product_name) LIKE ?), category filter, status filter
- Create: validate name, category_id, price, description, image (optional), status
- Edit: update same fields, handle image replacement (delete old if new uploaded)
- Delete: remove product, delete associated image
- Toggle status: quick active/inactive switch via POST
- Category relation via FK

### Categories (`admin/categories.php`, `admin/category-add.php`, `admin/category-edit.php`)
- List with product count
- Create: unique category_name check, length 2-100 chars
- Edit: update name
- Delete: check for linked products, prevent delete if FK constraint violated

### Messages (`admin/messages.php`)
- List all messages, newest first
- Delete message
- Modal view for full message detail
- Display sender name, email, date

### Profile (`admin/profile.php`)
- Edit: admin_name, username, admin_telp, admin_email, admin_address
- Password optional rotation (min 6 chars)
- Sensitive changes (username/email/password) require current password verification
- Unique username check (excluding self)
- Session sync after update

### Storefront (`index.php`)
- Public product catalog (only product_status=1)
- Search by product name
- Filter by category
- Contact form → tb_message (10-second throttle, email validation, min 5 char message)
- Display totals: live SKUs, categories, 3 featured products

### UI (`assets/css/style.css`, `assets/js/app.js`)
- VLONIX design system: #0B0F17 canvas, glass surfaces, gradient badges
- Sidebar nav, topbar, KPI cards, data tables
- Modals, forms, alerts
- Responsive layout
- Font: Plus Jakarta Sans

---

## LARAVEL IMPLEMENTATION PLAN

### PHASE A: Core Infrastructure
1. ✓ Audit complete
2. Create migrations for tb_admin, tb_category, tb_product, tb_message
3. Create models: Admin, Category, Product, Message
4. Configure custom User model for admin auth (or separate Admin model)

### PHASE B: Authentication
1. Create AdminController (login/logout)
2. Create AuthMiddleware (admin-only routes)
3. Migrate auth logic from includes/auth.php
4. Blade login form

### PHASE C: Layout & UI
1. Copy assets/css/style.css → public/css/
2. Copy assets/js/app.js → public/js/
3. Create Blade layout: resources/views/layouts/admin.blade.php
4. Create Blade components for header, sidebar, navbar

### PHASE D: Core CRUD
1. ProductController: index, create, store, edit, update, destroy
2. CategoryController: index, create, store, edit, update, destroy
3. MessageController: index, destroy
4. ProfileController: show, update
5. DashboardController: index

### PHASE E: Views
1. Dashboard blade
2. Products index, create, edit
3. Categories index, create, edit
4. Messages index + modal
5. Profile edit

### PHASE F: Upload & Storage
1. Create storage:link
2. ProductImageService (upload, replace, delete using Laravel Storage)
3. Validation on file type/size

### PHASE G: Validation & Form Requests
1. StoreProductRequest
2. UpdateProductRequest
3. StoreCategoryRequest
4. UpdateCategoryRequest

### PHASE H: Routes & Middleware
1. Route group /admin with auth:admin middleware
2. Named routes
3. Redirect on auth failure

### PHASE I: Testing
1. Feature tests for auth
2. Feature tests for product CRUD
3. Feature tests for category CRUD
4. Feature tests for message delete
5. Feature tests for upload validation

---

## MIGRATION STRATEGY

### DO NOT:
- ❌ migrate:fresh (will destroy pelanggan/produk/transaksi)
- ❌ Drop existing tables
- ❌ Modify pelanggan/produk/transaksi schema

### DO:
- ✅ Create NEW migrations that ADD tb_admin, tb_category, tb_product, tb_message
- ✅ Run: `php artisan migrate` (will only run new migrations)
- ✅ Verify: `php artisan migrate:status`

### Migration Files to Create:
- `database/migrations/YYYY_MM_DD_create_tb_admin_table.php`
- `database/migrations/YYYY_MM_DD_create_tb_category_table.php`
- `database/migrations/YYYY_MM_DD_create_tb_product_table.php`
- `database/migrations/YYYY_MM_DD_create_tb_message_table.php`

---

## MODEL MAPPING

| Native PHP | Laravel Model | Table | Primary Key |
|-----------|---------------|-------|-------------|
| tb_admin | Admin | tb_admin | admin_id |
| tb_category | Category | tb_category | category_id |
| tb_product | Product | tb_product | product_id |
| tb_message | Message | tb_message | message_id |

Models must explicitly set:
- `protected $table = 'tb_*'`
- `protected $primaryKey = 'xyz_id'`
- `public $incrementing = true`
- `protected $keyType = 'int'`

---

## AUTHENTICATION MODEL DECISION

Option 1: Extend Laravel's `User` model to map to `tb_admin`
- Pros: Uses Laravel's built-in auth
- Cons: Naming mismatch, complicates future pelanggan system

Option 2: Create separate `Admin` model + custom auth guard
- Pros: Clean separation, future-proof for multi-role systems
- Cons: More code, custom guard setup needed

**CHOSEN**: Option 2 - Separate Admin model + custom auth.

---

## FILES TO CREATE IN LARAVEL

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── AuthController.php
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── MessageController.php
│   │   │   └── ProfileController.php
│   └── Requests/
│       ├── StoreProductRequest.php
│       ├── UpdateProductRequest.php
│       ├── StoreCategoryRequest.php
│       └── UpdateCategoryRequest.php
├── Models/
│   ├── Admin.php
│   ├── Category.php
│   ├── Product.php
│   └── Message.php
└── Services/
    └── ProductImageService.php

resources/views/
├── layouts/
│   ├── admin.blade.php
│   ├── header.blade.php
│   ├── sidebar.blade.php
│   └── navbar.blade.php
├── auth/
│   └── login.blade.php
├── dashboard.blade.php
├── products/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── categories/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── messages/
│   └── index.blade.php
└── profile/
    └── edit.blade.php

database/
└── migrations/
    ├── YYYY_create_tb_admin_table.php
    ├── YYYY_create_tb_category_table.php
    ├── YYYY_create_tb_product_table.php
    └── YYYY_create_tb_message_table.php

routes/
├── web.php (admin routes)
└── auth.php (login/logout)

public/
├── css/
│   └── style.css (copied)
├── js/
│   └── app.js (copied)
└── storage/products/ (symlink to storage/app/public/products)
```

---

## NEXT STEPS (Phase A → B)

1. Create migrations for tb_* tables (non-destructive, additive only)
2. Create Admin, Category, Product, Message models
3. Configure custom auth guard for Admin
4. Copy assets (css, js) to public/
5. Create Blade layouts
6. Implement AuthController

---

## NOTES

- All passwords hashed with bcrypt (password_hash, Hash::make, password_verify)
- CSRF tokens handled by Laravel middleware
- Flash messages via session (Laravel built-in)
- Image storage: storage/app/public/products via Laravel Storage
- Database queries via Eloquent + Query Builder (no raw PDO)
- Validation via Form Requests or Validator
- No auth guard needed if using standard Laravel User + sessions
- Keep storefront (index.php equivalent) separate or as Laravel route

---

