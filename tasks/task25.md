Task: Bar Walk-in Sales (POS) + Immediate Payments + Receipts + Stock Deduction

Objective:
Implement a complete walk-in flow for the Bar so that guests who are NOT linked to a room/booking can:
- Order drinks at the bar
- Pay immediately
- Receive a receipt
- Ensure stock is deducted automatically

Problem Summary:

* The system supports bar/restaurant orders and unified guest checkout, but bar walk-ins need a clean POS-like workflow.
* Walk-ins must not be forced into guest folio billing.
* Walk-ins must still be fully auditable (payments + receipts + stock movements).

---

### 1. Walk-in Bar Order Creation

Add a Bar Walk-in order screen for bartender/cashier:
- Select drinks (from bar menu items)
- Set quantities
- Show unit price + line subtotal
- Show order subtotal/total
- Customer name/phone (optional)

Rules:
- Walk-in orders MUST NOT require booking_id
- Order must have a clear reference number (ticket/order_no)
- Status lifecycle: draft/pending → paid/settled → cancelled

---

### 2. Payment (Immediate)

Walk-in bar orders must be payable immediately:
- Cash
- Card
- Mobile money (if existing integration)

Rules:
- Order cannot be marked completed/served as “paid” unless a payment record exists
- Payment must store:
  - method
  - amount
  - currency
  - reference (transaction ref)
  - paid_at
  - cashier/bartender actor

---

### 3. Receipt Generation + Reprint

After payment:
- Generate a receipt tied to the walk-in order/payment
- Provide Print/Reprint action

Rules:
- Reprint must be idempotent (same receipt number)
- Receipt must include:
  - order reference
  - items
  - totals
  - payment method
  - cashier/bartender
  - timestamp

---

### 4. Auto Stock Deduction (REQUIRED)

Deduct stock automatically when the sale is confirmed/paid:

Rules:
- Deduct only once (idempotent)
- Deduct inside DB transaction
- Validate stock availability before finalizing payment
- If insufficient stock:
  - block payment/finalization
  - show a clear message

Audit:
- Each deduction must be logged with:
  - item
  - qty
  - from stock location (bar)
  - reference (walk-in order)
  - actor
  - timestamp

---

### 5. Access Control

- Bartender/Bar cashier can create and settle walk-in bar sales
- Manager can view all walk-in sales + receipts
- Other roles must not access bar walk-in POS

---

### 6. Reporting (Minimal)

Add a minimal list page:
- Walk-in bar sales list
- Filters: date range, status, payment method
- Totals summary for selected period

---

### 7. Testing (Manual Acceptance)

1) Create walk-in bar order → items added → totals correct
2) Attempt to pay with insufficient stock → blocked
3) Pay successfully → payment record saved → receipt generated
4) Reprint receipt → same receipt number
5) Stock quantity decreases exactly once
6) Cancel order before payment → no stock deducted
7) Authorization enforced (non-bartender blocked)

---

Expected Outcome:

* Bar can serve walk-in guests end-to-end
* Payments and receipts are auditable
* Stock is always accurate

Priority:
HIGH – Daily revenue flow

Notes:
- Follow existing UI patterns and i18n (include Swahili translations by default)
- Do not break existing bar/restaurant order settlement for booking-linked guests
