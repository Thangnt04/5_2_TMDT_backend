<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-03-10 11:58:51 --> Severity: Warning --> require_once(/var/www/html/vendor/autoload.php): failed to open stream: No such file or directory /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 11:58:51 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /var/www/html/system/core/Exceptions.php:271) /var/www/html/system/core/Common.php 578
ERROR - 2026-03-10 11:58:51 --> Severity: Compile Error --> require_once(): Failed opening required '/var/www/html/vendor/autoload.php' (include_path='.:/usr/local/lib/php') /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 11:58:51 --> 404 Page Not Found: Faviconico/index
ERROR - 2026-03-10 11:58:56 --> Severity: Warning --> require_once(/var/www/html/vendor/autoload.php): failed to open stream: No such file or directory /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 11:58:56 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /var/www/html/system/core/Exceptions.php:271) /var/www/html/system/core/Common.php 578
ERROR - 2026-03-10 11:58:56 --> Severity: Compile Error --> require_once(): Failed opening required '/var/www/html/vendor/autoload.php' (include_path='.:/usr/local/lib/php') /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 11:59:22 --> Severity: Warning --> require_once(/var/www/html/vendor/autoload.php): failed to open stream: No such file or directory /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 11:59:22 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /var/www/html/system/core/Exceptions.php:271) /var/www/html/system/core/Common.php 578
ERROR - 2026-03-10 11:59:22 --> Severity: Compile Error --> require_once(): Failed opening required '/var/www/html/vendor/autoload.php' (include_path='.:/usr/local/lib/php') /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:04:44 --> Severity: Warning --> require_once(/var/www/html/vendor/autoload.php): failed to open stream: No such file or directory /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:04:44 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /var/www/html/system/core/Exceptions.php:271) /var/www/html/system/core/Common.php 578
ERROR - 2026-03-10 12:04:44 --> Severity: Compile Error --> require_once(): Failed opening required '/var/www/html/vendor/autoload.php' (include_path='.:/usr/local/lib/php') /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:04:45 --> Severity: Warning --> require_once(/var/www/html/vendor/autoload.php): failed to open stream: No such file or directory /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:04:45 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /var/www/html/system/core/Exceptions.php:271) /var/www/html/system/core/Common.php 578
ERROR - 2026-03-10 12:04:46 --> Severity: Compile Error --> require_once(): Failed opening required '/var/www/html/vendor/autoload.php' (include_path='.:/usr/local/lib/php') /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:04:58 --> Severity: Warning --> require_once(/var/www/html/vendor/autoload.php): failed to open stream: No such file or directory /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:04:58 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /var/www/html/system/core/Exceptions.php:271) /var/www/html/system/core/Common.php 578
ERROR - 2026-03-10 12:04:58 --> Severity: Compile Error --> require_once(): Failed opening required '/var/www/html/vendor/autoload.php' (include_path='.:/usr/local/lib/php') /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:05:12 --> Severity: Warning --> require_once(/var/www/html/vendor/autoload.php): failed to open stream: No such file or directory /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:05:12 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /var/www/html/system/core/Exceptions.php:271) /var/www/html/system/core/Common.php 578
ERROR - 2026-03-10 12:05:12 --> Severity: Compile Error --> require_once(): Failed opening required '/var/www/html/vendor/autoload.php' (include_path='.:/usr/local/lib/php') /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:05:59 --> Severity: Warning --> require_once(/var/www/html/vendor/autoload.php): failed to open stream: No such file or directory /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:05:59 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /var/www/html/system/core/Exceptions.php:271) /var/www/html/system/core/Common.php 578
ERROR - 2026-03-10 12:05:59 --> Severity: Compile Error --> require_once(): Failed opening required '/var/www/html/vendor/autoload.php' (include_path='.:/usr/local/lib/php') /var/www/html/application/controllers/Home.php 3
ERROR - 2026-03-10 12:07:58 --> Query error: Table 'webquanao.discount' doesn't exist - Invalid query: SELECT product.*, CASE
				WHEN discount.status = 1 
					AND product.price >= discount.min_price 
					AND NOW() BETWEEN discount.start_date AND discount.end_date THEN
					CASE
						WHEN discount.measure = 0 THEN discount.value
						WHEN discount.measure = 1 THEN product.price * (discount.value / 100)
						ELSE 0
					END
				ELSE 0
			END AS discount
FROM `product`
LEFT JOIN `discount` ON `product`.`discount_id` = `discount`.`id`
ORDER BY `id` DESC
 LIMIT 12
ERROR - 2026-03-10 12:09:24 --> 404 Page Not Found: Upload/slider
ERROR - 2026-03-10 12:09:24 --> 404 Page Not Found: Upload/slider
ERROR - 2026-03-10 12:09:24 --> 404 Page Not Found: Upload/slider
