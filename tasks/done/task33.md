Task: Enforce Stock Transfer Role Workflow (Store Keeper Requests, Store Manager Approves/Confirms)

Objective:
Ensure stock transfers are initiated by the store keeper with clear source and destination location selection, while store manager is limited to approval/confirmation actions only.

This task must:
- Make store keeper the primary actor to create transfer requests
- Require explicit `from` location and `to` location when creating transfer
- Prevent invalid self-transfer (same source and destination)
- Restrict store manager to approval/confirmation (no direct transfer creation/editing)
- Keep transfer audit trail clear (requested, approved/rejected, fulfilled)

DO NOT redesign the stock module architecture.
DO NOT bypass stock integrity checks.
EXTEND existing routes/controllers/views/permissions patterns.

---

### 1) Discovery First: Inspect Existing Transfer Flow

Before implementing, review and document current behavior in:
- Transfer routes and middleware role guards
- Transfer controller methods (`index/create/store/approve/reject/fulfill`)
- Transfer views (list + create form)
- Model statuses and transition logic
- Stock movement updates tied to transfer completion

Goal:
- Confirm what already works
- Identify where role leakage or missing validation exists

---

### 2) Role Ownership Rules (Core)

Required role behavior:
- **Store Keeper**:
  - can create transfer request
  - can choose source and destination location
  - can submit transfer for approval
- **Store Manager**:
  - can review pending transfers
  - can approve/confirm transfer
  - can reject transfer with reason
  - should NOT create transfer directly

Security requirements:
- Enforce permissions server-side (not UI-only)
- Hide/disallow UI actions for unauthorized roles

---

### 3) Transfer Form Requirements (From -> To)

Update/verify transfer creation form to require:
- Product/item selection
- Quantity
- `from_location_id` (source location)
- `to_location_id` (destination location)

Validation rules:
- Source location required
- Destination location required
- Source and destination must be different
- Quantity must be positive and within allowed precision
- Product must exist and be transferable

Usability:
- Label fields clearly as **From** and **To**
- Show validation error near each field

---

### 4) Approval and Confirmation Workflow

Required status lifecycle (reuse existing status names where possible):
- Pending (requested by store keeper)
- Approved (store manager approval)
- Completed/Fulfilled (stock moved and finalized)
- Rejected (with mandatory reason)

Rules:
- Only approved transfers can be fulfilled/completed
- Rejection requires reason/comment
- Prevent duplicate approval/rejection on already finalized records

---

### 5) Stock Integrity and Movement Posting

When transfer is completed:
- Decrease stock from source location
- Increase stock at destination location
- Record stock movement/audit references for traceability

Validation requirements:
- Block completion if source stock is insufficient
- Keep operation transactional (all-or-nothing)
- No negative stock due to race conditions

---

### 6) Transfer List and Manager Review Views

Ensure transfer index/review view shows:
- Product, quantity
- From location, to location
- Requester
- Current status
- Approve/reject/complete actions based on user role + status

Manager-specific behavior:
- Focus queue on pending transfers
- Include rejection reason where applicable

---

### 7) Audit Trail and Logging

Capture these fields/events where schema supports:
- requested_by / requested_at
- approved_by / approved_at
- rejected_by / rejected_at / rejection_reason
- fulfilled_by / fulfilled_at

Log:
- Unauthorized action attempts
- Validation/transition failures

---

### 8) Testing and Manual Acceptance

Validate end-to-end:

1) Store keeper can open transfer create form
2) Store keeper must select both From and To locations
3) Same From/To is blocked by validation
4) Store manager cannot create transfer directly
5) Store manager can approve pending transfer
6) Store manager can reject pending transfer with reason
7) Only approved transfer can be completed
8) Completing transfer updates source/destination stock correctly
9) Transfer list shows correct statuses and role-based actions

---

Expected Outcome:

- Store keeper owns transfer request creation with mandatory From -> To selection
- Store manager strictly handles approval/confirmation actions
- Transfer lifecycle is controlled, auditable, and stock-safe

Priority:
HIGH - Inventory control and operational governance

Notes:
- Follow existing UI and translation patterns (include Swahili where applicable)
- Keep route/controller changes minimal and consistent with existing store module
- Reuse existing stock movement and approval services instead of parallel logic
