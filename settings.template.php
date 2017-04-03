<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_DEPRECATED);

define('LMS_MYSQL_SERVER', '123.4.56.7');
define('LMS_MYSQL_USER', 'user');
define('LMS_MYSQL_PASS', 'password');
define('LMS_MYSQL_DB', 'database');

define('UNIFI_USER', 'user');
define('UNIFI_PASSWORD', 'password');
define('UNIFI_SITE', 'default');
define('UNIFI_BASEURL', 'http://unifi.server.tld');
define('UNIFI_VERSION', '4.8.15');

define('DEFAULT_MAX_MINUTES', 50 * 60);
define('DEFAULT_MAX_QUANTITY', 1);
define('DEFAULT_PERIOD', '30 days');

define('RESTRICTED_MAX_MINUTES', 30);
define('RESTRICTED_MAX_QUANTITY', 1);
define('RESTRICTED_PERIOD', '7 days');

define('COMMISSION_MAX_MINUTES', 4 * 60);

