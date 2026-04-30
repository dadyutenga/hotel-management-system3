# ParseError - Internal Server Error

syntax error, unexpected token "endif"

PHP 8.4.20
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - resources/views/store/transfers/index.blade.php:68
1 - vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php:124
2 - vendor/laravel/framework/src/Illuminate/View/Engines/PhpEngine.php:57
3 - vendor/laravel/framework/src/Illuminate/View/Engines/CompilerEngine.php:76
4 - vendor/laravel/framework/src/Illuminate/View/View.php:208
5 - vendor/laravel/framework/src/Illuminate/View/View.php:191
6 - vendor/laravel/framework/src/Illuminate/View/View.php:160
7 - vendor/laravel/framework/src/Illuminate/Http/Response.php:78
8 - vendor/laravel/framework/src/Illuminate/Http/Response.php:34
9 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:939
10 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:906
11 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
12 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
13 - app/Http/Middleware/RoleMiddleware.php:37
14 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
15 - app/Http/Middleware/SetLocale.php:31
16 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
17 - vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php:50
18 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
19 - vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php:63
20 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
21 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php:87
22 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
23 - vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php:48
24 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
25 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:120
26 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
27 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
28 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
29 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
30 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
31 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
32 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
33 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
34 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
35 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
36 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
37 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
38 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
39 - app/Http/Middleware/SecurityHeaders.php:18
40 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
41 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
42 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
43 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
44 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
45 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
46 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
47 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
48 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
49 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
50 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
51 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
52 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
53 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
54 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
55 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
56 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
57 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:26
58 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
59 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
60 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
61 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
62 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
63 - public/index.php:20
64 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23

## Request

GET /store/transfers

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/store/stock/levels
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6IjlLVU04WHFCOFZERm04cjBqblIrM0E9PSIsInZhbHVlIjoiNldSZjdvL0lNYXJFWGFpRWRzb1d4SHBab0V1UzlwOHJQOHhieUFucWJId2NrRDVCQTFLOXUvek1OVU04ZjdENVFQbVhabG1ZT1haYllxV09HZjlkNFh0RUpDVnBZWmtBTlBqYWxuSUZ1YXlwd25HeHNRWDFWbjJnUkJPWUluRUQiLCJtYWMiOiJlOTcyZDg4ZjM5MTM3YWU5MGVjMGUxYTFlMWZiMmRhZjA0YzRlMzJlNzBmODMwM2JmYjgxY2EwZDcwZWI0NDJkIiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6ImhhbkVzMnBaZ0kyQWxqOW5WWnMva0E9PSIsInZhbHVlIjoiczBGdEswMTNDaVpRcXA2Wkd0Zk1VTzRuSjlqb09rTHdBS1JPZ1QvWGQyZTU3Uko1a05ZMzhtN2R2Vm1keDRTTW1WSHhKTGgxTUlPZ2JJczhMcHJvN3lKZ3RRcUpOanlPL0drS2czYndtNlRxak9sRkxuY0hkUkZWZTlLVWdyWVoiLCJtYWMiOiI4NzVmOTVmNDk2MmIzMGNmZGJlNjk2MDU4MDVmYTZlZTk5ZTRlNDAzMGVlOTNlNzg1OWRkZjY4MmQ2YTIxYmJlIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Store\StockTransferController@index
route name: store.transfers.index
middleware: web, auth, role:store_manager,store_keeper,manager,admin

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'GwZJaXVAvRaZAYjyGlKgHC74SURlJgauQCittjjS' limit 1 (1.41 ms)
* sqlite - select * from "users" where "id" = 'bb89326f-2556-41e5-9079-17822a6f1705' limit 1 (0.09 ms)
* sqlite - select * from "roles" where "roles"."id" = 'e89fd0bc-1454-49a7-a10e-1280958fa30e' limit 1 (0.05 ms)
* sqlite - select count(*) as aggregate from "stock_transfers" (0.03 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.05 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-system_currency', 'laravel-cache-illuminate:cache:flexible:created:system_currency') and "expiration" <= 1777224814 (7.67 ms)
* sqlite - select "value" from "system_settings" where "key" = 'default_currency' limit 1 (0.15 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1777224874, 'laravel-cache-system_currency', 's:3:"TZS";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (6.58 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.08 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.06 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-tzs_exchange_rate', 'laravel-cache-illuminate:cache:flexible:created:tzs_exchange_rate') and "expiration" <= 1777224814 (6.74 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.08 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1777224874, 'laravel-cache-tzs_exchange_rate', 's:4:"2500";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (6.73 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.08 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.03 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.03 ms)
