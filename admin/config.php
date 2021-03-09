<?php
// HTTP
define('HTTP_SERVER', 'http://localhost/shanid/looksshops/admin/');
define('HTTP_CATALOG', 'http://localhost/shanid/looksshops/');

// HTTPS
define('HTTPS_SERVER', 'http://localhost/shanid/looksshops/admin/');
define('HTTPS_CATALOG', 'http://localhost/shanid/looksshops/');

// DIR
define('DIR_APPLICATION', '/var/www/html/shanid/looksshops/admin/');
define('DIR_SYSTEM', '/var/www/html/shanid/looksshops/system/');
define('DIR_IMAGE', '/var/www/html/shanid/looksshops/image/');
define('DIR_STORAGE', '/var/www/html/shanid/looksshops/system/storage/');
define('DIR_CATALOG', '/var/www/html/shanid/looksshops/catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'toor');
define('DB_DATABASE', 'looksshops');
define('DB_PORT', '3306');
define('DB_PREFIX', '');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
