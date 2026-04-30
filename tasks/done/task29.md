Task: Fix GRN Approval Workflow (Shopkeeper -> Manager) + Manager Review Views + Resubmission Cycle

Objective:
Ensure GRNs are not self-approved by shopkeepers and are correctly routed to managers for approval.

This task must:
- Stop shopkeepers from approving their own GRNs
- Forward submitted GRNs to manager approval queue
- Provide manager approval views with full receipt visibility
- Allow manager to approve or cancel/reject for resubmission
- Allow shopkeeper to correct and resubmit cancelled GRNs

DO NOT redesign the entire module.
DO NOT break existing GRN creation/edit flows.
EXTEND existing approval patterns, views, routes, and controller logic.

---

### 1) Discovery First: Reuse Existing Manager Approval Pattern

Before implementing changes, identify and study current manager approval implementation in the project.

Required discovery:
- Find manager approval-related views
- Find controllers/methods handling manager approvals
- Find routes and middleware/permissions used for approval
- Find status fields/state transitions currently used

Implementation rule:
- Reuse existing architecture and conventions
- Apply the same style/pattern to GRN approvals
- Avoid creating parallel inconsistent flows

---

### 2) Fix Role Permissions and Approval Boundaries

Current issue to fix:
- Shopkeeper is able to approve GRN directly (should not happen)

Required behavior:
- Shopkeeper can: create, save, submit, edit when returned
- Manager can: review, approve, cancel/reject for resubmission
- Approval action must be restricted by role/permission checks in both UI and backend

Security requirements:
- Block unauthorized approval endpoints server-side
- Hide/disallow approval buttons for non-manager roles

---

### 3) Correct GRN Workflow State Transitions

Define and enforce clear GRN lifecycle states (use existing status names if already defined):
- Draft (shopkeeper working)
- Submitted/Pending Manager Approval
- Approved
- Cancelled/Returned for Resubmission
- Resubmitted (if separate from Submitted in current system)

Rules:
- Submitting a GRN must move it to manager queue
- Manager approval finalizes it
- Manager cancellation/return sends it back to shopkeeper editable state
- Track transition timestamps and acting user where patterns already exist

---

### 4) Manager Approval Queue and Review Views

Add/fix manager-facing views so managers can see pending GRNs.

Manager list view requirements:
- Show only GRNs requiring manager action
- Include key columns (GRN number, supplier, date, amount/status, submitted by)
- Add quick action links/buttons (View, Approve, Cancel/Return)

Manager detail view requirements:
- Show full GRN details and line items
- Show uploaded receipt(s)/attachment(s)
- Ensure receipts are viewable/downloadable from manager screen

UI rule:
- Follow existing manager approval UI style in the project

---

### 5) Manager Actions: Approve or Cancel for Resubmission

Implement/repair manager actions:

Approve:
- Allowed only for manager role
- Updates status to Approved
- Records approver and approved timestamp

Cancel/Return:
- Allowed only for manager role
- Requires a reason/comment for correction
- Updates status to Cancelled/Returned
- Sends GRN back to shopkeeper for editing and resubmission

Validation:
- Prevent duplicate/conflicting approvals (idempotent or guarded updates)
- Prevent approving already approved/cancelled records without proper flow

---

### 6) Shopkeeper Resubmission Flow

When manager returns a GRN:
- Shopkeeper can open returned GRN
- Shopkeeper can see manager reason/comments
- Shopkeeper can update data/receipts
- Shopkeeper can resubmit to manager approval queue

Rules:
- Returned GRN should not be stuck in read-only mode
- Resubmission must re-enter manager pending queue

---

### 7) Audit Trail and Logging

Record approval events:
- submitted_by and submitted_at
- approved_by and approved_at
- cancelled_by and cancelled_at
- cancellation/return reason

Log:
- Unauthorized approval attempts
- Status transition failures

If audit table/pattern already exists, integrate there instead of creating a new structure.

---

### 8) Notifications (If Existing Pattern Is Available)

If the project already has notifications:
- Notify manager when a GRN is submitted/resubmitted
- Notify shopkeeper when GRN is cancelled/returned with reason

Rules:
- Reuse existing notification service/channels
- Do not hardcode notification logic in random controllers if a service layer exists

---

### 9) Testing and Manual Acceptance

Validate end-to-end flow:

1) Shopkeeper creates GRN -> can submit but cannot approve
2) Submitted GRN appears in manager approval list
3) Manager opens GRN and can view receipt attachments
4) Manager approves -> status becomes Approved, audit is recorded
5) Manager cancels/returns with reason -> shopkeeper sees reason
6) Shopkeeper edits and resubmits -> manager can review again
7) Non-manager trying to approve/cancel is blocked server-side
8) Existing GRN flows outside approval are not broken

---

Expected Outcome:

- GRN approval authority is correctly enforced (manager-only)
- Managers can review full GRN details including receipts
- Managers can approve or return GRNs for correction
- Shopkeepers can fix and resubmit returned GRNs
- Workflow is traceable, stable, and aligned with existing project patterns

Priority:
HIGH - Procurement control and approval governance

Notes:
- Follow existing UI patterns and translations (include Swahili where applicable)
- Keep route/controller changes minimal and consistent with current module structure
- Prefer extending existing approval views/controllers over introducing new parallel logic
