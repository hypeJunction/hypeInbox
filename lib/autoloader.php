<?php

elgg_register_classes(dirname(dirname(__FILE__)) . '/classes/');

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