# Illuminate\Database\QueryException - Internal Server Error

SQLSTATE[23000]: Integrity constraint violation: 19 CHECK constraint failed: status (Connection: sqlite, Database: /home/dadi-utenga/projects/hotel-management-system/database/database.sqlite, SQL: update "laundry_orders" set "status" = charged, "payment_method" = charge_to_booking, "updated_at" = 2026-05-02 22:49:27 where "id" = 9d6c7b1c-1825-4e1d-a0a3-c48a2cc1a90c)

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
8 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php:1090
9 - app/Http/Controllers/Laundry/LaundryOrderController.php:309
10 - vendor/laravel/framework/src/Illuminate/Database/Concerns/ManagesTransactions.php:35
11 - vendor/laravel/framework/src/Illuminate/Database/DatabaseManager.php:491
12 - vendor/laravel/framework/src/Illuminate/Support/Facades/Facade.php:363
13 - app/Http/Controllers/Laundry/LaundryOrderController.php:289
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

POST /laundry/orders/9d6c7b1c-1825-4e1d-a0a3-c48a2cc1a90c/settle

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/laundry/orders/9d6c7b1c-1825-4e1d-a0a3-c48a2cc1a90c
* **content-type**: application/x-www-form-urlencoded
* **content-length**: 95
* **origin**: http://127.0.0.1:8000
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6IlorblJEZmFTNytkeXpFY05SbHYvTnc9PSIsInZhbHVlIjoieUJ6YjQ1UGMrTE5vWFdYTmQ5YnFNUVFIYkgvS0Qzd1h5dGw4RWxTMDRwVEsxVml5VlZBT3lFemxESFZuY0hOcjg3QWdBb3BnOU9pRXhSWFkzQ0pLYTFNaWlvU09rRkRnZVF4QkMvVTdlRTZmRy84VkpwalJtdStYVmg1NXliQzQiLCJtYWMiOiIzNzA5ZWEwNmJlZTBkNDI3MzhlMjI3ZGRkNzIwMGUzMDViMzVmMzgyMDJkNDJlMGMyOWExMGQzNDQ0YmM3Nzc1IiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6IjVWbWJWQjE1TklOUUFLOFVVRDU0dmc9PSIsInZhbHVlIjoiTEhGK1BxK3ArWDZuZ2UwWVN3SXhtQUFYUU5zeDBMUXNhNkNqcHBKb08xZS9IcnU3YVoxdjBISTd1VTU5YVNUUWI0L3lCL2dEM1VNRUZwZ2ppdWpuK1dwRlZ1V2dmMFdmWCtmVVFXL0VQb3FsT2M3MjNxSEFtR3dWQWVHcXA5cFMiLCJtYWMiOiJmNmZlMDAxMjdlZjZmMmQ0NGQ1Y2E3ZjU1ZjlhMmE5YzcwMDMzZTI3NTYzZWE1NTU5OGZmMTAzNzA1N2FjMzUyIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Laundry\LaundryOrderController@settle
route name: laundry.orders.settle
middleware: web, auth, role:front_desk,laundry_manager,supervisor,manager

## Route Parameters

{
    "laundryOrder": {
        "id": "9d6c7b1c-1825-4e1d-a0a3-c48a2cc1a90c",
        "order_number": "LND-20260502-0001",
        "customer_type": "guest",
        "booking_id": "3280a954-3b41-432f-8a75-fc42896b800a",
        "room_number": "MAIN-002",
        "customer_name": null,
        "customer_phone": null,
        "status": "charged",
        "special_instructions": "aaaaaaaa",
        "subtotal": "1000.00",
        "discount": "0.00",
        "total": "1000.00",
        "payment_method": "charge_to_booking",
        "expected_ready_at": "2026-05-03T10:45:42.000000Z",
        "ready_at": "2026-05-02T22:46:08.000000Z",
        "delivered_at": "2026-05-02T22:46:12.000000Z",
        "collected_at": null,
        "settled_at": null,
        "received_by": "4a2e9727-fdd9-4c79-ba5a-a3c34176417a",
        "processed_by": "4a2e9727-fdd9-4c79-ba5a-a3c34176417a",
        "delivered_by": "4a2e9727-fdd9-4c79-ba5a-a3c34176417a",
        "settled_by": null,
        "created_at": "2026-05-02T22:45:42.000000Z",
        "updated_at": "2026-05-02T22:49:27.000000Z"
    }
}

## Database Queries

* sqlite - select * from "sessions" where "id" = 'coSB6afm3LO8P3dQfp8ijXqJjDVqta3xjTh0wEga' limit 1 (8.14 ms)
* sqlite - delete from "sessions" where "last_activity" <= 1777754967 (0.18 ms)
* sqlite - select * from "users" where "id" = 'efcdd719-57e9-4015-82c6-04f8db24eac6' limit 1 (0.33 ms)
* sqlite - select * from "laundry_orders" where "id" = '9d6c7b1c-1825-4e1d-a0a3-c48a2cc1a90c' limit 1 (0.46 ms)
* sqlite - select * from "roles" where "roles"."id" = 'edc84afd-bb55-484d-9fbb-9939f1c771eb' limit 1 (0.33 ms)
* sqlite - select count(*) as aggregate from "bookings" where "id" = '3280a954-3b41-432f-8a75-fc42896b800a' (0.3 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.26 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.16 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.14 ms)
