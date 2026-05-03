# Error - Internal Server Error

Undefined constant App\Models\Role::CASHIER

PHP 8.4.20
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - app/Http/Controllers/UserController.php:18
1 - vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php:46
2 - vendor/laravel/framework/src/Illuminate/Routing/Route.php:265
3 - vendor/laravel/framework/src/Illuminate/Routing/Route.php:211
4 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:822
5 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
6 - app/Http/Middleware/RoleMiddleware.php:37
7 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
8 - app/Http/Middleware/SetLocale.php:31
9 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
10 - vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php:50
11 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
12 - vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php:63
13 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
14 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php:87
15 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
16 - vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php:48
17 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
18 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:120
19 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
20 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
21 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
22 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
23 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
24 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
25 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
26 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
27 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
28 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
29 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
30 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
31 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
32 - app/Http/Middleware/SecurityHeaders.php:18
33 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
34 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
35 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
36 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
37 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
38 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
39 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
40 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
41 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
42 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
43 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
44 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
45 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
46 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
47 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
48 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
49 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
50 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:26
51 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
52 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
53 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
54 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
55 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
56 - public/index.php:20
57 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23

## Request

GET /users/create

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/users
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6IlhvcEZ6TGdWb0RNNjl5Tm5jNHdNYVE9PSIsInZhbHVlIjoieWJGdDlrVXlRb1EwMjVlWFo0dXU2V1VGYVh0YXRNRzlPdmIxRjkxOGdXRzFLS01OUlB4YVV1eU50NFNuS09tVUVDd1lHbWFBYmF0ZUY5a2JwbmFpNkZXazg5cGtFL0dwdiszZFVzeDZwcTdKdzVhMzBZNGYxdTNLdzdETVNhRXUiLCJtYWMiOiIxOTU2ZGRmMWFmOWFiYjBjODliNDRiZTExYThhODczY2Q3M2I2NGI3ODYxOGYyZjMyY2M1ZjQ2NTgzMDUwNWM5IiwidGFnIjoiIn0%3D; hms-session=eyJpdiI6IkVBMEgxSHNxMEZGWExORXVTL2tpMFE9PSIsInZhbHVlIjoia00zK3YzQWpQU0FCWmlHUExCYTBYL0FzejdhMU5qTUNId0xqUENTOGlkTDVsV3FDUDlzRnBHckYvRWlKcXl0clREQTZzaXFjMTZ1QmJMdmtQQmY2ekVKQkMvelBwdzlVTWNlT2ZWdHRDdDJNY2dWY3ZqWFFMTTVTQ1ZYRnhDOEoiLCJtYWMiOiI0OGI3M2M0ODgwYzFlNzc1ODgzMzFhZWM3YTJjMWI2ZmEzMGUyYWNjNzYxMWJmZmNmODM4YzZkNDU5ZGM0ODEwIiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6InVUSHNhNXFGR0JQcFRzcVpSL1Uya0E9PSIsInZhbHVlIjoiSmhLRlhQVGsvSUZLN0hzSzhxMDhQWHFEcUsyV0hybEdnTjYyT2dqUWFnUkE5emNnaVBnU25QM3FyRnoyK0s1dGxCWHhSbDljS1R2L1Y3ODFPT2dGYXpPSkFOaDhGb2FUSzFqSjNpSGdvOW12MVkwQ0RxVlZDUUZYVk1iSVVnUXoiLCJtYWMiOiIwYzBlZGYxODZhZjYzZDkyZTcxZGM1M2MzNDE0ODRjNGQxMmNjOGQ5ZDFjYjFiNWViYjAzNDcxYzU1Yzg1Njc0IiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\UserController@create
route name: users.create
middleware: web, auth, role:admin

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'OnxzWli9hSM63mfTHy8tzMhPmt5DDsWc8hoF4rsJ' limit 1 (8.76 ms)
* sqlite - select * from "users" where "id" = 'ad8c70a2-efde-48e9-b73a-cd8775edec0e' limit 1 (0.47 ms)
* sqlite - select * from "roles" where "roles"."id" = '666e1198-96fe-4f88-ac23-5021476b586a' limit 1 (0.38 ms)
* sqlite - select * from "cache" where "key" in ('hms-cache-system_currency') (0.35 ms)
* sqlite - delete from "cache" where "key" in ('hms-cache-system_currency', 'hms-cache-illuminate:cache:flexible:created:system_currency') and "expiration" <= 1777818941 (12.89 ms)
* sqlite - select "value" from "system_settings" where "key" = 'default_currency' limit 1 (0.37 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1777819001, 'hms-cache-system_currency', 's:3:"USD";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (10.02 ms)
* sqlite - select * from "cache" where "key" in ('hms-cache-system_currency') (0.33 ms)
* sqlite - select * from "cache" where "key" in ('hms-cache-tzs_exchange_rate') (0.28 ms)
* sqlite - delete from "cache" where "key" in ('hms-cache-tzs_exchange_rate', 'hms-cache-illuminate:cache:flexible:created:tzs_exchange_rate') and "expiration" <= 1777818941 (8.82 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.28 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1777819001, 'hms-cache-tzs_exchange_rate', 's:4:"2500";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (11.41 ms)
