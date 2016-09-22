<?PHP

$path = __DIR__;
if (file_exists("{$path}/vendor/autoload.php")) {
	require_once "{$path}/vendor/autoload.php";
}

/**
 * Plugin container
 * 
 * @return \hypeJunction\Inbox\Plugin
 * @access private since 6.0
 */
function hypeInbox() {

	static $instance;
	if (null === $instance) {
		$plugin = elgg_get_plugin_from_id('hypeInbox');
		$instance = new \hypeJunction\Inbox\Plugin($plugin);
	}

	return $instance;
}
