Task: Remove MRK Hotel Branding and Replace with Hotel Management System

Objective:
Replace all "MRK Hotel & Resort" branding and references with generic "Hotel Management System" or "Hotel Management" terminology throughout the entire codebase, while preserving the application structure and functionality.

This task must:
- Remove all "MRK Hotel", "MRK", and "MRK Hotel & Resort" references from views, meta tags, and labels
- Replace with "Hotel Management System" or "Hotel Management" as appropriate
- Update email addresses and contact information that reference MRK
- Update meta descriptions and page titles consistently
- Keep all styling, translations (EN/SW), and layout intact
- Ensure no broken links or missing alt text

DO NOT redesign the application structure.
DO NOT break authentication, routing, or functional flows.
EXTEND existing view naming, translation conventions, and component patterns.

---

### 1) Discovery First: Identify All MRK References

Affected areas identified:
- Welcome/public pages (welcome.blade.php, about, features, pricing, contact, booking)
- Authentication pages (login, forgot-password, reset-password)
- Internal pages (laundry, booking charges, dashboards)
- Email addresses (reservations@mrkhotel.com, concierge@mrkhotel.com)
- Meta tags and page titles (across 70+ locations)
- Header/footer partials and navigation
- Layout defaults

Implementation rule:
- Follow the project's existing naming conventions for titles and descriptions
- Preserve translation keys where they already exist (e.g., keep __('dashboard.admin_title'))
- Update only the branding-specific text, not the framework

---

### 2) Replace MRK Hotel Branding Consistently

Required replacements:
- "MRK Hotel & Resort" → "Hotel Management System" (in meta descriptions and formal contexts)
- "MRK Hotel" → "Hotel Management System" (in page titles and short references)
- "MRK Experience" → "Hotel Experience" or similar alternative
- "MRK Hospitality" → "Hospitality Services"
- "MRK family" → Keep minimal, or replace with "our hotel community"

Rules:
- Preserve context: formal descriptions use "Hotel Management System", casual contexts may use "Hotel" only
- Ensure all page titles remain descriptive and SEO-friendly
- Meta descriptions should reflect the new branding without losing meaning

---

### 3) Update Contact and Email References

Required behavior:
- Replace "reservations@mrkhotel.com" with a generic placeholder (e.g., "reservations@hotel-system.local" or "reservations@hotel.local") OR provide a configuration-driven approach
- Replace "concierge@mrkhotel.com" similarly
- Preserve all footer, contact form, and business contact structure
- Keep the structure for future configuration

Important:
- If email addresses should be configurable, create environment-based references (e.g., use config/app.php or .env)
- Otherwise, use a generic/placeholder domain that makes sense for a hotel system

---

### 4) Update Page Titles and Meta Tags

Update all @section('title') statements:
- Page specific titles should follow pattern: "Page Name - Hotel Management System"
- Meta descriptions should reference "hotel management system" naturally
- Preserve any existing translation calls

Pattern examples:
- Old: "MRK Hotel & Resort - Premium Accommodation in East Africa"
- New: "Hotel Management System - Premium Hotel Operations"
- Old: "Pricing - MRK Hotel Management System"
- New: "Pricing Plans - Hotel Management System"

UI rule:
- Keep SEO-friendly; descriptions should be meaningful
- Maintain brand consistency without MRK

---

### 5) Update Welcome/Public Pages

Update all public-facing pages:
- welcome.blade.php: Replace hero text, descriptions, and brand story appropriately
- about.blade.php: Update company story to reflect generic hotel system positioning
- features.blade.php: Keep feature descriptions; update branding references
- pricing.blade.php: Update page title and description
- contact.blade.php: Update all contact blocks and email references
- booking.blade.php & booking-confirmation.blade.php: Update confirmation messaging and branding

UI rule:
- Preserve styling, layout, and structure
- Keep alt text meaningful (e.g., "Hotel Management System Interface")
- Update language naturally without leaving placeholder text

---

### 6) Update Internal Pages and Dashboards

Update authenticated area pages:
- laundry-orders (index, create, edit, show)
- laundry-items (index, create, edit)
- booking-charges (index)
- dashboards (admin, front-desk)

Pattern:
- Titles should follow: "Page Name - Hotel Management System"
- Keep all functional logic and routing unchanged

---

### 7) Update Shared Components

Update reusable components:
- partials/public-header.blade.php: Replace MRK Hotel logo alt text and branding
- partials/public-footer.blade.php: Update copyright year, branding text, and email
- layouts/app.blade.php: Update default title and meta tags

Rules:
- Preserve component responsiveness and styling
- Keep image alt attributes meaningful
- Update copyright year reference if hardcoded

---

### 8) Alt Text and Accessibility

Ensure meaningful alt attributes:
- "MRK Hotel Exterior" → "Hotel Building Exterior"
- "MRK Hotel Lobby" → "Hotel Lobby Interior"
- "MRK Hotel Reception" → "Hotel Reception Desk"

Rule:
- Keep alt text descriptive and accessible without brand references

---

### 9) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Public welcome page displays "Hotel Management System" branding consistently
2) All page titles show "Hotel Management System" or appropriate variant
3) Meta descriptions are readable and free of MRK references
4) Contact page shows updated email addresses or placeholders correctly
5) About page reads naturally with updated branding
6) Authentication pages (login, forgot-password, reset-password) display updated titles
7) Laundry, booking, and dashboard pages all show updated page titles
8) Header and footer partials display updated branding and copyright
9) No broken alt text; all image alt attributes are meaningful
10) All translations (EN/SW) remain intact and functioning
11) No console errors related to missing or malformed branding elements
12) Email links in contact form and footer link correctly to updated addresses

---

Expected Outcome:

- All "MRK Hotel" branding removed from codebase
- Consistent "Hotel Management System" branding in place
- All public and internal pages updated with new branding
- Page titles, meta descriptions, and alt text all reflect new branding
- Application functionality remains completely stable
- Styling, translations, and routing unchanged

Priority:
HIGH - Complete rebranding for cohesive system identity

Notes:
- Keep changes minimal and localized to branding text only
- Reuse existing translation keys where applicable
- For email addresses, consider using environment-based config for flexibility
- Do not modify application logic, routes, or controllers
