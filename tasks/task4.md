You are a Laravel + Blade UI engineer.

========================================
OBJECTIVE
========================================
Implement payment modal for laundry walk-in checkout.

========================================
STEP 1: LOCATE LAUNDRY MODULE
========================================

Search:
- "LaundryController"
- "laundry_orders"
- "walk-in"

========================================
STEP 2: MODIFY SETTLE BUTTON
========================================

- Attach modal trigger

========================================
STEP 3: CREATE MODAL
========================================

Fields:
- name
- phone
- card (optional)

========================================
STEP 4: VALIDATION
========================================

Require:
- name
- (phone OR card)

========================================
STEP 5: PAYMENT INTEGRATION
========================================

Call:
- PaymentService

========================================
STEP 6: SUCCESS FLOW
========================================

- Mark as paid
- Return items/images

========================================
STEP 7: UX
========================================

- Disable button on submit
- Show loader
- Show result

========================================
OUTPUT
========================================

- Blade modal
- Controller update
- Payment integration