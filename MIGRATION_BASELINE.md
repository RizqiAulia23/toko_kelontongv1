# MIGRATION_BASELINE.md

**Date**: 2026-09-17
**Phase**: 0 - Safe Baseline

## Git baseline
- Current branch: N/A (not a git repository)
- Current commit SHA: N/A
- Working tree status: N/A
- Note: Project is not under version control. No git operations possible.

## Laravel verification
- Laravel version: 12.69.2
- PHP version: 8.3.16
- Dependencies installed: yes (vendor/autoload.php present)
- Routes count: 16 routes defined
- Test suite: phpunit.xml present, tests/ directory exists

## Database protection
- Database engine: PostgreSQL (DB_CONNECTION=pgsql)
- Protected tables status:
  - pelanggan: exists
  - produk: exists
  - transaksi: exists
  - tb_admin: exists
  - tb_category: exists
  - tb_product: exists
  - tb_message: exists
  - users: exists
  - sessions: exists
  - migrations: exists
  - password_reset_tokens: exists
  - cache: exists
  - cache_locks: exists
  - jobs: exists
  - job_batches: exists
  - failed_jobs: exists

## Current features (from audit)
- Authentication: Admin login/logout with session
- Dashboard: Placeholder
- Product CRUD: With image upload/toggle
- Category CRUD: With delete protection
- Public storefront: Filterable product catalog
- Legacy tables: pelanggan, produk, transaksi (read-only)
- File storage: Local (storage/app/public/products)

## Asset status
- CSS: public/css/style.css (20392 bytes)
- JS: public/js/app.js (3095 bytes)
- Image storage: 0 files in storage/app/public/products/

## Safety confirmation
- [x] Laravel application unchanged
- [x] No modifications to routes, controllers, models, migrations
- [x] No database alterations
- [x] Legacy tables protected
- [x] .gitignore excludes .env and secrets
- [x] No git repository to modify