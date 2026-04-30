# Illuminate\Database\QueryException - Internal Server Error

SQLSTATE[HY000]: General error: 1 no such column: approved_by (Connection: sqlite, Database: /home/dadi-utenga/projects/hotel-management-system/database/database.sqlite, SQL: update "goods_received_notes" set "status" = approved, "accounting_journal_entry_id" = 019dcac9-4554-7146-a3a4-aa232ede51cf, "approved_by" = 9638fcd4-d29e-4a8a-af67-dc1192692b1d, "approved_at" = 2026-04-26 17:14:41, "rejected_by" = ?, "rejected_at" = ?, "updated_at" = 2026-04-26 17:14:41 where "id" = f5e23813-53f4-491b-9210-52be605bec4e)

PHP 8.4.20
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:838
1 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:794
2 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:597
3 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:549
4 - vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php:4234
5 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php:1266
6 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php:1316
7 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php:1233
8 - app/Services/ProcurementIntegrationService.php:140
9 - vendor/laravel/framework/src/Illuminate/Database/Concerns/ManagesTransactions.php:35
10 - vendor/laravel/framework/src/Illuminate/Database/DatabaseManager.php:491
11 - vendor/laravel/framework/src/Illuminate/Support/Facades/Facade.php:363
12 - app/Services/ProcurementIntegrationService.php:68
13 - app/Http/Controllers/Procurement/GoodsReceivedNoteController.php:284
14 - vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php:46
15 - vendor/laravel/framework/src/Illuminate/Routing/Route.php:265
16 - vendor/laravel/framework/src/Illuminate/Routing/Route.php:211
17 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:822
18 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
19 - app/Http/Middleware/RoleMiddleware.php:37
20 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
21 - app/Http/Middleware/SetLocale.php:31
22 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
23 - vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php:50
24 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
25 - vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php:63
26 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
27 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php:87
28 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
29 - vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php:48
30 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
31 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:120
32 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
33 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
34 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
35 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
36 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
37 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
38 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
39 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
40 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
41 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
42 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
43 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
44 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
45 - app/Http/Middleware/SecurityHeaders.php:18
46 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
47 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
48 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
49 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
50 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
51 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
52 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
53 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
54 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
55 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
56 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
57 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
58 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
59 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
60 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
61 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
62 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
63 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:26
64 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
65 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
66 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
67 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
68 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
69 - public/index.php:20
70 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23

## Request

POST /procurement/grn/f5e23813-53f4-491b-9210-52be605bec4e/approve

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/procurement/grn/f5e23813-53f4-491b-9210-52be605bec4e
* **content-type**: application/x-www-form-urlencoded
* **content-length**: 47
* **origin**: http://127.0.0.1:8000
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6InJBNWZ3SU0zY2ovOG5RSUkwU1hLNlE9PSIsInZhbHVlIjoiMnJGTys1QUtSR0NINzVyOGFNK3hWRGlSdk1pZ2xLc2VhY3ZGV005VUdRMFhMU09nUHQ1WmcxeEwwL3pwaUZKYXdmVkU3Q1JKRmR5Z1Z5YkNtdkdJTXVGU3B1Rlh6eHpGdnZMSlZDcC95aW5IOXFRcG1SZjRXaGwyZDE4Y2pSYWgiLCJtYWMiOiJmNTQ4NzA0YTE3NjZkYjM2ZDFhNTg3MWIyZTFmMTlhZDYzZjFlOTQ4YTM2ZGFkMjE3MjNkNGM3MTY0ZjE3ZmI4IiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6IlpvQjIwOUxseUwzTlRjN3RwYm9ueVE9PSIsInZhbHVlIjoiQy96d3N4QnhoZUVNdWE1aDNUSjRSTElvSVMxZkhQVHhNVWVnWXZ3RGxjTHpWRUNSTndRK0FnWHpYZ1RPSXBiYnowb29Xa0ZSVFVZdTJON2JZTnl4RU85QjRLaUpTMEE1YTZjZWEwblpyRWJJVWRDTHlZRUJjTkpnUFBDYVlLTUkiLCJtYWMiOiJhM2JiMWFkOGM4ZTE0NTY1YTkzNjljNDBkODhlNDNjZjdiMDBjNWNhNmRhY2Q4NzlkY2Q1ZWNmZTczOGE4MjY0IiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Procurement\GoodsReceivedNoteController@approve
route name: procurement.grn.approve
middleware: web, auth, role:manager

## Route Parameters

