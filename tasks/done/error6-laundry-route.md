# Symfony\Component\Routing\Exception\RouteNotFoundException - Internal Server Error

Route [laundry.index] not defined.

PHP 8.4.20
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor/laravel/framework/src/Illuminate/Routing/UrlGenerator.php:528
1 - vendor/laravel/framework/src/Illuminate/Foundation/helpers.php:870
2 - resources/views/dashboards/house-help.blade.php:150
3 - vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php:123
4 - vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php:124
5 - vendor/laravel/framework/src/Illuminate/View/Engines/PhpEngine.php:57
6 - vendor/laravel/framework/src/Illuminate/View/Engines/CompilerEngine.php:76
7 - vendor/laravel/framework/src/Illuminate/View/View.php:208
8 - vendor/laravel/framework/src/Illuminate/View/View.php:191
9 - vendor/laravel/framework/src/Illuminate/View/View.php:160
10 - vendor/laravel/framework/src/Illuminate/Http/Response.php:78
11 - vendor/laravel/framework/src/Illuminate/Http/Response.php:34
12 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:939
13 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:906
14 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
15 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
16 - app/Http/Middleware/SetLocale.php:31
17 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
18 - vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php:50
19 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
20 - vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php:63
21 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
22 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php:87
23 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
24 - vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php:48
25 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
26 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:120
27 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
28 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
29 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
30 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
31 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
32 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
33 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
34 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
35 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
36 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
37 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
38 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
39 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
40 - app/Http/Middleware/SecurityHeaders.php:18
41 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
42 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
43 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
44 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
45 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
46 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
47 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
48 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
49 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
50 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
51 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
52 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
53 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
54 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
55 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
56 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
57 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
58 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:26
59 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
60 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
61 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
62 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
63 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
64 - public/index.php:20
65 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23

## Request

GET /dashboard

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/login
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6IkN4RTZBbnJaQVdzaWo5QzhKL0JkSUE9PSIsInZhbHVlIjoiZWt0c3VRLzJ3RllGcXZEWVpTNndsUkphVXdaN2JmZXcrcTVsRXM3UEVVZ3Z6YmtpT2MxLzhiL2kvWWo3VzRjSXVGWXVkbmZJa09vdDZFcExEVExIZWZyUUZvVkhXRHZ3N1FDVCtZTWlHOERGUkZoVkM5Ukhlbko5QWxSeWwyc1kiLCJtYWMiOiI3YWFmNjk4N2JmZWUzZjUxMjFiNzkxMjhiNjJmZTc2YWI1MjI3NGQ4NjNkNDgxMzc0MjQ5MWQ3MTdkOTU5Y2ZhIiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6ImhjNS9qUzM0dmloTndDQTYwcmMwQ2c9PSIsInZhbHVlIjoibW1wRit2L2ZwejY4TGJseTEzdStCODYwNExaejdtSzhGU21uRFFDZUlORFlZVjN3TEsrZFFXVWRONnNtZUN0eENTdm5BTkJaSUpOOGh6aHlGQjlGWTdHaWlDS3RKZktmZmlYWjlHbGRhakFvRUluVUNxVFNJeEU5dGpveUQwalgiLCJtYWMiOiIzMWM3NjAyZWYwODdiMTM4MTAxMjI2M2Q1ODIzOWZiOGZmNDZjYmQ5NzBkYTcxOGU3ZDliZTFhMTg0NDI3YTNkIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\DashboardController@index
route name: dashboard
middleware: web, auth

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'cIEOZtybjk2z59gLnVgIRmmknvCikSQdvmmSc4iV' limit 1 (9.63 ms)
* sqlite - select * from "users" where "id" = '4a2e9727-fdd9-4c79-ba5a-a3c34176417a' limit 1 (0.29 ms)
* sqlite - select * from "roles" where "roles"."id" = '451c3da4-5c93-45c3-80a5-f4989d217673' limit 1 (0.25 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'pending' (0.29 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'in_progress' (0.2 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'completed' (0.16 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'delivered' (0.16 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where strftime('%Y-%m-%d', "created_at") = cast('2026-05-02' as text) (0.26 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" (0.15 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'dirty' (0.34 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'out_of_order' (0.28 ms)
* sqlite - select * from "laundry_orders" order by "created_at" desc limit 10 (0.31 ms)
* sqlite - select * from "users" where "users"."id" in ('efcdd719-57e9-4015-82c6-04f8db24eac6') (0.29 ms)
* sqlite - select "status", count(*) as count from "laundry_orders" group by "status" (0.28 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.24 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.2 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.22 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.27 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.16 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.18 ms)
