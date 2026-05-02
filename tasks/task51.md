Task: Fix Currency Consistency and Conversion Across Modules

Objective:
Ensure TZS (Tanzanian Shillings) and any configured base currency is reflected consistently across all modules and views (including bookings, conference, rooms, bar, restaurant, POS, store, accounting, reports) and that converted amounts display correctly wherever conversions are expected.

This task must:
- Apply the configured base currency consistently in all views and modules
- Ensure converted amounts display with correct currency labels and formatting
- Align currency conversion logic across bookings, bar, restaurant, and accounting
- Keep existing data intact

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Currency Sources and Views

Before implementation, identify:
- Where base currency is configured and stored
	- All places where currency is displayed (views, receipts, dashboards, reports)
- Any conversion logic or exchange rate helpers
	- Modules with inconsistent currency display (bookings, conference, rooms, bar, restaurant, POS, store, accounting)
- Any cached currency or rate values

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing helpers/services for currency and conversion

---

### 2) Base Currency Consistency

Required behavior:
- When base currency is set to TZS, all monetary values display TZS by default
- No module should display a different currency unless explicitly configured
- Currency labels and symbols should be consistent across modules

---

### 3) Conversion Display

Required behavior:
- If conversion is required, show both base and converted values
- Converted values should use correct exchange rates and formatting
- Conversion should not override base values unless explicitly requested

---

### 4) Module Coverage

Required behavior:
- Bookings and check-in/out flows show correct currency
- Conference and rooms views show correct currency
- Bar, restaurant, and POS show correct currency
- Store and inventory views show correct currency
- Receipts and invoices show correct currency and conversions
- Accounting and financial reports show correct currency

---

### 5) Caching and Refresh

Required behavior:
- Any cached currency or exchange rate values refresh correctly after changes
- Avoid stale currency display after settings updates

---

### 6) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Setting base currency to TZS updates all modules
2) Conference, rooms, bookings, bar, restaurant, POS, and store screens show consistent currency labels
3) Receipts and reports display correct currency and conversions
4) Converted amounts are accurate and formatted correctly
5) No DB resets or data loss

---

Expected Outcome:

- All modules display the configured base currency consistently
- Currency conversions are accurate and visible where required

Priority:
HIGH - Financial accuracy
