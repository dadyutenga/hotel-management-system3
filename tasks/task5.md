You are a Laravel architect.

========================================
OBJECTIVE
========================================
Create unified checkout system across all modules.

========================================
STEP 1: FIND ALL BILLABLE MODULES
========================================

Search:
- bookings
- laundry
- reservations

========================================
STEP 2: CREATE CHECKOUT PAGE
========================================

- Central billing view

========================================
STEP 3: AGGREGATE DATA
========================================

Include:
- items
- services
- totals

========================================
STEP 4: TRANSACTIONS
========================================

- Record all payments

========================================
STEP 5: STANDARD FLOW
========================================

Module → Checkout → Payment → Confirm

========================================
OUTPUT
========================================

- Checkout implementation