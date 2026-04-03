You are a senior Laravel payments engineer working INSIDE a production Laravel codebase.

You MUST discover all payment-related logic dynamically. DO NOT assume structure.

========================================
OBJECTIVE
========================================
Implement a unified payment request system supporting:
- USSD (phone-based)
- Card payments

The system must:
- Use customer data (name + phone/card)
- Work across bookings, reservations, and laundry
- Store all transactions
- Track status (pending, success, failed)

========================================
GLOBAL RULES
========================================
- DO NOT hardcode payment logic in controllers
- DO NOT depend on ClickPesa (it will be removed later)
- ALL payments must go through a central service
- DO NOT break existing flows

========================================
STEP 1: DISCOVER EXISTING PAYMENT FLOW
========================================

Search for:
- "payment"
- "transaction"
- "checkout"
- "pay"
- "snippe"

Locate:
- Controllers handling payments
- Models storing transactions
- Existing services

Document:
- Current flow
- Entry points
- Database tables used

========================================
STEP 2: SEARCH PAYMENT SERVICE LAYER
========================================
All in all  search  for  the   snippe  payments methods  
Check if exists:
- app/Services/PaymentService.php

IF NOT:
- Create it

Methods:
- requestUssdPayment($phone, $amount, $meta)
- requestCardPayment($cardData, $amount, $meta)
- handleCallback($payload)

Rules:
- Must be reusable
- Must NOT depend on controller
- Must return structured response

========================================
STEP 3: DATABASE STRUCTURE
========================================

Ensure transactions table has:

- id
- user_id / customer_id
- reference
- amount
- currency
- phone (nullable)
- provider
- status (pending, success, failed)
- metadata (json)
- created_at

If missing:
- Create migration
- DO NOT drop existing data

========================================
STEP 4: USSD PAYMENT FLOW
========================================

- Accept phone + amount
- Create transaction (status = pending)
- Trigger external request (mock if no provider)
- Return response

Status updates:
- pending → success/failed

========================================
STEP 5: CARD PAYMENT FLOW
========================================

- Accept card details
- Validate input
- Create transaction
- Process payment (mock or integrate)

========================================
STEP 6: INTEGRATION POINTS
========================================

Locate:
- Booking checkout
- Laundry payment
- Reservation payment

Replace direct payment logic with:
- PaymentService calls

========================================
STEP 7: ERROR HANDLING
========================================

- Wrap all payment calls in try/catch
- Log failures
- DO NOT break checkout flow

========================================
STEP 8: UI FEEDBACK
========================================

Ensure:
- Payment status is shown
- Pending state visible
- Success/failure messages clear

========================================
STEP 9: TESTING
========================================

Test:
- USSD success
- USSD failure
- Card success
- Card failure
- Missing phone/card

========================================
OUTPUT
========================================

1. Files created/modified
2. PaymentService implementation
3. Integration points
4. Transaction logs

========================================
GOAL
========================================

- Unified payment system
- Clean architecture
- Trackable transactions