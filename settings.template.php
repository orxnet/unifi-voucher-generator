<?PHP

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_DEPRECATED);

define('LMS_MYSQL_SERVER', '1.2.3.4');
define('LMS_MYSQL_USER', 'user');
define('LMS_MYSQL_PASS', 'password');
define('LMS_MYSQL_DB', 'database');

define('UNIFI_USER', 'user');
define('UNIFI_PASSWORD', 'password');
define('UNIFI_SITE', 'default');
define('UNIFI_BASEURL', 'https://unifi.domain.tld:8443');
define('UNIFI_VERSION', '4.8.15');

?>
