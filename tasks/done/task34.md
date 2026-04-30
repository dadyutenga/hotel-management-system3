Task: Improve Supplier Payment Workflow (Auto Reference + Optional Full Allocation + Payment Deletion)

Objective:
Improve the supplier payment process so it is faster and more flexible for finance users.

This task must:
- Auto-generate a Payment Reference when creating a supplier payment
- Remove the rule that forces full allocation before finalizing/posting a payment
- Allow users to delete eligible supplier payments from the recent payments list

DO NOT redesign the full payments module.
DO NOT break existing supplier payment creation/edit/allocation flows.
EXTEND existing numbering, status, validation, permissions, and UI patterns.

---

### 1) Discovery First: Reuse Existing Numbering + Approval/Posting Patterns

Before implementing, identify existing project conventions for:
- Auto-generated document/reference numbers (voucher, invoice, GRN, etc.)
- Payment status transitions (Draft, Posted, Allocated, etc.)
- Delete/void behavior for financial records
- Permissions and audit logging in finance modules

Implementation rule:
- Reuse existing architecture and naming conventions
- Avoid introducing a parallel custom flow just for supplier payments

---

### 2) Auto-Generate Payment Reference in Create Supplier Payment

Current issue to fix:
- Users are expected to manually enter Payment Reference

Required behavior:
- On opening `Create Supplier Payment`, auto-populate `payment_reference`
- Reference should be unique and follow existing project format/sequence rules
- If generation fails client-side, server-side must still generate safely on save
- Payment Reference should no longer be blocked by "required manual input" validation

Validation and safety:
- Prevent duplicate references (race-safe generation)
- Keep backward compatibility for existing saved payments

---

### 3) Make Full Allocation Optional Before Finalization

Current issue to fix:
- System requires allocating the full payment amount before finalizing/posting

Required behavior:
- User can finalize/post a supplier payment even if allocation is partial or zero
- Track and show unallocated balance clearly
- Allocation can be completed later through existing allocation flow

Rules:
- Allow: `allocated_amount <= payment_amount`
- Block: `allocated_amount > payment_amount`
- Keep allocation integrity across edits and re-open actions
- Preserve existing accounting/posting logic where already implemented

---

### 4) Add/Delete Action for Recent Supplier Payments

Current issue to fix:
- Users cannot delete unwanted recent supplier payments (especially drafts)

Required behavior:
- Add delete capability in recent supplier payments list/view
- Deletion must respect permission checks (role/policy)
- Restrict deletion to safe states using existing finance rules (prefer Draft-only if no existing void pattern)

Safety requirements:
- Confirm before delete (UI confirmation)
- Log who deleted and when (or use existing soft-delete/audit pattern)
- If record is already posted/linked, block hard delete and show clear validation message

---

### 5) UI and UX Requirements

Create Supplier Payment form:
- Show auto-generated `Payment Reference` immediately
- Keep field consistent with existing form style (readonly/editable as per current convention)
- Update helper text to indicate full allocation is optional

Recent payments list:
- Add `Delete` action where allowed
- Keep actions aligned with current button/dropdown style

---

### 6) Backend/API and Validation Updates

Update relevant controller/service/model logic:
- Reference generation and uniqueness guard
- Validation rules to remove "must fully allocate before finalize" constraint
- Delete endpoint/action guards by status + permission

Security requirements:
- Enforce all critical checks server-side (not UI-only)
- Return clear error messages for invalid delete/finalize attempts

---

### 7) Audit Trail and Logging

Record important events (using existing audit patterns):
- payment created with auto reference
- payment finalized with partial/unallocated amount
- payment deleted (actor + timestamp + reason if available)
- unauthorized delete/finalize attempts

---

### 8) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Create Supplier Payment auto-fills Payment Reference
2) User can save/finalize payment without full allocation
3) Over-allocation is blocked correctly
4) Unallocated balance remains visible and can be allocated later
5) Draft/eligible payments can be deleted from recent list
6) Non-eligible payments (posted/linked) are protected from invalid deletion
7) Unauthorized users cannot delete/finalize beyond permission
8) Existing supplier payment and allocation flows are not broken

---

Expected Outcome:

- Supplier payment reference is generated automatically and reliably
- Full allocation is no longer mandatory for finalization
- Users can clean up unwanted eligible supplier payments via delete action
- Workflow remains secure, auditable, and aligned with existing project patterns

Priority:
HIGH - Finance usability and operational accuracy
