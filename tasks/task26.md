Task: Restaurant Buffet Billing Flow (Walk-in + Booking-Linked) + Menu/Package Setup

Objective:
Implement a Restaurant Buffet flow that supports:
- Walk-in buffet customers paying immediately
- Hotel guests (booking-linked) charging buffet to room folio (collectable billing)
- Consistent receipts and accounting traceability
- Buffet package/menu setup managed by Restaurant Manager

Problem Summary:

* Restaurant operations need a buffet option distinct from normal à la carte orders.
* Buffet must have fixed packages/prices and integrate into existing billing.

---

### 1. Buffet Package Setup (Restaurant Manager)

Create buffet package configuration:
- Package name (e.g., Breakfast Buffet, Lunch Buffet, Dinner Buffet)
- Price per adult
- Price per child (optional)
- Availability schedule:
  - days of week
  - time window (start/end)
- Active/inactive toggle

Rules:
- Packages must be editable only by Restaurant Manager (or Manager/Admin)
- Changes must not affect already-paid historical receipts (price snapshots should be captured at sale time)

---

### 2. Buffet Sale Types

Support two buffet sale types:

A) Walk-in Buffet
- Customer not linked to booking
- Payment must be immediate
- Receipt must be generated

B) Booking-linked Buffet (Charge to Room)
- Select booking/room/guest
- Create a charge that appears in guest folio/unified checkout
- No immediate payment required at restaurant

---

### 3. Buffet Order Capture

Buffet sale screen must capture:
- Package selected
- Number of adults
- Number of children (optional)
- Notes (optional)
- Served by (waiter)

Totals:
- total = adults * adult_price + children * child_price

Rules:
- Validate counts are non-negative integers
- Validate package is active and within availability window (or manager override, if you already support overrides)

---

### 4. Billing Integration (MUST)

A) Walk-in buffet:
- Require payment record before marking as settled
- Generate receipt

B) Booking-linked buffet:
- Create a billing record (BookingCharge or existing equivalent)
- charge_type should map to restaurant
- Description includes buffet package + pax count
- Amount matches buffet totals

---

### 5. Receipt + Traceability

- Walk-in: receipt required
- Booking-linked: must be visible in unified checkout and be payable there
- All buffet records must store:
  - package snapshot (name + price used)
  - actor (created_by/served_by)
  - references (booking_id if applicable)

---

### 6. Access Control

- Restaurant Manager can manage buffet packages
- Waiters/cashiers can record buffet sales
- Only authorized cashier/accountant can accept payments (if your system separates roles)

---

### 7. Testing (Manual Acceptance)

1) Restaurant manager creates buffet package with schedule
2) Walk-in buffet sale → pay → receipt prints
3) Booking-linked buffet sale → charge appears in guest folio → paid at checkout
4) Package inactive/outside schedule → blocked
5) Historical receipts still show correct price even after package price changes

---

Expected Outcome:

* Buffet works for walk-ins and hotel guests
* Billing is accurate and traceable
* Restaurant manager controls buffet packages

Priority:
HIGH – Common restaurant revenue flow

Notes:
- Follow existing UI patterns and i18n (include Swahili translations by default)
- Do not break existing restaurant à la carte ordering
