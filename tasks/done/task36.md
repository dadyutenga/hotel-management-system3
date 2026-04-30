Task: Remove Cashier Role from the Entire System

Objective:
Remove the cashier role from the application everywhere it is exposed or assignable, while keeping the rest of the role system intact.

This task must:
- Remove cashier from user role creation and editing flows
- Remove cashier from role pickers, permissions info, dashboards, and any UI labels that surface it
- Prevent cashier from being assigned through validation, seeding, factories, or manual form submission
- Update any role-based logic, sidebar routing, or permission checks that reference cashier
- Keep existing admin, AP, accounting, and front-desk flows working normally

DO NOT redesign the role system.
DO NOT break existing authentication or authorization flows.
EXTEND existing controller, model, view, and seeding conventions.

---

### 1) Discovery First: Reuse Existing Role and Permission Patterns

Before implementation, identify:
- Where cashier is defined in the role model and seeded data
- Which forms and views expose cashier as a selectable role or informational label
- Which controllers or validators allow cashier assignment
- Which sidebar, dashboard, or route-selection logic depends on cashier

Implementation rule:
- Follow the project’s existing role naming and normalization patterns
- Do not introduce a parallel role system or temporary role mapping layer

---

### 2) Remove Cashier from Assignment Paths

Required behavior:
- Cashier must not appear in user create/edit dropdowns
- Cashier must be blocked at validation if submitted directly
- Cashier must not be assignable via seeders or factories used for test/demo data
- Cashier must not be reachable through any alternate role assignment path

Rules:
- UI lists must match backend filtering
- Validation must remain the source of truth even if the UI is bypassed
- Existing non-cashier roles must continue to work unchanged

---

### 3) Remove Cashier from System UI References

Update any views or labels that expose cashier, including:
- Role description blocks
- Permission summaries
- Sidebar or dashboard routes tied to cashier
- Language entries if they are no longer used anywhere in the system

UI rule:
- Preserve existing styling and translation conventions
- Remove cashier references without altering unrelated layouts

---

### 4) Update Role-Based Logic Safely

If cashier is referenced in application logic:
- Remove cashier-specific branches when they are no longer needed
- Ensure fallback behavior remains correct for other roles
- Keep guard checks and role comparisons consistent with existing helpers

Important:
- Do not break existing role matching or normalize/match helpers unless absolutely necessary
- Keep changes minimal and localized to the affected branches

---

### 5) Audit and Data Integrity

Ensure the removal is traceable and safe:
- Log any blocked cashier assignment attempts if logging already exists in the flow
- Avoid deleting historical user records unless a separate cleanup task is explicitly approved
- If historical cashier assignments exist, preserve them unless the project requires a migration plan

---

### 6) Testing and Manual Acceptance Criteria

Validate end-to-end:
1) Cashier does not appear in user role create/edit dropdowns
2) Direct POST requests assigning cashier are rejected
3) Existing users with other roles continue to work normally
4) No dashboard, sidebar, or permission panel still surfaces cashier
5) Seeding and factories do not recreate cashier assignments
6) Authentication and authorization flows still resolve correctly for remaining roles

Expected Outcome:

- Cashier is removed from the active system role surface area
- Role assignment and authorization stay consistent and secure
- Existing non-cashier behavior remains stable

Priority:
HIGH - Role hygiene and access-control consistency

Notes:
- Keep changes minimal and consistent with current controller/service boundaries
- Prefer removing cashier at the source of truth rather than only hiding it in the UI
