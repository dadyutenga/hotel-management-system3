Task: Admin Settings for SMS Provider, Email Config, and Snipe Payments

Objective:
Add a settings page feature that allows an admin to store and manage SMS provider configuration, email configuration, and Snipe payment options. Preserve existing settings UI patterns and backend conventions.

This task must:
- Add persistent settings for SMS provider configuration
- Add persistent settings for email configuration
- Add persistent settings for Snipe payment options
- Provide a secure admin-only UI for managing these settings
- Store settings in the existing configuration or settings storage pattern
- Keep UI styling and translations consistent (EN/SW)

DO NOT redesign the settings page.
DO NOT break existing settings flows or validation.
EXTEND existing controller, service, model, and view conventions.

---

### 1) Discovery First: Reuse Existing Settings Patterns

Before implementation, identify:
- Where settings are stored (database table, config, .env backed, or settings service)
- How settings are displayed and saved in the current settings page
- Existing validation patterns for settings updates
- Any encryption or masking patterns for sensitive values
- Any audit logging for settings changes

Implementation rule:
- Follow existing settings architecture and naming
- Avoid introducing parallel settings storage or new config systems

---

### 2) SMS Provider Settings

Required behavior:
- Allow admin to select an SMS provider (based on existing providers or a simple key)
- Store provider-specific credentials and endpoints (e.g., API key, sender ID, base URL)
- Validate required fields by provider
- Mask secrets in the UI on edit (show placeholders, not raw secrets)
- Persist values to the settings storage

Suggested fields (adjust to existing provider support):
- sms_provider_key (string)
- sms_sender_id (string)
- sms_api_key (string, secret)
- sms_base_url (string, url)
- sms_is_enabled (boolean)

---

### 3) Email Configuration Settings

Required behavior:
- Allow admin to store email service configuration
- Support typical SMTP fields and system defaults
- Validate required fields and formats
- Mask secrets in UI
- Persist values to the settings storage

Suggested fields:
- mail_driver (string)
- mail_host (string)
- mail_port (integer)
- mail_username (string)
- mail_password (string, secret)
- mail_encryption (string: tls/ssl/none)
- mail_from_address (string, email)
- mail_from_name (string)
- mail_is_enabled (boolean)

---

### 4) Snipe Payment Settings

Required behavior:
- Allow admin to enable/disable Snipe payment option
- Store Snipe provider keys and endpoints
- Validate required fields for enabled state
- Mask secrets in UI
- Persist values to the settings storage

Suggested fields (align with current payment naming):
- snipe_is_enabled (boolean)
- snipe_api_key (string, secret)
- snipe_api_secret (string, secret)
- snipe_base_url (string, url)
- snipe_webhook_secret (string, secret)

---

### 5) Settings UI Integration

Required behavior:
- Add a new section in the settings page for SMS, Email, and Snipe
- Use existing cards, tabs, or form layout patterns
- Keep translations for labels and helper text
- Show validation errors inline
- Provide a single save action or per-section save consistent with existing UI

UI rule:
- Preserve design tokens, spacing, and components
- Do not introduce new layout frameworks

---

### 6) Security and Access Control

Required behavior:
- Restrict settings access to admin role
- Validate requests server-side
- Encrypt secrets at rest if current pattern does so
- Log changes with actor, timestamp, and section changed

Important:
- Do not expose secrets in responses or logs
- Do not allow non-admin users to access settings routes

---

### 7) Persistence and Configuration Sync

Required behavior:
- Ensure settings are stored consistently with existing approach
- If settings affect runtime configuration, ensure a safe sync or cache refresh step
- Avoid direct writes to .env unless that is already the project pattern

---

### 8) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Admin can open settings page and see SMS, Email, and Snipe sections
2) Admin can save valid SMS provider settings
3) Admin can save valid email configuration settings
4) Admin can enable Snipe and save required keys
5) Validation prevents incomplete or invalid configurations
6) Secrets are masked when editing existing settings
7) Non-admins cannot access or update settings
8) Settings persist and are reflected on reload
9) Audit/log entries are created for changes
10) Existing settings features remain unchanged

---

Expected Outcome:

- Admin can manage SMS provider settings in the settings page
- Admin can manage email configuration settings in the settings page
- Admin can manage Snipe payment settings in the settings page
- All sensitive data is stored securely
- Role-based access control and audit logging are preserved
- UI remains consistent with existing settings styling

Priority:
HIGH - Admin configuration and integrations

Notes:
- Keep changes minimal and consistent with current settings architecture
- Prefer reusing existing validation and storage patterns
- Do not introduce new settings storage unless explicitly approved
