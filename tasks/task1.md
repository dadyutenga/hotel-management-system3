You are a senior Laravel backend engineer operating INSIDE a production codebase.

You have full access to the repository. You MUST discover structure dynamically.
DO NOT assume file names, relationships, or architecture without verifying.

This task is HIGH RISK because it touches booking flow. Do NOT break existing logic.

========================================
OBJECTIVE
========================================
Implement a reliable notification system that sends:
- Email notifications
- SMS notifications

to the  customers 

TRIGGERS:
1. When a reservation is successfully created
2. When a booking is successfully confirmed/finalized

The system must:
- Not block user flow
- Not introduce latency
- Not break transactions
- Work with partial data (email or phone may be missing)

========================================
GLOBAL RULES
========================================
- DO NOT duplicate logic across controllers
- DO NOT hardcode email/SMS inside controllers
- DO NOT interrupt booking/reservation flow on failure
- ALL notification logic must be centralized
- ALL failures must be logged, not thrown to user

========================================
STEP 1: LOCATE ENTRY POINTS
========================================

Search for:
- Reservation creation logic
- Booking confirmation logic

Use search keywords:
- "ReservationController"
- "BookingController"
- "store("
- "create("
- "confirm("
- "finalize("
- "completeBooking"

For each match:
- Read full method
- Identify EXACT line where:
  - DB save is successful
  - Transaction is committed

DO NOT hook before transaction completes.

Record:
- File path
- Method name
- Line reference

========================================
STEP 2: TRACE DATA STRUCTURE
========================================

For both Reservation and Booking:

Identify:
- Model used (Reservation, Booking, etc.)
- Relationships:
  - customer()
  - guest()
  - user()

Extract fields:
- name
- email
- phone
- booking id
- reservation id
- dates
- total amount

If relationships are missing:
- Add proper Eloquent relationships
- Ensure eager loading if needed

========================================
STEP 3: CREATE NOTIFICATION SERVICE
========================================

Check if a service layer already exists:
- Search "Services/"
- Search "NotificationService"

IF EXISTS:
- Extend it

IF NOT:
- Create:
  app/Services/NotificationService.php

Inside it implement:

Methods:
- sendReservationConfirmation($reservation)
- sendBookingConfirmation($booking)

Rules:
- Methods must accept full model instance
- Methods must NOT depend on controller context
- Must internally call Email + SMS logic

========================================
STEP 4: EMAIL IMPLEMENTATION
========================================

Check:
- Does app/Mail exist?

IF NOT:
- Create directory

Create:
- app/Mail/ReservationConfirmed.php
- app/Mail/BookingConfirmed.php

Each must:
- Extend Mailable
- Accept model in constructor
- Pass data to Blade view

Create views:
- resources/views/emails/reservation_confirmed.blade.php
- resources/views/emails/booking_confirmed.blade.php

Content must include:
- Greeting with customer name
- Booking/Reservation ID
- Dates
- Amount
- Simple clean HTML

In NotificationService:
- Use Mail facade
- Prefer:
  Mail::to($email)->queue(new ReservationConfirmed($reservation))

Fallback:
- If queue not configured → use send()

Validate:
- Skip email if email is null

========================================
STEP 5: SMS IMPLEMENTATION
========================================

Search for existing SMS:
- "SmsService"
- "sendSms"
- "twilio"
- "africastalking"

IF FOUND:
- Reuse it

IF NOT:
- Create:
  app/Services/SmsService.php

Method:
- send($phone, $message)

Rules:
- Accept raw phone
- Normalize format if needed
- Return success/failure boolean

Message format:
- Reservation:
  "Hello {name}, your reservation #{id} is received."

- Booking:
  "Hello {name}, your booking #{id} is confirmed."

In NotificationService:
- Call SmsService after email

Validation:
- Skip if phone is null

========================================
STEP 6: ERROR HANDLING
========================================

Wrap ALL notification calls in try/catch

Inside catch:
- Log error using Log::error()

DO NOT:
- Throw exception
- Interrupt main flow

========================================
STEP 7: INTEGRATE INTO CONTROLLERS
========================================

Return to controllers from STEP 1

AFTER successful DB save:

Add:
- app(NotificationService::class)->sendReservationConfirmation($reservation)
- app(NotificationService::class)->sendBookingConfirmation($booking)

Placement:
- AFTER commit
- NOT before validation
- NOT before save

========================================
STEP 8: QUEUE HANDLING
========================================

Check if queue is configured:
- .env QUEUE_CONNECTION

IF YES:
- Use queue()

IF NO:
- Use send()

DO NOT force queue if not configured

========================================
STEP 9: LOGGING
========================================

Log:
- Success (optional)
- Failures (mandatory)

Use:
- Log::info
- Log::error

========================================
STEP 10: TEST CASES
========================================

Test:
1. Reservation with email phone
2. Reservation with phone and email
3. Booking with both
4. Missing both → no crash
5. SMS failure → booking still works
6. Email failure → booking still works

========================================
STEP 11: CLEANUP
========================================

Ensure:
- No duplicate notification code
- Controllers remain thin
- Service is reusable

========================================
OUTPUT
========================================

Return:
1. Files created
2. Files modified
3. Exact controller integration points
4. Logs showing execution
5. Any assumptions

========================================
FINAL GOAL
========================================

- Notifications sent reliably
- No system breakage
- Clean architecture
- Production-safe implementation