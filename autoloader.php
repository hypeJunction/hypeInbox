<?PHP

$path = __DIR__;
if (file_exists("{$path}/vendor/autoload.php")) {
	require_once "{$path}/vendor/autoload.php";
}

/**
 * Plugin container
 * @return \hypeJunction\Inbox\Plugin
 */
function hypeInbox() {
	return \hypeJunction\Inbox\Plugin::factory();
}