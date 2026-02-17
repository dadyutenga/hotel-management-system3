Snippe Documentation

Get started with Snippe's payment API

Snippe is a payment processing API that enables you to accept payments via mobile money, card, and QR code, and send disbursements to mobile money and bank accounts.
Base URL

https://api.snippe.sh

Authentication

All API requests require authentication using an API key in the Authorization header:

Authorization: Bearer your_api_key_here



# Authentication



All API requests require authentication using an API key in the `Authorization` header.

```http
Authorization: Bearer snp_your_api_key_here
```

***

Getting Your API Key [#getting-your-api-key]

<Steps>
  <Step>
    Log in to Dashboard [#log-in-to-dashboard]

    Go to your [Snippe Dashboard](https://snippe.sh) and sign in to your account.
  </Step>

  <Step>
    Navigate to API Keys [#navigate-to-api-keys]

    Go to **Settings** → **API Keys** in the sidebar.
  </Step>

  <Step>
    Create New Key [#create-new-key]

    Click **Create API Key** and select the scopes you need.
  </Step>

  <Step>
    Copy and Store [#copy-and-store]

    Copy your API key immediately and store it securely.
  </Step>
</Steps>

<Callout type="warn">
  Your API key is only shown once. Store it securely immediately. If you lose
  it, you'll need to create a new one.
</Callout>

***

Example Request [#example-request]

```bash
curl -X GET https://api.snippe.sh/v1/payments \
  -H "Authorization: Bearer snp_your_api_key_here"
```

***

API Key Scopes [#api-key-scopes]

| Scope                 | Description               |
| --------------------- | ------------------------- |
| `collection:read`     | View payments and balance |
| `collection:create`   | Create payment intents    |
| `disbursement:read`   | View payouts              |
| `disbursement:create` | Create payouts            |

<Callout>
  Select only the scopes your application needs. This follows the principle of
  least privilege.
</Callout>

***

Error Responses [#error-responses]

<Tabs items={['Invalid API Key', 'Insufficient Scope']}>
  <Tab value="Invalid API Key">
    **HTTP 401 Unauthorized**

    ```json
    {
      "status": "error",
      "code": 401,
      "error_code": "unauthorized",
      "message": "invalid or missing API key"
    }
    ```

    **Common causes:** API key is missing from the request, API key is malformed or expired, or using test key in production.
  </Tab>

  <Tab value="Insufficient Scope">
    **HTTP 403 Forbidden**

    ```json
    {
      "status": "error",
      "code": 403,
      "error_code": "insufficient_scope",
      "message": "API key does not have required scope: disbursement:create"
    }
    ```

    **Fix:** Create a new API key with the required scope.
  </Tab>
</Tabs>

***

Best Practices [#best-practices]

<Callout type="warn">
  Never expose your API key in client-side code, public repositories, or logs.
</Callout>

Store API keys in environment variables. Use different keys for development and production. Rotate keys periodically. Revoke compromised keys immediately.


for  more  info  please    read the   Read https://docs.snippe.sh/docs/2026-01-25/authentication.mdx,  to  calrify  yourself a s  an  agent  .








# Mobile Money



Collect payments from customers using mobile money. The customer receives a USSD push notification to authorize the payment.

Supported Networks [#supported-networks]

| Network      | Country |
| ------------ | ------- |
| Airtel Money | TZ      |
| M-Pesa       | TZ      |
| Mixx by Yas  | TZ      |
| Halotel      | TZ      |

***

Create Payment [#create-payment]

```http
POST /v1/payments
Authorization: Bearer <api_key>
Content-Type: application/json
Idempotency-Key: <unique_key>
```

Request [#request]

```json
{
  "payment_type": "mobile",
  "details": {
    "amount": 500,
    "currency": "TZS"
  },
  "phone_number": "255781000000",
  "customer": {
    "firstname": "FirstName",
    "lastname": "LastName",
    "email": "customer@email.com"
  },
  "webhook_url": "https://yoursite.com/webhooks/snippe",
  "metadata": {
    "order_id": "ORD-12345"
  }
}
```

Response [#response]

```json
{
  "status": "success",
  "code": 201,
  "data": {
    "amount": {
      "currency": "TZS",
      "value": 500
    },
    "api_version": "2026-01-25",
    "expires_at": "2026-01-25T05:04:54.063993853Z",
    "object": "payment",
    "payment_type": "mobile",
    "reference": "9015c155-9e29-4e8e-8fe6-d5d81553c8e6",
    "status": "pending"
  }
}
```

***

Request Parameters [#request-parameters]

Required Fields [#required-fields]

| Field                | Type    | Description                          |
| -------------------- | ------- | ------------------------------------ |
| `payment_type`       | string  | Must be `mobile`                     |
| `details.amount`     | integer | Amount in smallest currency unit     |
| `details.currency`   | string  | Currency code (`TZS`)                |
| `phone_number`       | string  | Customer phone number (255XXXXXXXXX) |
| `customer.firstname` | string  | Customer first name                  |
| `customer.lastname`  | string  | Customer last name                   |
| `customer.email`     | string  | Customer email                       |

Optional Fields [#optional-fields]

| Field         | Type   | Description                   |
| ------------- | ------ | ----------------------------- |
| `webhook_url` | string | URL for webhook notifications |
| `metadata`    | object | Custom key-value data         |

***

How It Works [#how-it-works]

1. Create a payment intent with the customer's phone number
2. Customer receives a USSD push on their phone
3. Customer enters their PIN to authorize
4. Snippe sends a webhook notification with the result
5. Payment expires after 4 hours if not completed

***

Error Responses [#error-responses]

Validation Error (400) [#validation-error-400]

```json
{
  "status": "error",
  "code": 400,
  "error_code": "validation_error",
  "message": "amount is required"
}
```

Unauthorized (401) [#unauthorized-401]

```json
{
  "status": "error",
  "code": 401,
  "error_code": "unauthorized",
  "message": "invalid or missing API key"
}
```


# Card Payments



Collect payments from customers using credit and debit cards. Customer is redirected to a secure checkout page to complete payment.

Supported Cards [#supported-cards]

* Visa
* Mastercard
* Local debit cards

***

Create Payment [#create-payment]

```http
POST /v1/payments
Authorization: Bearer <api_key>
Content-Type: application/json
Idempotency-Key: <unique_key>
```

Request [#request]

```json
{
  "payment_type": "card",
  "details": {
    "amount": 1000,
    "currency": "TZS",
    "redirect_url": "https://your_domain.com/payment_done",
    "cancel_url": "https://your_domain.com/payment_failed"
  },
  "phone_number": "255781000000",
  "customer": {
    "firstname": "FirstName",
    "lastname": "LastName",
    "email": "customer@email.com",
    "address": "Customer Address",
    "city": "Customer City",
    "state": "DSM",
    "postcode": "14101",
    "country": "TZ"
  },
  "webhook_url": "https://yoursite.com/webhooks/snippe",
  "metadata": {
    "order_id": "ORD-12345"
  }
}
```

Response [#response]

```json
{
  "status": "success",
  "code": 201,
  "data": {
    "amount": {
      "currency": "TZS",
      "value": 1000
    },
    "api_version": "2026-01-25",
    "expires_at": "2026-01-25T01:32:10.476693917Z",
    "object": "payment",
    "payment_qr_code": "000201010212041552545429990002026390014tz.go.bot.tips...",
    "payment_token": "63891931",
    "payment_type": "card",
    "payment_url": "https://tz.selcom.online/paymentgw/checkout/...",
    "reference": "2e0bcc5f-92ca-44f9-8c1b-4d2966d9921f",
    "status": "pending"
  }
}
```

***

Request Parameters [#request-parameters]

Required Fields [#required-fields]

| Field                  | Type    | Description                              |
| ---------------------- | ------- | ---------------------------------------- |
| `payment_type`         | string  | Must be `card`                           |
| `details.amount`       | integer | Amount in smallest currency unit         |
| `details.currency`     | string  | Currency code (`TZS`)                    |
| `details.redirect_url` | string  | URL to redirect after successful payment |
| `details.cancel_url`   | string  | URL to redirect on cancel/failure        |
| `customer.firstname`   | string  | Customer first name                      |
| `customer.lastname`    | string  | Customer last name                       |
| `customer.email`       | string  | Customer email                           |
| `customer.address`     | string  | Billing address                          |
| `customer.city`        | string  | Billing city                             |
| `customer.state`       | string  | Billing state/region                     |
| `customer.postcode`    | string  | Billing postal code                      |
| `customer.country`     | string  | Country code (ISO 3166-1 alpha-2)        |

Optional Fields [#optional-fields]

| Field          | Type   | Description                   |
| -------------- | ------ | ----------------------------- |
| `phone_number` | string | Customer phone number         |
| `webhook_url`  | string | URL for webhook notifications |
| `metadata`     | object | Custom key-value data         |

***

Response Fields [#response-fields]

| Field             | Description                                    |
| ----------------- | ---------------------------------------------- |
| `payment_url`     | URL to redirect customer for card entry        |
| `payment_token`   | Payment token for reference                    |
| `payment_qr_code` | QR code data (can also be used for QR payment) |
| `reference`       | Unique payment reference                       |
| `expires_at`      | Payment expiration time                        |

***

How It Works [#how-it-works]

1. Create a payment intent with customer billing details
2. Redirect customer to the `payment_url`
3. Customer enters card details on secure checkout page
4. Customer is redirected to your `redirect_url` or `cancel_url`
5. Snippe sends a webhook notification with the result


# Dynamic QR



Generate QR codes that customers scan with their mobile money app to complete payment.

***

Create Payment [#create-payment]

```http
POST /v1/payments
Authorization: Bearer <api_key>
Content-Type: application/json
Idempotency-Key: <unique_key>
```

Request [#request]

```json
{
  "payment_type": "dynamic-qr",
  "details": {
    "amount": 500,
    "currency": "TZS",
    "redirect_url": "https://your_domain.com/payment_done",
    "cancel_url": "https://your_domain.com/payment_failed"
  },
  "phone_number": "255781000000",
  "customer": {
    "firstname": "FirstName",
    "lastname": "LastName",
    "email": "customer@email.com"
  },
  "webhook_url": "https://yoursite.com/webhooks/snippe",
  "metadata": {
    "order_id": "ORD-12345"
  }
}
```

Response [#response]

```json
{
  "status": "success",
  "code": 201,
  "data": {
    "amount": {
      "currency": "TZS",
      "value": 500
    },
    "api_version": "2026-01-25",
    "expires_at": "2026-01-25T04:47:50.159178853Z",
    "object": "payment",
    "payment_qr_code": "000201010212041552545429990002026390014tz.go.bot.tips01050399802086389040052045999530383454035005802TZ5909NEUROTECH6003DSM610512345622401086389040003086389040081500012tz.co.selcom0130 https://selcom.link/addbbff1 630401F1",
    "payment_token": "63890400",
    "payment_type": "dynamic-qr",
    "payment_url": "https://tz.selcom.online/paymentgw/checkout/...",
    "reference": "6a490816-799b-4fc9-b9b6-2ec67c54e17e",
    "status": "pending"
  }
}
```

***

Request Parameters [#request-parameters]

Required Fields [#required-fields]

| Field              | Type    | Description                      |
| ------------------ | ------- | -------------------------------- |
| `payment_type`     | string  | Must be `dynamic-qr`             |
| `details.amount`   | integer | Amount in smallest currency unit |
| `details.currency` | string  | Currency code (`TZS`)            |

Optional Fields [#optional-fields]

| Field                  | Type   | Description                   |
| ---------------------- | ------ | ----------------------------- |
| `phone_number`         | string | Customer phone number         |
| `customer.firstname`   | string | Customer first name           |
| `customer.lastname`    | string | Customer last name            |
| `customer.email`       | string | Customer email                |
| `details.redirect_url` | string | URL to redirect after success |
| `details.cancel_url`   | string | URL to redirect on cancel     |
| `webhook_url`          | string | URL for webhook notifications |
| `metadata`             | object | Custom key-value data         |

***

Response Fields [#response-fields]

| Field             | Description                                     |
| ----------------- | ----------------------------------------------- |
| `payment_qr_code` | QR code data string to display to customer      |
| `payment_url`     | Hosted payment page URL (alternative to QR)     |
| `payment_token`   | Payment token for reference                     |
| `reference`       | Unique payment reference                        |
| `expires_at`      | Payment expiration time (4 hours from creation) |

***

How It Works [#how-it-works]

1. Create a payment intent
2. Display the QR code from `payment_qr_code` to the customer (render as QR image)
3. Alternatively, redirect customer to `payment_url`
4. Customer scans the QR code with their mobile money app
5. Customer confirms payment in their app
6. Snippe sends a webhook notification with the result


# Trigger Push Payment



Trigger or retry a USSD push notification for a pending payment. Use this when the customer missed the initial push or it timed out.

***

Trigger Push [#trigger-push]

```http
POST /v1/payments/{reference}/push
Authorization: Bearer <api_key>
Content-Type: application/json
```

Path Parameter [#path-parameter]

| Parameter   | Type   | Description                                  |
| ----------- | ------ | -------------------------------------------- |
| `reference` | string | Payment reference or payment token (from QR) |

Request Body (Optional) [#request-body-optional]

```json
{
  "phone": "+255712345678"
}
```

| Field   | Type   | Required | Description                                                     |
| ------- | ------ | -------- | --------------------------------------------------------------- |
| `phone` | string | No       | Phone number to send push to (defaults to original payer phone) |

Response [#response]

```json
{
  "status": 200,
  "message": "USSD push sent successfully",
  "data": {
    "reference": "pi_a1b2c3d4e5f6",
    "external_reference": "SEL123456789",
    "status": "pending",
    "phone": "+255712345678"
  }
}
```

***

Error Responses [#error-responses]

| Status | Code              | Description                            |
| ------ | ----------------- | -------------------------------------- |
| 400    | `invalid_request` | Payment is not in pending status       |
| 403    | `forbidden`       | Payment doesn't belong to your account |
| 404    | `not_found`       | Payment not found                      |

***

Examples [#examples]

Retry push by reference [#retry-push-by-reference]

```bash
curl -X POST https://api.snippe.sh/v1/payments/pi_a1b2c3d4e5f6/push \
  -H "Authorization: Bearer sk_live_xxx"
```

Trigger push for QR payment using payment token [#trigger-push-for-qr-payment-using-payment-token]

```bash
curl -X POST https://api.snippe.sh/v1/payments/63877176/push \
  -H "Authorization: Bearer sk_live_xxx"
```

Send push to different phone number [#send-push-to-different-phone-number]

```bash
curl -X POST https://api.snippe.sh/v1/payments/pi_a1b2c3d4e5f6/push \
  -H "Authorization: Bearer sk_live_xxx" \
  -H "Content-Type: application/json" \
  -d '{"phone": "+255787654321"}'
```



# Webhooks



Snippe sends webhooks to your specified URL when payment or payout status changes. Use webhooks to update your system in real-time.

<Callout>
  Always provide a `webhook_url` when creating payments or payouts to receive
  status notifications.
</Callout>

***

Webhook Headers [#webhook-headers]

All webhooks include the following headers:

| Header                | Description                            |
| --------------------- | -------------------------------------- |
| `Content-Type`        | `application/json`                     |
| `User-Agent`          | `Snipe-Webhook/1.0`                    |
| `X-Webhook-Event`     | Event type (e.g., `payment.completed`) |
| `X-Webhook-Timestamp` | Unix timestamp when webhook was sent   |
| `X-Webhook-Signature` | HMAC-SHA256 signature for verification |

***

Event Types [#event-types]

<Tabs items={["Payment Events", "Payout Events"]}>
  <Tab value="Payment Events">
    \| Event | Description | | ----- | ----------- | | `payment.completed` |
    Payment successfully completed | | `payment.failed` | Payment failed or was
    declined |
  </Tab>

  <Tab value="Payout Events">
    \| Event | Description | | ----- | ----------- | | `payout.completed` |
    Payout successfully sent | | `payout.failed` | Payout failed |
  </Tab>
</Tabs>

***

Payment Events [#payment-events]

payment.completed [#paymentcompleted]

Sent when a payment is successfully completed.

<Callout>
  **Trigger:** Customer completes payment via mobile money, card, or QR code.

  **Action:** Mark the order as paid, deliver the product/service.
</Callout>

```json
{
  "id": "evt_427edf89c5c8c02a2301254e",
  "type": "payment.completed",
  "api_version": "2026-01-25",
  "created_at": "2026-01-25T01:05:17.834276191Z",
  "data": {
    "reference": "9015c155-9e29-4e8e-8fe6-d5d81553c8e6",
    "external_reference": "S20388385575",
    "status": "completed",
    "amount": {
      "value": 500,
      "currency": "TZS"
    },
    "settlement": {
      "gross": { "value": 500, "currency": "TZS" },
      "fees": { "value": 9, "currency": "TZS" },
      "net": { "value": 491, "currency": "TZS" }
    },
    "channel": {
      "type": "mobile_money",
      "provider": "airtel"
    },
    "customer": {
      "phone": "+255781000000",
      "name": "Customer Name",
      "email": "customer@email.com"
    },
    "metadata": {
      "order_id": "ORD-12345",
      "product": "Premium Plan"
    },
    "completed_at": "2026-01-25T01:05:16.8303Z"
  }
}
```

***

payment.failed [#paymentfailed]

Sent when a payment fails.

<Callout type="warn">
  **Trigger:** Payment was declined, insufficient funds, or other failure.

  **Action:** Notify the customer, offer retry option.
</Callout>

```json
{
  "id": "evt_a1b2c3d4e5f6g7h8i9j0k1l2",
  "type": "payment.failed",
  "api_version": "2026-01-25",
  "created_at": "2026-01-25T01:05:17.834276191Z",
  "data": {
    "reference": "9015c155-9e29-4e8e-8fe6-d5d81553c8e6",
    "external_reference": "S20388385575",
    "status": "failed",
    "amount": {
      "value": 500,
      "currency": "TZS"
    },
    "settlement": {
      "gross": { "value": 500, "currency": "TZS" },
      "fees": { "value": 9, "currency": "TZS" },
      "net": { "value": 491, "currency": "TZS" }
    },
    "channel": {
      "type": "mobile_money",
      "provider": "airtel"
    },
    "customer": {
      "phone": "+255781000000",
      "name": "Customer Name",
      "email": "customer@email.com"
    },
    "metadata": {
      "order_id": "ORD-12345"
    },
    "failure_reason": "Something went wrong",
    "completed_at": "2026-01-25T01:05:16.8303Z"
  }
}
```

***

Payout Events [#payout-events]

payout.completed [#payoutcompleted]

Sent when a payout is successfully completed.

<Callout>
  **Trigger:** Funds have been successfully sent to the recipient.

  **Action:** Update your records, notify sender of successful transfer.
</Callout>

```json
{
  "id": "evt_a1b2c3d4e5f6g7h8i9j0k1l2",
  "type": "payout.completed",
  "api_version": "2026-01-25",
  "created_at": "2026-01-25T10:30:00Z",
  "data": {
    "reference": "PAY-ABC123XYZ",
    "status": "completed",
    "amount": {
      "value": 50000,
      "currency": "TZS"
    },
    "settlement": {
      "gross": { "value": 50000, "currency": "TZS" },
      "fees": { "value": 500, "currency": "TZS" },
      "net": { "value": 50500, "currency": "TZS" }
    },
    "channel": {
      "type": "mobile_money",
      "provider": "MPESA"
    },
    "customer": {
      "phone": "255712345678",
      "name": "Customer Name"
    },
    "metadata": {
      "order_id": "12345"
    },
    "completed_at": "2026-01-25T10:30:00Z"
  }
}
```

***

payout.failed [#payoutfailed]

Sent when a payout fails.

<Callout type="error">
  **Trigger:** Payout could not be completed (invalid recipient, network error, etc.).

  **Action:** Funds are returned to balance, notify sender of failure.
</Callout>

```json
{
  "id": "evt_a1b2c3d4e5f6g7h8i9j0k1l2",
  "type": "payout.failed",
  "api_version": "2026-01-25",
  "created_at": "2026-01-25T10:30:00Z",
  "data": {
    "reference": "PAY-ABC123XYZ",
    "status": "failed",
    "amount": {
      "value": 50000,
      "currency": "TZS"
    },
    "settlement": {
      "gross": { "value": 50000, "currency": "TZS" },
      "fees": { "value": 500, "currency": "TZS" },
      "net": { "value": 50500, "currency": "TZS" }
    },
    "channel": {
      "type": "mobile_money",
      "provider": "MPESA"
    },
    "customer": {
      "phone": "255712345678",
      "name": "Customer Name"
    },
    "metadata": {
      "order_id": "12345"
    },
    "failure_reason": "Recipient phone number is invalid"
  }
}
```

***

Webhook Signature Verification [#webhook-signature-verification]

<Callout type="warn">
  Always verify webhook signatures in production to ensure requests are from
  Snippe.
</Callout>

Validate the `X-Webhook-Signature` header using HMAC-SHA256:

<Tabs items={['Node.js', 'Python', 'Go']}>
  <Tab value="Node.js">
    ```javascript
    const crypto = require('crypto');

    function verifyWebhookSignature(payload, signature, secret) {
    const expectedSignature = crypto
    .createHmac('sha256', secret)
    .update(payload)
    .digest('hex');

    return crypto.timingSafeEqual(
    Buffer.from(signature),
    Buffer.from(expectedSignature)
    );
    }

    ```
  </Tab>

  <Tab value="Python">
    ```python
    import hmac
    import hashlib

    def verify_webhook_signature(payload, signature, secret):
        expected = hmac.new(
            secret.encode(),
            payload.encode(),
            hashlib.sha256
        ).hexdigest()
        return hmac.compare_digest(signature, expected)
    ```
  </Tab>

  <Tab value="Go">
    ```go
    import (
        "crypto/hmac"
        "crypto/sha256"
        "encoding/hex"
    )

    func verifyWebhookSignature(payload, signature, secret string) bool {
    mac := hmac.New(sha256.New, []byte(secret))
    mac.Write([]byte(payload))
    expected := hex.EncodeToString(mac.Sum(nil))
    return hmac.Equal([]byte(signature), []byte(expected))
    }

    ```
  </Tab>
</Tabs>

***

Best Practices [#best-practices]

<Steps>
  <Step>
    Respond Quickly [#respond-quickly]

    Return a `2xx` status code within 30 seconds. Process webhooks asynchronously.
  </Step>

  <Step>
    Handle Duplicates [#handle-duplicates]

    Use the event `id` to deduplicate. You may receive the same event multiple times.
  </Step>

  <Step>
    Verify Signatures [#verify-signatures]

    Always validate webhook signatures in production environments.
  </Step>

  <Step>
    Implement Retries [#implement-retries]

    Snippe retries failed webhooks with exponential backoff. Ensure your endpoint is idempotent.
  </Step>
</Steps>

<Callout type="error">
  If your endpoint consistently fails, webhooks may be disabled. Monitor your webhook endpoint health.
</Callout>

```
```
# Error Handling



All API errors follow a consistent format to help you handle them gracefully.

***

Response Format [#response-format]

<Tabs items={['Success', 'Error']}>
  <Tab value="Success">
    ```json
    {
      "status": "success",
      "code": 200,
      "data": {
        // Response data
      }
    }
    ```
  </Tab>

  <Tab value="Error">
    ```json
    {
      "status": "error",
      "code": 400,
      "error_code": "validation_error",
      "message": "amount is required"
    }
    ```
  </Tab>
</Tabs>

***

HTTP Status Codes [#http-status-codes]

| Code | Name                  | Description                                         |
| ---- | --------------------- | --------------------------------------------------- |
| 200  | OK                    | Request successful                                  |
| 201  | Created               | Resource created successfully                       |
| 400  | Bad Request           | Invalid request (validation errors, malformed JSON) |
| 401  | Unauthorized          | Authentication required or failed                   |
| 403  | Forbidden             | Authenticated but not authorized                    |
| 404  | Not Found             | Resource doesn't exist                              |
| 409  | Conflict              | Resource already exists or state conflict           |
| 422  | Unprocessable Entity  | Idempotency key mismatch                            |
| 429  | Too Many Requests     | Rate limit exceeded                                 |
| 500  | Internal Server Error | Server-side error                                   |
| 503  | Service Unavailable   | External service temporarily down                   |

***

Common Error Scenarios [#common-error-scenarios]

<Tabs items={['Authentication', 'Validation', 'Payment', 'Rate Limit']}>
  <Tab value="Authentication">
    Invalid API Key (401) [#invalid-api-key-401]

    ```json
    {
      "status": "error",
      "code": 401,
      "error_code": "unauthorized",
      "message": "invalid or missing API key"
    }
    ```

    <Callout type="error">
      **Fix:** Check that your API key is correct and included in the
      `Authorization` header.
    </Callout>

    Insufficient Scope (403) [#insufficient-scope-403]

    ```json
    {
      "status": "error",
      "code": 403,
      "error_code": "insufficient_scope",
      "message": "API key does not have required scope: disbursement:create"
    }
    ```

    <Callout type="warn">
      **Fix:** Create a new API key with the required scope from the Dashboard.
    </Callout>
  </Tab>

  <Tab value="Validation">
    Missing Required Fields (400) [#missing-required-fields-400]

    ```json
    {
      "status": "error",
      "code": 400,
      "error_code": "validation_error",
      "message": "amount is required"
    }
    ```

    Invalid Phone Number (400) [#invalid-phone-number-400]

    ```json
    {
      "status": "error",
      "code": 400,
      "error_code": "validation_error",
      "message": "phone_number must be a valid phone number"
    }
    ```

    <Callout>
      **Fix:** Use format `255XXXXXXXXX` or `+255XXXXXXXXX` for Tanzanian numbers.
    </Callout>

    Amount Out of Range (400) [#amount-out-of-range-400]

    ```json
    {
      "status": "error",
      "code": 400,
      "error_code": "validation_error",
      "message": "amount 100 is below minimum of 500"
    }
    ```
  </Tab>

  <Tab value="Payment">
    Payment Not Found (404) [#payment-not-found-404]

    ```json
    {
      "status": "error",
      "code": 404,
      "error_code": "not_found",
      "message": "payment not found"
    }
    ```

    Insufficient Balance (500) [#insufficient-balance-500]

    ```json
    {
      "status": "error",
      "code": 500,
      "error_code": "payment_failed",
      "message": "insufficient balance: available 5000, required 6500"
    }
    ```

    <Callout type="error">
      **Fix:** Top up your account balance before creating payouts.
    </Callout>

    Idempotency Conflict (422) [#idempotency-conflict-422]

    ```json
    {
      "status": "error",
      "code": 422,
      "error_code": "validation_error",
      "message": "idempotency key already used with different request body"
    }
    ```

    <Callout type="warn">
      **Fix:** Use a unique idempotency key for each unique request.
    </Callout>
  </Tab>

  <Tab value="Rate Limit">
    Rate Limit Exceeded (429) [#rate-limit-exceeded-429]

    ```json
    {
      "status": "error",
      "code": 429,
      "error_code": "rate_limit_exceeded",
      "message": "Too many requests"
    }
    ```

    <Callout>
      **Fix:** Implement exponential backoff and respect the `X-Ratelimit-Reset` header.
    </Callout>
  </Tab>
</Tabs>

***

Error Codes Reference [#error-codes-reference]

Authentication Errors [#authentication-errors]

| Code                 | Description                  |
| -------------------- | ---------------------------- |
| `unauthorized`       | Invalid or missing API key   |
| `insufficient_scope` | API key lacks required scope |

Validation Errors [#validation-errors]

| Code               | Description                |
| ------------------ | -------------------------- |
| `validation_error` | One or more fields invalid |

Resource Errors [#resource-errors]

| Code        | Description             |
| ----------- | ----------------------- |
| `not_found` | Resource doesn't exist  |
| `conflict`  | Resource state conflict |

Payment Errors [#payment-errors]

| Code             | Description              |
| ---------------- | ------------------------ |
| `payment_failed` | Payment processing error |

***

Validation Rules [#validation-rules]

<Tabs items={['Amounts', 'Phone Numbers', 'URLs']}>
  <Tab value="Amounts">
    | Rule    | Value                                   |
    | ------- | --------------------------------------- |
    | Minimum | 500 TZS (payments), 5,000 TZS (payouts) |
    | Maximum | Varies by account type                  |
    | Format  | Integer (smallest currency unit)        |
  </Tab>

  <Tab value="Phone Numbers">
    | Format            | Example         |
    | ----------------- | --------------- |
    | With country code | `+255712345678` |
    | Without plus      | `255712345678`  |

    <Callout>
      Phone numbers must be valid Tanzanian mobile numbers.
    </Callout>
  </Tab>

  <Tab value="URLs">
    * Must be valid HTTPS URLs
    * Maximum 500 characters
    * Used for: `webhook_url`, `redirect_url`, `cancel_url`
  </Tab>
</Tabs>

***

Best Practices [#best-practices]

<Steps>
  <Step>
    Log All Errors Log the full error response including error_code and [#log-all-errors-log-the-full-error-response-including-error_code-and]

    `message` for debugging.
  </Step>

  <Step>
    Handle Gracefully Show user-friendly messages to customers, not raw API [#handle-gracefully-show-user-friendly-messages-to-customers-not-raw-api]

    errors.
  </Step>

  <Step>
    Implement Retries For 5xx errors and network failures, implement [#implement-retries-for-5xx-errors-and-network-failures-implement]

    exponential backoff.
  </Step>

  <Step>
    Monitor Error Rates Track error rates by code to identify integration [#monitor-error-rates-track-error-rates-by-code-to-identify-integration]

    issues early.
  </Step>
</Steps>


Best Practices
Log All Errors Log the full error response including error_code and

message for debugging.
Handle Gracefully Show user-friendly messages to customers, not raw API

errors.
Implement Retries For 5xx errors and network failures, implement

exponential backoff.
Monitor Error Rates Track error rates by code to identify integration

issues early.