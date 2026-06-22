# POS Passkey Implementation Report

## Executive Summary

This report documents the completion of the **waiter → order → cashier** workflow using 4-digit passkey login on the `kijana` branch.

Before this change, the passkey login existed but the POS dashboard was only a placeholder — waiters could log in but had no way to place orders, and cashiers had no screen to view/clear waiter orders. This implementation closes that gap.

---

## 1. What Was Already Implemented

### Barcode logic (beverages)
- `Beverage` model has a unique `barcode` field.
- `BarcodeScanController` resolves any scanned barcode to a beverage.
- `StockReceivingController` receives stock by scanning barcodes.
- `StockTakeController` performs stock takes by scanning barcodes and calculating variances.
- All scans record the `barcode_scanned` value for audit.

### Passkey foundation
- `User` model: `passkey`, `passkey_enabled`, `login_type`, `property_code`, `failed_passkey_attempts`, `passkey_locked_until`.
- `PasskeyAuthService`: verifies PIN, counts failures, locks after 5 attempts for 15 minutes.
- `StaffPasskeyLoginController`: 4-digit PIN pad login for `waiter`, `cashier`, `bar_tender`.
- `CheckStaffSession` middleware protects POS routes.
- Management login still uses email + password + property code.

---

## 2. What Was Added / Fixed

### 2.1 Admin user management

Files changed:
- `app/Http/Controllers/UserController.php`
- `resources/views/users/create.blade.php`
- `resources/views/users/edit.blade.php`
- `resources/views/users/index.blade.php`

What changed:
- Admin can now choose **Login Type** when creating/editing a user:
  - `full` — management login only
  - `both` — management login + passkey
  - `staff` — passkey only
- Admin can now set **Property Code** directly on the user form.
- Admin can now set a **4-digit passkey PIN** directly when creating a user (or reset it when editing).
- A **Passkey** link was added to the users list for quick passkey management.

### 2.2 Waiter order entry

New files:
- `app/Http/Controllers/Pos/PosOrderController.php`
- `resources/views/pos/orders/create.blade.php`

What it does:
- Waiters logged in with their 4-digit passkey are redirected to `/pos/orders/create`.
- They can select **Walk-in** or **Guest (charge to room)**.
- They tap menu items to add them to a cart.
- They can adjust quantities or remove items.
- On submit, an `Order` is created with:
  - `order_source = 'pos_waiter'`
  - `created_by = waiter user id`
  - `status = 'sent'` (sent to kitchen automatically)
- The order total is calculated and kitchen tickets are dispatched immediately.

### 2.3 Cashier settlement

New files:
- `app/Http/Controllers/Pos/PosCashierController.php`
- `resources/views/pos/cashier/orders.blade.php`

What it does:
- Cashiers logged in with their 4-digit passkey are redirected to `/pos/cashier/orders`.
- They see **all open orders placed by waiters** via passkey login.
- Each order card shows:
  - Order number, status, waiter name, customer name
  - Items and total
- Cashier can:
  - **Settle** with cash, mobile money, or card
  - **Charge to room** (guest folio)
  - **Cancel** the order
- On settlement, stock is deducted, accounting entries are posted, and a receipt is generated.

### 2.4 POS dashboard

Changed file:
- `resources/views/pos/dashboard.blade.php`

What changed:
- Dashboard now shows role-specific action cards:
  - Waiter / Bar tender: **New Order**
  - Cashier: **Open Orders**
  - All staff: **Switch Staff** (logout to let another person log in)

### 2.5 Login redirect

Changed file:
- `app/Http/Controllers/Auth/StaffPasskeyLoginController.php`

What changed:
- After passkey login:
  - Cashiers go straight to `/pos/cashier/orders`
  - Waiters / Bar tenders go straight to `/pos/orders/create`

### 2.6 Routes

Changed file:
- `routes/web.php`

Added routes under `staff.session` middleware:
```
GET    /pos/orders/create          -> PosOrderController@create
POST   /pos/orders                 -> PosOrderController@store
GET    /pos/cashier/orders         -> PosCashierController@index
POST   /pos/cashier/orders/{order}/settle -> PosCashierController@settle
POST   /pos/cashier/orders/{order}/cancel -> PosCashierController@cancel
```

---

## 3. How to Use It

### Step 1: Create a waiter user
1. Go to **Users → Add User**.
2. Fill name, email, phone, password.
3. Role = `waiter`.
4. Login Type = `staff` (or `both` if they also need management login).
5. Enter a 4-digit PIN in the **Passkey** fields.
6. Save.

### Step 2: Create a cashier user
1. Repeat the above but Role = `cashier`.
2. Login Type = `staff` or `both`.
3. Set a 4-digit PIN.

### Step 3: Waiter logs in
1. Open `/staff/login`.
2. Select their name.
3. Enter 4-digit PIN.
4. They land on **New Order**.
5. Tap items, choose Walk-in or Guest, then click **Place Order**.

### Step 4: Cashier clears the sale
1. Open `/staff/login` on the cashier terminal.
2. Select cashier name and enter PIN.
3. They land on **Open Orders**.
4. Find the waiter order, click **Settle / Charge**.
5. Choose payment method and confirm.

---

## 4. Security Notes

- Passkey still locks after **5 failed attempts** for **15 minutes**.
- Staff session expires after **8 hours** (configurable in `config/hms_auth.php`).
- POS routes are protected by `staff.session` middleware.
- PIN is hashed with `bcrypt` before storage.
- Passkey reset fields on user forms use `type="password"` so the PIN is hidden while typing.

---

## 5. Files Added / Modified

### New files
- `app/Http/Controllers/Pos/PosOrderController.php`
- `app/Http/Controllers/Pos/PosCashierController.php`
- `resources/views/pos/orders/create.blade.php`
- `resources/views/pos/cashier/orders.blade.php`

### Modified files
- `app/Http/Controllers/UserController.php`
- `app/Http/Controllers/Auth/StaffPasskeyLoginController.php`
- `resources/views/pos/dashboard.blade.php`
- `resources/views/users/create.blade.php`
- `resources/views/users/edit.blade.php`
- `resources/views/users/index.blade.php`
- `routes/web.php`

---

## 6. Known Limitations / Next Steps

- The waiter POS order entry is simplified: it does not yet support menu item **options/varieties** or **table selection**. If you need those, the existing `/restaurant/pos` screen can be adapted for staff session use.
- Barcode logic is currently for **beverages only**. If you want barcode receiving/stock-taking for general store products, extend the same pattern from `Product`.
- Passkey login currently lists `waiter`, `cashier`, `bar_tender`. If other roles (e.g. `restaurant_manager`) need passkey access, update `StaffPasskeyLoginController::showLoginForm()`.

---

## 7. Verification

Run these commands to confirm everything is wired correctly:

```bash
# Check POS routes are registered
php artisan route:list --path=pos

# Check code style
vendor/bin/pint --test app/Http/Controllers/Pos app/Http/Controllers/UserController.php app/Http/Controllers/Auth/StaffPasskeyLoginController.php

# Run basic tests
php artisan test tests/Feature/ExampleTest.php tests/Unit/ExampleTest.php
```

---

*Report generated on 2026-06-14 for branch `kijana`.*
