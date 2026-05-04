Task: Fix Item Selection During Order Creation

Objective:
Fix the order creation flow so items can be selected and added to the order reliably.

This task must:
- Allow selecting items during order creation
- Ensure selected items appear in the order summary/ticket
- Keep existing order creation behavior unchanged apart from the fix

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Order Creation Flow

Before implementation, identify:
- Where order creation UI renders items
- Item selection handler and state updates
- Where selected items are persisted to the order

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse current item selection logic where possible

---

### 2) Fix Item Selection

Required behavior:
- Items can be clicked/selected during order creation
- Selected items are added to the order list/ticket
- Selection works consistently across item categories

---

### 3) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Items are selectable during order creation
2) Selected items appear in the order summary/ticket
3) No regressions in existing order creation flow
4) No DB resets or data loss

---

Expected Outcome:

- Order creation allows selecting and adding items as expected

Priority:
HIGH - Order flow blocker
and  in  order   creation  i cant  select  the      the  items   so  we  neeed   to  fix  t his  asap    so   patvh  the     new  prompt   
