Task: Restaurant Manager Menu Management + Menu Options/Variants

Objective:
Allow the Restaurant Manager to create and manage:
- Menus (categories)
- Menu items (food/drinks)
- Menu options/variants (e.g., sizes, add-ons, cooking preference)
So that waiters can take orders using manager-defined menu configuration.

Problem Summary:

* Menu content must be controlled by restaurant management, not developers.
* Orders must be priced consistently and options must affect pricing where applicable.

---

### 1. Restaurant Manager Menu Area (CRUD)

Create a Restaurant Manager-only area to manage:

A) Categories
- name
- sort order
- active/inactive

B) Menu Items
- name
- category_id
- base_price
- status: available/unavailable
- kitchen/bar location tag (optional) for routing orders
- description (optional)

Rules:
- Only Restaurant Manager (and Manager/Admin) can create/edit/delete menu items
- Waiters can only view and order

---

### 2. Menu Options / Variants (REQUIRED)

Add options that can be attached to menu items:

Option Group examples:
- Size: Small/Medium/Large
- Add-ons: Cheese, Fries, Sauce
- Preference: Well done/Medium (no price impact)

Data requirements:
- option_group_name
- selection_type: single / multiple
- required: true/false
- option values:
  - label
  - price_delta (can be 0)

Rules:
- Options must be validated when ordering:
  - required groups must be selected
  - single-choice groups must not accept multiple selections

---

### 3. Ordering Integration (Non-breaking)

Ensure waiter ordering UI can:
- Display categories and items
- Select options/variants
- Compute line total:
  base_price + sum(option price deltas)

Rules:
- Store a snapshot of item name and selected options on the order item line
- Price snapshot must be stored so future menu edits don’t change historical receipts

---

### 4. Availability Controls

Restaurant Manager must be able to:
- Mark an item unavailable (out of stock/kitchen closed)
- Optionally set availability schedule by day/time (only if easy; otherwise keep it simple with available/unavailable)

Waiter UI:
- Must hide or disable unavailable items

---

### 5. Access Control (RBAC)

Restaurant Manager permissions:
- manage_menu_categories
- manage_menu_items
- manage_menu_options

Waiter permissions:
- view_menu
- create_orders

---

### 6. i18n

- All UI labels must use translation system
- Add Swahili translations for new labels

---

### 7. Testing (Manual Acceptance)

1) Restaurant manager creates categories + items
2) Restaurant manager creates option groups and attaches to items
3) Waiter creates order with required options → totals correct
4) Missing required option → blocked with validation error
5) Menu item marked unavailable → waiter cannot order it
6) Historical order keeps original item/price snapshot after menu edits

---

Expected Outcome:

* Restaurant manager fully controls menus and options
* Waiters can order using configured menus
* Pricing and receipts remain consistent and auditable

Priority:
HIGH – Core restaurant operations

Notes:
- Keep UI consistent with existing layouts
- Do not break existing restaurant/bar order models; extend them safely
