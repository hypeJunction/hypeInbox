<?PHP

if (!is_callable('hypeApps')) {
	throw new Exception("hypeInbox requires hypeApps");
}

$path = dirname(dirname(__DIR__));

if (!file_exists("{$path}/vendor/autoload.php")) {
	throw new Exception('hypeInbox can not resolve composer dependencies. Run composer install');
}

require_once "{$path}/vendor/autoload.php";

/**
 * Plugin container
 * @return \hypeJunction\Inbox\Plugin
 */
function hypeInbox() {
	return \hypeJunction\Inbox\Plugin::factory();
}