{
    "goodsReceivedNote": {
        "id": "f5e23813-53f4-491b-9210-52be605bec4e",
        "grn_number": "GRN-20260426-0001",
        "lpo_id": "f46530a6-d04d-4432-bc9f-6ac6544812fa",
        "supplier_id": "6d8b3ccb-1c32-4b22-9b6c-ab04a645529a",
        "supplier_name_manual": null,
        "received_date": "2026-04-26T00:00:00.000000Z",
        "delivery_vehicle": null,
        "driver_name": null,
        "subtotal": "268000.00",
        "tax_amount": "48240.00",
        "grand_total": "316240.00",
        "notes": "adadaddada",
        "receipt_path": null,
        "status": "pending_manager_approval",
        "rejection_reason": null,
        "received_by": "bb89326f-2556-41e5-9079-17822a6f1705",
        "confirmed_by": "bb89326f-2556-41e5-9079-17822a6f1705",
        "confirmed_at": "2026-04-26T16:40:01.000000Z",
        "created_at": "2026-04-26T16:39:43.000000Z",
        "updated_at": "2026-04-26T17:14:41.000000Z",
        "accounting_journal_entry_id": null
    }
}

## Database Queries

* sqlite - select * from "sessions" where "id" = 'KOup6vnhqGIrAM7oT0Nr9eVrMSA6HPREoaAm8gwv' limit 1 (2.42 ms)
* sqlite - select * from "users" where "id" = '9638fcd4-d29e-4a8a-af67-dc1192692b1d' limit 1 (0.11 ms)
* sqlite - select * from "goods_received_notes" where "id" = 'f5e23813-53f4-491b-9210-52be605bec4e' limit 1 (0.12 ms)
* sqlite - select * from "roles" where "roles"."id" = '50d9df04-bf91-4870-83de-ba4dec3a6a4b' limit 1 (0.08 ms)
* sqlite - update "goods_received_notes" set "status" = 'pending_manager_approval', "updated_at" = '2026-04-26 17:14:41' where "id" = 'f5e23813-53f4-491b-9210-52be605bec4e' (9.09 ms)
* sqlite - select * from "goods_received_notes" where "id" = 'f5e23813-53f4-491b-9210-52be605bec4e' limit 1 (0.14 ms)
* sqlite - select * from "goods_received_notes" where "goods_received_notes"."id" = 'f5e23813-53f4-491b-9210-52be605bec4e' limit 1 (0.08 ms)
* sqlite - select * from "goods_received_note_items" where "goods_received_note_items"."grn_id" in ('f5e23813-53f4-491b-9210-52be605bec4e') (0.07 ms)
* sqlite - select * from "products" where "products"."id" in ('e7448160-e47e-47bd-9f82-6a37fcccf312') (0.05 ms)
* sqlite - select * from "local_purchase_order_items" where "local_purchase_order_items"."id" in ('e2c8a87f-7b81-487f-9b97-5d4fd4e95c28') (0.05 ms)
* sqlite - select * from "local_purchase_orders" where "local_purchase_orders"."id" in ('f46530a6-d04d-4432-bc9f-6ac6544812fa') (0.06 ms)
* sqlite - select * from "local_purchase_order_items" where "local_purchase_order_items"."lpo_id" in ('f46530a6-d04d-4432-bc9f-6ac6544812fa') (0.04 ms)
* sqlite - select * from "stock_locations" where "code" = 'main_store' limit 1 (0.07 ms)
* sqlite - select * from "suppliers" where "suppliers"."id" = '6d8b3ccb-1c32-4b22-9b6c-ab04a645529a' limit 1 (0.04 ms)
* sqlite - select * from "stock_levels" where "product_id" = 'e7448160-e47e-47bd-9f82-6a37fcccf312' and "location_id" = '09b2f008-0110-4a39-b2e0-e3548c12f2f1' limit 1 (0.04 ms)
* sqlite - update "stock_levels" set "quantity" = 31447 where "id" = '5b39ac18-5282-4bb2-83c7-38d116931cf8' (0.12 ms)
* sqlite - insert into "stock_movements" ("product_id", "location_id", "type", "quantity", "quantity_before", "quantity_after", "unit_cost", "reference_type", "reference_id", "notes", "approved_by", "created_by", "created_at", "id") values ('e7448160-e47e-47bd-9f82-6a37fcccf312', '09b2f008-0110-4a39-b2e0-e3548c12f2f1', 'restock', '134.000', 31313, 31447, '2000.00', 'grn_item', '63c49b31-b177-497c-b3db-d67b91580c38', 'GRN GRN-20260426-0001 from adad', '9638fcd4-d29e-4a8a-af67-dc1192692b1d', '9638fcd4-d29e-4a8a-af67-dc1192692b1d', '2026-04-26 17:14:41', 'a8c7cf36-0a82-4626-9e5b-6369f94c602e') (0.14 ms)
* sqlite - select * from "products" where "products"."id" = 'e7448160-e47e-47bd-9f82-6a37fcccf312' limit 1 (0.04 ms)
* sqlite - update "goods_received_note_items" set "stock_movement_id" = 'a8c7cf36-0a82-4626-9e5b-6369f94c602e', "updated_at" = '2026-04-26 17:14:41' where "id" = '63c49b31-b177-497c-b3db-d67b91580c38' (0.05 ms)
* sqlite - update "local_purchase_order_items" set "received_quantity" = 134, "updated_at" = '2026-04-26 17:14:41' where "id" = 'e2c8a87f-7b81-487f-9b97-5d4fd4e95c28' (0.04 ms)
* sqlite - select count(*) as aggregate from "journal_entries" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-26' as text) (0.06 ms)
* sqlite - insert into "journal_entries" ("entry_date", "description", "source", "source_id", "supplier_id", "reference", "total_debit", "total_credit", "status", "created_by", "posted_by", "posted_at", "id", "entry_no", "updated_at", "created_at") values ('2026-04-26 00:00:00', 'Goods received - GRN-20260426-0001', 'procurement', 'f5e23813-53f4-491b-9210-52be605bec4e', '6d8b3ccb-1c32-4b22-9b6c-ab04a645529a', 'GRN-20260426-0001', 316240, 316240, 'posted', '9638fcd4-d29e-4a8a-af67-dc1192692b1d', '9638fcd4-d29e-4a8a-af67-dc1192692b1d', '2026-04-26 17:14:41', '019dcac9-4554-7146-a3a4-aa232ede51cf', 'JE-20260426-0001', '2026-04-26 17:14:41', '2026-04-26 17:14:41') (0.11 ms)
* sqlite - select * from "accounts" where "code" = '1400' limit 1 (0.06 ms)
* sqlite - insert into "journal_lines" ("journal_entry_id", "account_id", "type", "amount", "notes", "id", "updated_at", "created_at") values ('019dcac9-4554-7146-a3a4-aa232ede51cf', '019d5139-bd17-7183-af0f-e88ee4f20100', 'debit', 268000, NULL, '019dcac9-4556-72b0-8c1f-5651cc3f6cc8', '2026-04-26 17:14:41', '2026-04-26 17:14:41') (0.1 ms)
* sqlite - select * from "accounts" where "code" = '2300' limit 1 (0.04 ms)
* sqlite - insert into "journal_lines" ("journal_entry_id", "account_id", "type", "amount", "notes", "id", "updated_at", "created_at") values ('019dcac9-4554-7146-a3a4-aa232ede51cf', '019d5139-bd51-7118-ab19-d8034e0429dd', 'debit', 48240, NULL, '019dcac9-4556-72b0-8c1f-5651cc73867d', '2026-04-26 17:14:41', '2026-04-26 17:14:41') (0.03 ms)
* sqlite - select * from "accounts" where "code" = '2100' limit 1 (0.03 ms)
* sqlite - insert into "journal_lines" ("journal_entry_id", "account_id", "type", "amount", "notes", "id", "updated_at", "created_at") values ('019dcac9-4554-7146-a3a4-aa232ede51cf', '019d5139-bd3b-73a8-bb29-875211ca0dbb', 'credit', 316240, NULL, '019dcac9-4557-711f-958d-697d6252453d', '2026-04-26 17:14:41', '2026-04-26 17:14:41') (0.03 ms)
* sqlite - select * from "supplier_payables" where ("source_module" = 'procurement' and "source_reference_id" = 'f5e23813-53f4-491b-9210-52be605bec4e') limit 1 (0.06 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.05 ms)
* sqlite - insert into "supplier_payables" ("source_module", "source_reference_id", "created_by", "supplier_id", "reference", "payable_date", "currency", "amount_total", "journal_entry_id", "source_reference_type", "notes", "amount_paid", "balance", "status", "id", "updated_at", "created_at") values ('procurement', 'f5e23813-53f4-491b-9210-52be605bec4e', '9638fcd4-d29e-4a8a-af67-dc1192692b1d', '6d8b3ccb-1c32-4b22-9b6c-ab04a645529a', 'GRN-20260426-0001', '2026-04-26 00:00:00', 'TZS', 316240, '019dcac9-4554-7146-a3a4-aa232ede51cf', 'grn', 'adadaddada', 0, 316240, 'unpaid', 'a4c4a9a4-378c-4315-b8e2-b3276b6ce99d', '2026-04-26 17:14:41', '2026-04-26 17:14:41') (0.16 ms)
* sqlite - select * from "supplier_payables" where "id" = 'a4c4a9a4-378c-4315-b8e2-b3276b6ce99d' limit 1 (0.04 ms)
* sqlite - select * from "suppliers" where "suppliers"."id" in ('6d8b3ccb-1c32-4b22-9b6c-ab04a645529a') (0.03 ms)
* sqlite - select * from "journal_entries" where "journal_entries"."id" in ('019dcac9-4554-7146-a3a4-aa232ede51cf') (0.03 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.1 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.04 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.03 ms)
