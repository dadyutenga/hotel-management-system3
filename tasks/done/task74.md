Task: Buffet Sale Menu Option on POS

Objective:
Make a buffet sale menu option appear on the POS waiter side so buffet packages can be selected and sold.

This task must:
- Show a "Buffet Sale" menu option on the POS waiter interface
- Display available buffet packages when selecting the buffet option
- Allow adding a buffet package to an order
- Allow restaurant managers to add items to buffet menus so sales can be finalized

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Buffet and POS

Before implementation, identify:
- Where POS menu options are defined and rendered
- Current buffet packages data source and how it is displayed
- How buffet menu items are created and managed by restaurant managers
- Ordering flow on POS for adding items to a ticket

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing ordering logic for adding items

---

### 2) POS Buffet Menu Option

Required behavior:
- Add a "Buffet Sale" option in POS menu options for waiters
- When selected, show buffet packages list
- Selecting a package adds it to the current order

---

### 3) Manager Buffet Menu Items

Required behavior:
- Restaurant managers can add items to buffet menus
- Items added to a buffet menu are reflected in the buffet packages list

---

### 4) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) "Buffet Sale" option appears on POS waiter side
2) Buffet packages list is visible and accurate
3) A buffet package can be added to an order
4) Restaurant managers can add items to buffet menus
5) No DB resets or data loss

---

Expected Outcome:

- Waiters can sell buffet packages from POS

Priority:
HIGH - POS usability
