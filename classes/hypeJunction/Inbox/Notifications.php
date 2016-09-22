<?php

namespace hypeJunction\Inbox;

class Notifications {

	/**
	 * Register custom template
	 *
	 * @param string $hook   "get_templates"
	 * @param string $type   "notifications"
	 * @param string $return Template names
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function registerCustomTemplates($hook, $type, $return, $params) {
		$return[] = "messages_send";
		return $return;
	}
}
