Task: Add User Phone Number + Phone-Based Password Reset (SMS) + Admin/User Profile Phone Management

Objective:
Implement a safe and complete phone-based password reset flow using SMS.

This task must:
- Add a phone number field to users via migration WITHOUT wiping users
- Require admin to enter phone number when creating users
- Allow users to update their phone number in their profile
- Enable password reset using phone number
- Send the reset password (temporary password) to the user’s phone via SMS
- Ensure the user can log in again after reset

DO NOT redesign architecture.
DO NOT wipe or recreate the users table.
EXTEND existing authentication and notification/SMS patterns.

---

### 1) Fix “route:list” / Password Reset Route Issues (If Currently Broken)

If the project has broken auth routes due to missing controllers (e.g., missing ForgotPasswordController):
- Restore/implement the missing controller(s) or route handlers
- Ensure `php artisan route:list` works again
- Ensure password reset routes are registered and reachable

Rules:
- Do not remove routes that other parts depend on
- Keep changes minimal: fix missing class references and ensure routes load

---

### 2) Database Migration: Add Phone to Users (NO DATA LOSS)

Create a migration to add a phone column to the users table:

Requirements:
- Add `phone` column as nullable initially to avoid breaking existing records
- Add an index (and unique constraint if business rules require unique phone)
- DO NOT drop or truncate the users table
- DO NOT rebuild users from seeders

Recommended safe approach:
- `phone` nullable
- Add index on phone
- Add uniqueness only if you confirm there are no duplicates and the DB supports it safely

Data safety notes:
- Migration must be additive and reversible
- No destructive operations (no column drops unless explicitly required)

---

### 3) Phone Formatting + Validation

Standardize phone format rules used across:
- User creation
- Profile update
- Password reset

Requirements:
- Validate phone is present where required
- Normalize phone format (E.164 recommended) using existing helper/util if available
- Block obviously invalid numbers

---

### 4) Admin User Creation Must Capture Phone

Update the Admin user creation flow:

Requirements:
- Add input for phone number
- Validate:
  - required
  - proper format
  - uniqueness (if enforced)
- Store normalized phone

Rules:
- Do not break existing user creation fields/logic
- If phone is missing for existing users, allow admin to edit later

---

### 5) User Profile: Phone Update

Add/verify a profile screen section that allows a user to:
- View their current phone
- Update phone

Requirements:
- Validate phone format
- If uniqueness is enforced, block duplicates
- Log changes (audit)

Security:
- If system has verification/OTP framework already, reuse it
- If not, keep it simple but log changes

---

### 6) Phone-Based Password Reset (Core Feature)

Implement a password reset flow using phone number:

User flow:
1) User opens “Forgot password”
2) User enters phone number
3) System verifies the phone exists for an active user
4) System generates a temporary password
5) System updates user password
6) System sends the temporary password to the phone via SMS
7) User logs in with temporary password
8) (Recommended) Force the user to change password on next login

Requirements:
- Must NOT expose whether a phone exists (avoid user enumeration) unless your app already does
- Must be safe against repeated resets (rate limiting)
- Temporary password must be strong (random, sufficiently long)

SMS content requirements:
- Short and clear
- Include user name if available
- Example:
  "Hello {name}, your temporary password is: {temp}. Please login and change it immediately."

---

### 7) SMS Sending (Reuse Existing Provider)

Reuse the existing SMS service/provider already used in the system.

Rules:
- Do not introduce a new SMS provider
- Wrap sending in try/catch
- If SMS fails:
  - do not crash the whole request
  - log the failure
  - return a safe message to user

---

### 8) Audit Trail + Logging

Record:
- password reset requested at
- password reset completed at
- reset actor (self-service)
- phone used

Log:
- SMS send success/failure
- rate-limit blocks

---

### 9) Rate Limiting + Abuse Prevention

Add throttling to password reset requests by:
- phone
- IP

Requirements:
- reasonable cooldown (e.g., 60s+)
- lockout after repeated attempts (use existing throttle middleware patterns)

---

### 10) Testing (Manual Acceptance)

1) Run migrations → users remain intact; no data loss
2) Admin creates a user → phone required and stored
3) User updates profile phone → validation works
4) Forgot password by phone:
   - valid phone → SMS sent with temporary password
   - invalid/unknown phone → safe response
5) Login with temporary password works
6) User is forced to change password after login (if implemented)
7) Rate limit triggers after multiple requests
8) `php artisan route:list` works and shows password reset routes

---

Expected Outcome:

- Users table gains phone column safely
- Admin captures phone on user creation
- Users can update phone in profile
- Password reset works via phone and sends temporary password by SMS
- System remains stable and audit-ready

Priority:
HIGH – Auth usability and recovery

Notes:
- Follow existing UI patterns and i18n (include Swahili translations by default)
- Do not hardcode SMS content in controllers; use notifications/service if present
- Avoid breaking existing email-based password reset if it already exists
