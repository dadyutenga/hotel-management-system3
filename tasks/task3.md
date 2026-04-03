You are a Laravel cleanup engineer working in production.

========================================
OBJECTIVE
========================================
Completely remove ClickPesa integration without breaking payment system.

========================================
STEP 1: SEARCH ALL REFERENCES
========================================

Search:
- "clickpesa"
- "ClickPesa"
- API endpoints
- config keys

========================================
STEP 2: REMOVE COMPONENTS
========================================

Remove:
- Services
- Controllers
- Config files
- ENV variables

========================================
STEP 3: REPLACE LOGIC
========================================

Replace all ClickPesa usage with:
- PaymentService

========================================
STEP 4: CLEAN DATABASE
========================================

- Remove provider references if needed
- Keep transaction history

========================================
STEP 5: VERIFY
========================================

- No broken imports
- No missing classes
- Payment still works

========================================
OUTPUT
========================================

- Files removed
- Files updated
- Confirmation no references remain