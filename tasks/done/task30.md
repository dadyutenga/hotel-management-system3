Task: Fix Supplier Payables Creation (Supplier + GRN Selection) + Accounting GRN Visibility

Objective:
Resolve the issue where the accountant cannot select suppliers while creating supplier payables, and ensure GRNs are visible and usable on the accounting side.

Affected flow:
- `http://127.0.0.1:8000/accountant/supplier-payments/create`

This task must:
- Restore supplier selection in supplier payable creation form
- Make eligible GRNs visible/selectable in accounting workflows
- Ensure payable creation can be completed end-to-end

DO NOT redesign accounting architecture.
DO NOT bypass approval/business rules.
EXTEND existing controller/service/query/view patterns.

---

### 1) Discovery First: Trace Current Accounting Payables Flow

Before changing code, inspect existing implementation for:
- Supplier payables create page view and form components
- Controller methods loading suppliers and GRNs
- Route definitions and middleware/permission checks
- Model scopes/filters for supplier and GRN retrieval
- Any JS (AJAX/select2/live search) used to populate dropdowns

Goal:
- Identify why supplier dropdown is empty/disabled
- Identify why GRNs are not visible to accounting users

Rule:
- Reuse existing module conventions; avoid introducing parallel logic paths

---

### 2) Fix Supplier Dropdown Data Loading

Issue:
- Supplier cannot be selected in supplier payable creation flow

Required behavior:
- Supplier field is populated with active/eligible suppliers
- Supplier field is selectable and submitted correctly
- Existing validation errors are shown clearly if supplier is missing/invalid

Checks to implement:
- Ensure controller passes supplier dataset to view
- Ensure view binds correct variable keys and value/label mapping
- Ensure front-end select component (if AJAX-based) points to valid endpoint
- Ensure permission/tenant/company filters do not incorrectly hide all suppliers

Validation:
- `supplier_id` required and must exist in allowed scope

---

### 3) Make GRNs Visible in Accounting Side

Issue:
- GRNs are not viewable/selectable when creating supplier payables

Required behavior:
- Accountant can see eligible GRNs for payable creation
- GRN list follows business rules (e.g., approved/received/not yet fully paid)
- GRN details necessary for payable entry are accessible (number, supplier, amount, balance)

Implementation requirements:
- Fix GRN query filters/scopes used by accounting module
- Ensure joins/relations (supplier, store, receiving) are not excluding valid data unintentionally
- Ensure route/controller/view for GRN lookup is accessible to accountant role

Rule:
- Keep integrity: do not include GRNs that are cancelled, invalid, or already fully settled

---

### 4) Supplier-to-GRN Dependency and Consistency

When a supplier is selected:
- GRN options should be filtered to that supplier only
- Prevent selecting GRN belonging to a different supplier

Required safeguards:
- Front-end filtering for usability
- Back-end validation for security/data integrity

Validation rules:
- Selected GRN must exist
- Selected GRN must belong to selected supplier
- Selected GRN must be in payable-eligible state

---

### 5) Complete Supplier Payable Save Flow

Ensure that after fixing selections:
- Form submits successfully
- Payable record persists with correct foreign keys and amounts
- Duplicate or overpayment scenarios are blocked according to existing business rules

If partial payments are supported:
- Ensure remaining balance is computed and updated correctly

---

### 6) Permissions and Role Access

Verify accountant permissions for:
- Viewing suppliers used in payables
- Viewing eligible GRNs
- Creating supplier payable entries

Rules:
- Do not grant broad admin permissions as a shortcut
- Keep role checks in both UI and backend

---

### 7) Error Handling and Logging

Add/fix safe diagnostics for failures:
- Log when supplier dataset resolves empty due to filters/scopes
- Log GRN query failures or permission denials
- Return user-friendly validation/errors on form

Do not expose sensitive internals in UI error messages.

---

### 8) Testing and Manual Acceptance

Validate end-to-end:

1) Open supplier payable create page -> supplier dropdown is populated
2) Select supplier -> related GRNs become visible/selectable
3) Non-matching supplier/GRN combination is blocked
4) Submit payable successfully with valid supplier + GRN
5) Ineligible GRNs (cancelled/fully paid/etc.) are excluded
6) Accountant role can access required data without escalation
7) Existing payable flows outside this issue remain stable

---

Expected Outcome:

- Supplier selection works in supplier payable creation
- GRNs are visible on accounting side according to business rules
- Accountant can create supplier payables reliably with correct supplier-GRN linkage
- Data integrity and role boundaries remain intact

Priority:
HIGH - Accounting operations and payment processing

Notes:
- Follow existing UI patterns and i18n (include Swahili where applicable)
- Prefer fixing query scope/binding issues over adding hardcoded fallback data
- Keep controller/view changes minimal and aligned with current accounting module structure
