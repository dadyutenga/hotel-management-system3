Task: Fix Room Count Not Updating After Add

Objective:
Ensure the room count updates immediately after adding a new room in the rooms view, not only after changing status.

This task must:
- Update room count when a new room is created
- Keep status-based count updates working
- Ensure counts are consistent across the rooms view and dashboard widgets

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Room Count Sources

Before implementation, identify:
- Where room counts are calculated and displayed
- Any cached totals or computed fields
- Events or listeners tied to room creation and status changes

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing count/query helpers

---

### 2) Room Creation Updates

Required behavior:
- When a room is created, totals update immediately
- Counts reflect the new room without manual refresh where possible
- Status-specific totals still reflect the assigned status

---

### 3) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Add a new room and see count update instantly
2) Change room status and see status counts update correctly
3) No DB resets or data loss

---

Expected Outcome:

- Room counts update correctly on create and status changes

Priority:
HIGH - Rooms management accuracy
