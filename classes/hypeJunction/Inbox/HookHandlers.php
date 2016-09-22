<?php

namespace hypeJunction\Inbox;

use ElggMenuItem;
use hypeJunction\Inbox\Config;

/**
 * Plugin hooks service
 */
class HookHandlers {

	/**
	 * Add third party user types/roles to the config array
	 *
	 * @param string $hook   "config:user_types"
	 * @param string $type   "framework:inbox"
	 * @param array  $return User types config array
	 * @param array  $params Hook params
	 * @return array
	 * @deprecated 6.0
	 */
	public function filterUserTypes($hook, $type, $return, $params) {
		return Config::filterUserTypes($hook, $type, $return, $params);
	}

	/**
	 * Messages page menu setup
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:page"
	 * @param array  $return An array of menu items
	 * @param array  $params Additional parameters
	 * @return array An array of menu items
	 * @deprecated 6.0
	 */
	public function setupPageMenu($hook, $type, $return, $params) {
		return Menus::setupPageMenu($hook, $type, $return, $params);
	}

	/**
	 * Register user hover menu items
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:user_hover"
	 * @param array  $return An array of menu items
	 * @param array  $params Additional parameters
	 * @return array An array of menu items
	 * @deprecated 6.0
	 */
	public function setupUserHoverMenu($hook, $type, $return, $params) {
		return Menus::setupUserHoverMenu($hook, $type, $return, $params);
	}

	/**
	 * Message entity menu setup
	 *
	 * @param string $hook "register"
	 * @param string $type "menu:entity"
	 * @param array $return An array of menu items
	 * @param array $params An array of additional parameters
	 * @return array An array of menu items
	 * @deprecated 6.0
	 */
	public function setupMessageMenu($hook, $type, $return, $params) {
		return Menus::setupMessageMenu($hook, $type, $return, $params);
	}

	/**
	 * Inbox controls setup
	 *
	 * @param string $hook "register"
	 * @param string $type "menu:inbox"
	 * @param array $return An array of menu items
	 * @param array $params An array of additional parameters
	 * @return array An array of menu items
	 * @deprecated 6.0
	 */
	public function setupInboxMenu($hook, $type, $return, $params) {
		return Menus::setupInboxMenu($hook, $type, $return, $params);
	}

	/**
	 * Thread controls setup
	 *
	 * @param string $hook "register"
	 * @param string $type "menu:inbox:thread"
	 * @param array $return An array of menu items
	 * @param array $params An array of additional parameters
	 * @return array An array of menu items
	 * @deprecated 6.0
	 */
	public function setupInboxThreadMenu($hook, $type, $return, $params) {
		return Menus::setupInboxThreadMenu($hook, $type, $return, $params);
	}

	/**
	 * Setup topbar menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:topbar"
	 * @param ElggMenuItem[] $return  Menu
	 * @param array          $params  Hook params
	 * @return ElggMenuItem[]
	 * @deprecated 6.0
	 */
	public function setupTopbarMenu($hook, $type, $return, $params) {
		return Menus::setupTopbarMenu($hook, $type, $return, $params);
	}

	/**
	 * Pretty URL for message objects
	 *
	 * @param string $hook   "entity:url"
	 * @param string $type   "object"
	 * @param string $return Icon URL
	 * @param array  $params Hook params
	 * @return string Filtered URL
	 */
	public function handleMessageURL($hook, $type, $return, $params) {
		return Router::messageUrlHandler($hook, $type, $return, $params);
	}

	/**
	 * Replace message icon with a sender icon
	 *
	 * @param string $hook   "entity:icon:url"
	 * @param string $type   "object"
	 * @param string $return Icon URL
	 * @param array  $params Hook params
	 * @return string Filtered URL
	 * @deprecated 6.0
	 */
	public function handleMessageIconURL($hook, $type, $return, $params) {
		return Router::messageIconUrlHandler($hook, $type, $return, $params);
	}

	/**
	 * @deprecated 6.0
	 */
	public function getGraphAlias($hook, $type, $return, $params) {
		return Graph::getGraphAlias($hook, $type, $return, $params);
	}

	/**
	 * @deprecated 6.0
	 */
	public function getMessageProperties($hook, $type, $return, $params) {
		return Graph::getMessageProperties($hook, $type, $return, $params);
	}

	/**
	 * Add unread notifications count to the ajax responses
	 *
	 * @param string $hook   "output"
	 * @param string $type   "ajax"
	 * @param array  $return Ajax output
	 * @param array  $params Hook params
	 * @return array
	 * @deprecated 6.0
	 */
	public function ajaxOutput($hook, $type, $return, $params) {
		return Ajax::setUnreadMessagesCount();
	}

	/**
	 * Register custom template
	 *
	 * @param string $hook   "get_templates"
	 * @param string $type   "notifications"
	 * @param string $return Template names
	 * @param array  $params Hook params
	 * @return array
	 * @deprecated 6.0
	 */
	public function addCustomTemplate($hook, $type, $return, $params) {
		return \hypeJunction\Inbox\Notifiations::registerCustomTemplates($hook, $type, $return, $params);
	}

}
