<?PHP

define('DEFAULT_MAX_MINUTES', 50 * 60);
define('DEFAULT_MAX_LENGTH', 50 * 60);
define('DEFAULT_MAX_QUANTITY', 1);
define('DEFAULT_PERIOD', '30 days');
define('DEFAULT_EXPIRATION', '0');

define('RESTRICTED_MAX_MINUTES', 60);
define('RESTRICTED_MAX_LENGTH', 60);
define('RESTRICTED_MAX_QUANTITY', 1);
define('RESTRICTED_PERIOD', '7 days');
define('RESTRICTED_EXPIRATION', '0');

define('COMMISSION_MAX_MINUTES', 10 * 60);
define('COMMISSION_MAX_LENGTH', 3 * 60);
define('COMMISSION_MAX_QUANTITY', 1);
define('COMMISSION_PERIOD', '1 days');
define('COMMISSION_EXPIRATION', '30 minutes');

define('PRIVILEGED_MAX_MINUTES', 200 * 60);
define('PRIVILEGED_MAX_LENGTH', 24 * 60);
define('PRIVILEGED_MAX_QUANTITY', 10);
define('PRIVILEGED_PERIOD', '30 days');
define('PRIVILEGED_EXPIRATION', '0');

define('ADMIN_MAX_MINUTES', 500 * 60);
define('ADMIN_MAX_LENGTH', 500 * 60);
define('ADMIN_MAX_QUANTITY', 50);
define('ADMIN_PERIOD', '0 seconds');
define('ADMIN_EXPIRATION', '0');

?>