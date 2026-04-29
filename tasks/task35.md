Task: Reconcile Supplier Payment Posting with Saved Allocations + Reflect Supplier Payables in Expenses

Objective:
Ensure accounting values stay consistent when supplier payments are posted, and ensure supplier payable activity is visible in expenses-related views/reports.

This task must:
- Ensure the amount posted uses the correct payment amount and aligns with saved allocation state
- Ensure saved allocations are accurately reflected after posting (no hidden mismatch)
- Ensure supplier payable/payment impact is reflected in expenses summaries or expense reports where project rules expect it

DO NOT redesign the accounting module.
DO NOT break existing AP, journal, or payment posting flows.
EXTEND existing service, controller, reporting, and UI conventions.

---

### 1) Discovery First: Reuse Existing Accounting and Expense Aggregation Patterns

Before implementation, identify:
- How supplier payment posting currently creates journal entries and financial transactions
- How allocations currently update payable `amount_paid`, `balance`, and `status`
- How expenses are currently calculated (journal-based, transaction-based, or mixed)
- Which pages/reports are considered the source of truth for expense totals

Implementation rule:
- Follow existing accounting architecture and naming
- Do not introduce parallel calculations that conflict with current report logic

---

### 2) Ensure Posted Amount and Allocation State Are Consistent

Current issue to fix:
- After posting, the posted amount and saved allocation state can appear inconsistent or not fully reflected

Required behavior:
- Posting a supplier payment must preserve and reflect the latest saved allocations
- If allocation is partial/zero (as allowed by current rules), posting must still produce consistent records
- Over-allocation must remain blocked (`allocated_total` must never exceed payment amount)

Rules:
- Allocated totals displayed in UI must match database totals
- Payable balances after posting must equal prior balance minus applied allocations
- No silent recalculation that changes user-saved allocation without explicit action

---

### 3) Enforce Data Integrity on Status Transitions

Validate lifecycle transitions:
- Draft/Pending -> Posted
- Posted -> Cancelled (if existing flow supports cancellation)

Requirements:
- Prevent posting invalid states
- Prevent duplicate posting side effects
- Keep idempotent/guarded updates so repeated submit does not double-apply effects

---

### 4) Reflect Correct Values in AP Screens

Update/fix AP views so posted data is transparent:
- Payment apply/detail view shows posted amount, allocated amount, and remaining/unallocated clearly
- Payable detail/history reflects allocations exactly as stored
- Dashboard recent payments and totals remain accurate after posting

UI rule:
- Preserve existing style/components and translations (EN/SW)

---

### 5) Reflect Supplier Payables/Payments in Expenses Views and Reports

Required behavior:
- Expense-related pages/reports should include supplier payable/payment impact according to existing accounting rules
- If expenses are journal-driven, ensure supplier payment journal postings are correctly included in expense calculations where applicable
- If expenses are transaction-driven, ensure supplier payment financial transactions are correctly categorized and included

Important:
- Do not classify AP principal payment incorrectly as new expense if your accounting design treats it as liability settlement
- Follow the project's chart-of-accounts/reporting conventions

---

### 6) Add Reconciliation Safeguards (If Needed)

If mismatch scenarios already exist in historical data:
- Add a safe reconciliation routine/command/service (reuse existing patterns)
- Recompute and align payable balances from allocation history where appropriate
- Log corrected records for audit review

Do not run destructive fixes without validation checks.

---

### 7) Audit Trail and Logging

Ensure logs/audit include:
- Posting actor/time and payment reference
- Allocation totals at posting time
- Any mismatch detection and correction actions
- Unauthorized or invalid posting attempts

Reuse existing audit infrastructure if available.

---

### 8) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Save allocations on a supplier payment draft
2) Post payment and verify posted amount + allocation totals remain consistent
3) Verify payable `amount_paid`, `balance`, and `status` are correct after posting
4) Verify over-allocation attempts are blocked
5) Verify AP dashboard/detail pages show accurate reflected values
6) Verify expenses views/reports include expected supplier payable/payment impact per accounting rules
7) Verify cancellation/reversal flow (if used) reverts/reflected values correctly
8) Verify no regressions in existing AP, journal, and reporting pages

---

Expected Outcome:

- Posted supplier payments consistently reflect saved allocations
- AP balances and statuses stay accurate and traceable
- Expenses views/reports correctly reflect supplier payable/payment accounting impact
- Workflow remains stable, auditable, and aligned with existing project patterns

Priority:
HIGH - Financial accuracy and reporting integrity

Notes:
- Keep changes minimal and consistent with current controller/service boundaries
- Prefer fixing source-of-truth logic over patching display-only values
