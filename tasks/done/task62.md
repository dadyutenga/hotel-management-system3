Task: Align Restaurant Manager Views and Sidebar Layout

Objective:
Align all restaurant manager views to use the standard sidebar and layout used by the restaurant module, so pages are consistent and not using a separate sidebar/layout.

This task must:
- Switch restaurant manager views to the shared restaurant sidebar/layout
- Ensure all restaurant manager pages appear under the same sidebar navigation
- Keep working features intact; avoid changes outside layout alignment

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Restaurant Manager Layouts

Before implementation, identify:
- All restaurant manager views and their current layout wrappers
- The standard restaurant sidebar/layout used elsewhere
- Any views using custom or isolated sidebars

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing restaurant layouts and sidebar partials

---

### 2) Layout Alignment

Required behavior:
- Replace custom/isolated layout usage with the standard restaurant layout
- Ensure all restaurant manager views share the same sidebar navigation
- Verify no broken routes or missing sections after alignment

---

### 3) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) All restaurant manager pages use the standard restaurant sidebar
2) Navigation items remain consistent across restaurant manager views
3) No unrelated behavior regressions
4) No DB resets or data loss

---

Expected Outcome:

- Restaurant manager views are aligned with the standard restaurant layout
- Sidebar navigation is consistent across all restaurant manager pages

Priority:
HIGH - UI consistency
