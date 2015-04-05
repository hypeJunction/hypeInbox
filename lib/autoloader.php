<?php

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

/**
 * Plugin DI Container
 * @staticvar \hypeJunction\Inbox\Di\PluginContainer $provider
 * @return \hypeJunction\Inbox\Di\PluginContainer
 */
function hypeInbox() {
	static $provider;
	if (null === $provider) {
		$provider = \hypeJunction\Inbox\Di\PluginContainer::create();
	}
	return $provider;
}