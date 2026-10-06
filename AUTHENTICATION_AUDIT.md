# AUTHENTICATION_AUDIT.md

**Date**: 2026-09-17
**Phase**: 5A - Authentication Audit (READ-ONLY)

## Current Laravel Authentication Architecture

### 1. Admin Model (tb_admin table)

**Table Schema:**
| Column | Type | Nullable | Notes |
|--------|------|----------|-------|
| admin_id | bigint | NO | Primary key |
| admin_name | varchar | NO | Display name |
| username | varchar | NO | Login identifier |
| password | varchar | NO | Bcrypt hash |
| admin_telp | varchar | NO | Phone number |
| admin_email | varchar | NO | Email address |
| admin_address | text | NO | Address |
| level | varchar | NO | Authorization level (fixed: "admin") |

**Model Location:** `app/Models/Admin.php`
**Base Class:** `Authenticatable` (Laravel's built-in auth trait)
**Timestamps:** Disabled (`public $timestamps = false;`)
**Hidden Fields:** `password`
**Casts:** `'password' => 'hashed'` (automatically hashed on save)
**Authorization Method:** `isAdmin()` checks `$this->level === 'admin'`

**Default Seeded Admin:**
- username: `admin`
- level: `admin`
- name: `Vlonix Lead Admin`
- password: bcrypt hash of `admin123` (via `Hash::make()`)

### 2. Login Flow

**Route:**
- `GET /login` → `AuthController::showLoginForm()` (displays login form)
- `POST /login` → `AuthController::login()` (processes login)

**Controller: AuthController::login()**

Input Validation:
```php
$credentials = $request->validate([
    'username' => ['required', 'string', 'max:50'],
    'password' => ['required', 'string', 'min:6'],
]);
```

Authentication Attempt:
```php
Auth::attempt([
    'username' => $credentials['username'],
    'password' => $credentials['password'],
    'level' => 'admin',  // CRITICAL: must be admin
], $request->filled('remember'));
```

**Key Points:**
- Username + password + level check (all 3 must match)
- Supports "remember me" functionality (persistent session)
- On failure: throws `ValidationException` with message "Invalid username, password, or insufficient privileges."
- On success:
  - Session regenerated: `$request->session()->regenerate()`
  - Redirects to `/admin/dashboard` (or intended route)

**Session Details:**
- Driver: `database` (stored in `sessions` table)
- Lifetime: 120 minutes (default, from `config/session.php`)
- Session encryption: disabled
- CSRF protection: enabled

### 3. Logout Flow

**Route:**
- `POST /logout` → `AuthController::logout()`

**Actions Performed:**
```php
Auth::logout();                           // Clear auth state
$request->session()->invalidate();        // Invalidate session
$request->session()->regenerateToken();   // Regenerate CSRF token
return redirect('/login');                // Redirect to login
```

**Key Points:**
- Invalidates entire session (deletes from database)
- Regenerates CSRF token (prevents token reuse attacks)
- User must be redirected to `/login`
- No grace period or delayed invalidation

### 4. Authorization Middleware (EnsureAdmin)

**File:** `app/Http/Middleware/EnsureAdmin.php`
**Registered as:** `'admin'` alias in `bootstrap/app.php`

**Check Logic:**
```php
if (!Auth::check() || Auth::user()->level !== 'admin') {
    // User not authenticated OR level != 'admin'
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login')->withErrors([
        'username' => 'Authentication required. Please login to access VLONIX Admin.'
    ]);
}
return $next($request);
```

**Key Points:**
- Runs on every protected route
- Checks both authentication state AND admin level
- On unauthorized access:
  - Logs out user
  - Invalidates session
  - Regenerates CSRF token
  - Redirects to login with error message
  - Prevents privilege escalation

### 5. Protected Routes

**Middleware Group:**
```php
Route::middleware('admin')->group(function () {
    // All admin routes inside this group
});
```

**Protected Routes:**
- `GET /admin/dashboard` → Dashboard (public for now, but middleware-protected)
- `GET /admin/products` → List products
- `GET /admin/products/create` → Create form
- `POST /admin/products` → Store product
- `GET /admin/products/{product}/edit` → Edit form
- `PATCH /admin/products/{product}` → Update product
- `DELETE /admin/products/{product}` → Delete product
- `PATCH /admin/products/{product}/toggle-status` → Toggle status
- `GET /admin/categories` → List categories
- `GET /admin/categories/create` → Create form
- `POST /admin/categories` → Store category
- `GET /admin/categories/{category}/edit` → Edit form
- `PATCH /admin/categories/{category}` → Update category
- `DELETE /admin/categories/{category}` → Delete category

**Public Routes (no auth required):**
- `GET /` → Redirects to `/login`
- `GET /login` → Show login form
- `POST /login` → Process login
- `POST /logout` → Process logout

### 6. Password Hashing & Verification

**Algorithm:** Bcrypt (Blowfish cipher, `$2y$` variant)
**Hash Format:** `$2y$cost$salt$hash` (60 characters)
**Cost:** 10 (default Laravel setting)
**Verification Method:** PHP `password_verify()` or Node.js `bcrypt` package

**Example Hash (not real):**
```
$2y$10$N9qo8uLOickgx2ZMRZoMye...  (60 chars total)
```

**Verification (Laravel):**
```php
Hash::check('plaintext_password', $admin->password);  // returns boolean
```

**Verification (Node.js):**
```javascript
const bcrypt = require('bcrypt');
const match = await bcrypt.compare('plaintext_password', dbHash);
```

### 7. Authentication Configuration

**Guard Configuration (config/auth.php):**
- Guard: `'web'` (session-based)
- Driver: `'session'`
- Provider: `'admins'` (Eloquent ORM)
- Model: `Admin::class`

**Session Configuration (config/session.php):**
- Driver: `'database'` (sessions stored in PostgreSQL)
- Table: `sessions`
- Lifetime: `120` minutes (idle timeout)
- Expire on browser close: `false`
- Encryption: `false`

**Password Reset Configuration:**
- Broker: `'admins'`
- Token table: `password_reset_tokens`
- Expiry: 60 minutes
- Throttle: 60 seconds between requests

### 8. CSRF Protection

**Enabled:** Yes
**Token storage:** Session (via session middleware)
**Token regeneration:** On logout and after authorization changes
**Expected in forms:** Hidden input `_token` field
**Expected in AJAX:** `X-CSRF-TOKEN` header

### 9. Laravel Test Coverage

**File:** `tests/Feature/AuthenticationTest.php`
**Tests:**
- User can access login form
- User can login with valid credentials
- User cannot login with invalid credentials
- User cannot login without level='admin'
- User can logout
- Session is invalidated on logout
- Unauthenticated users redirect to login
- Non-admin users cannot access admin routes

**Result:** All authentication tests pass (included in 41 passed tests)

## Next.js Authentication Requirements

### Required Behavior (Must Implement)

1. **Login Page** (`/login`)
   - Form with username and password fields
   - Display validation errors
   - Submit to login endpoint
   - Redirect to dashboard on success
   - Show "insufficient privileges" if level != 'admin'

2. **Login Endpoint** (Server Action or API Route)
   - Accept username and password
   - Query `tb_admin` table for matching username
   - Verify password using bcrypt.compare()
   - Verify level === 'admin'
   - Create session/JWT/cookie with authenticated state
   - Return redirect to dashboard or error

3. **Session Management**
   - Session must persist across requests
   - Session must include admin_id and level
   - Session must have timeout (120 minutes)
   - Session invalidated on logout

4. **Logout Endpoint** (Server Action or API Route)
   - Clear session/JWT/cookie
   - Clear any client-side auth state
   - Redirect to login

5. **Protected Routes** (`/admin/*`)
   - Check if user is authenticated
   - Check if user level === 'admin'
   - Redirect to login if not authorized
   - Preserve intended URL for post-login redirect

6. **Middleware/Guards**
   - Apply to all `/admin/*` routes
   - Block access if not authenticated
   - Block access if level != 'admin'
   - Clear session on unauthorized attempt

7. **Error Handling**
   - Invalid credentials → show generic message (don't reveal username validity)
   - Insufficient privileges → show specific message
   - Session expired → redirect to login
   - Database errors → generic error page

### Password Compatibility

**bcrypt in Node.js:**
- Use `bcryptjs` package (pure JavaScript) or `bcrypt` package (native)
- Must verify against existing `tb_admin.password` hashes
- Same algorithm ($2y$): fully compatible
- Example verification:
  ```javascript
  const bcrypt = require('bcryptjs');
  const match = await bcrypt.compare(plainPassword, dbHash);
  ```

### Database Access for Auth

**Read Operations:**
- Query `tb_admin` by username: `SELECT * FROM tb_admin WHERE username = $1 LIMIT 1`
- Only read username, password, level, admin_id
- No writes to tb_admin (deferred to later phase)

**Server-Side Only:**
- Do NOT query PostgreSQL from client-side code
- Do NOT expose DATABASE_URL to browser
- Do NOT send plaintext passwords over the network

### Session Storage Options for Next.js

**Option A: Database Sessions**
- Store sessions in PostgreSQL (`sessions` table or custom table)
- Server retrieves session on each request
- Similar to Laravel's approach
- Pros: Persistent, centralized
- Cons: Extra query per request

**Option B: JWT (JSON Web Token)**
- Stateless session tokens
- Signed with server secret
- Client stores in HTTP-only cookie
- No database lookup needed per request
- Pros: Stateless, scalable
- Cons: Can't revoke immediately

**Option C: Session Cookie (encrypted)**
- Encrypted session data in cookie
- Similar to Laravel sessions
- Can't modify without server key
- Pros: Simple, no storage
- Cons: Cookie size limits

### Security Considerations

1. **Password Hashing**
   - Must use bcrypt for verification (compatible with existing hashes)
   - Do NOT re-hash passwords in Next.js (accept hashes as-is)

2. **Session Invalidation**
   - On logout: delete session entirely
   - On level change: invalidate existing sessions
   - On password change: re-authenticate

3. **CSRF Protection**
   - Ensure POST /logout uses CSRF token
   - Verify token on login (if using traditional form)
   - Use SameSite=Strict for cookies

4. **Rate Limiting**
   - Limit login attempts (prevent brute force)
   - Recommend: 5 attempts per 15 minutes per IP

5. **Audit Logging**
   - Log login attempts (success/failure)
   - Log logout events
   - Log unauthorized access attempts

6. **Redirect Safety**
   - Only allow redirects to internal routes
   - Prevent open redirect vulnerabilities
   - Validate intended URL before redirect

## Implementation Architecture (Proposed, NOT IMPLEMENTED YET)

### File Structure (Target)
```
next-app/
├── app/
│   ├── login/
│   │   ├── page.tsx              # Login form (public)
│   │   └── actions.ts            # Login server action
│   ├── api/
│   │   ├── auth/
│   │   │   ├── login/route.ts    # Login endpoint (alternative)
│   │   │   └── logout/route.ts   # Logout endpoint (alternative)
│   │   └── session/route.ts      # Verify session endpoint
│   └── admin/
│       └── [protected routes]     # All require auth
├── lib/
│   ├── auth.ts                   # Auth utilities & session functions
│   ├── db.ts                     # Database layer (existing)
│   └── storefront.ts             # Storefront layer (existing)
├── middleware.ts                 # Next.js middleware for auth checks
└── types/
    └── auth.ts                   # TypeScript interfaces for auth
```

### Key Functions (NOT IMPLEMENTED)

**`authenticateAdmin(username, password)`**
- Query `tb_admin` by username
- Verify password with bcrypt
- Return admin object if valid

**`createSession(adminId, level)`**
- Create session object with user data
- Store in database or JWT
- Return session token/cookie

**`getSession(sessionToken)`**
- Retrieve session data
- Check if still valid/not expired
- Return admin object or null

**`invalidateSession(sessionToken)`**
- Delete/revoke session
- Clear any cached data

**`verifyAdmin(session)`**
- Check if session exists and is valid
- Check if level === 'admin'
- Return authenticated admin or null

## Open Questions & Decisions Pending

1. **Session Storage:** Database vs JWT vs encrypted cookie?
2. **Authentication Library:** Manual bcrypt + custom session vs Auth.js vs other?
3. **Cookie vs Header:** How to send session token?
4. **Session Timeout:** Keep 120 minutes or adjust?
5. **Remember Me:** Implement persistent login option?
6. **Email Verification:** Add to login flow?
7. **Account Lockout:** Lock after N failed attempts?
8. **Password Requirements:** Enforce complexity rules?
9. **Audit Logging:** What events to log?
10. **Two-Factor Auth:** Add TOTP or email 2FA?

## Database Safety Confirmation

- ✅ No INSERT executed on `tb_admin`
- ✅ No UPDATE executed on `tb_admin`
- ✅ No DELETE executed on `tb_admin`
- ✅ No ALTER executed on `tb_admin`
- ✅ No migrations executed
- ✅ `tb_admin` schema unchanged
- ✅ PostgreSQL untouched

## Laravel Code Status

- ✅ `app/Models/Admin.php` untouched
- ✅ `app/Http/Controllers/Auth/AuthController.php` untouched
- ✅ `app/Http/Middleware/EnsureAdmin.php` untouched
- ✅ `config/auth.php` untouched
- ✅ `routes/web.php` untouched
- ✅ All Laravel auth code preserved

## Files Created/Modified in This Phase

- ✅ Created: `AUTHENTICATION_AUDIT.md` (this file)
- No code changes (audit-only)
- No database changes
- No Laravel modifications

---

**Audit Completed**: 2026-09-17 15:15:45 UTC+07:00
**Status**: READ-ONLY inspection complete. Ready for implementation decisions.
**Next Step**: Phase 5B (Implementation) — pending selection of authentication approach