<?php

/* hypeInbox
 *
 * Enhanced inbox for Elgg
 * 
 * @package    hypeJunction
 * @subpackage Inbox
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyright (c) 2011-2013, Ismayil Khayredinov
 */

namespace hypeJunction\Inbox;

define('HYPEINBOX', basename(__DIR__));
define('HYPEINBOX_NOTIFICATION', '__notification');
define('HYPEINBOX_PRIVATE', '__private');

require_once __DIR__ . '/vendors/autoload.php';

require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/events.php';
require_once __DIR__ . '/lib/hooks.php';
require_once __DIR__ . '/lib/page_handlers.php';
require_once __DIR__ . '/lib/deprecated.php';

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\config', 1);
elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');
elgg_register_event_handler('upgrade', 'system', __NAMESPACE__ . '\\upgrade');
elgg_register_event_handler('pagesetup', 'system', __NAMESPACE__ . '\\pagesetup');

/**
 * Prepare config
 * @return void
 */
function config() {

	$config = new Config;
	$message_types = $config->getMessageTypes();

	// Register label translations for custom message types
	foreach ($message_types as $type => $options) {
		$ruleset = $config->getRuleset($type);
		add_translation('en', array(
			$ruleset->getSingularLabel(false) => $ruleset->getSingularLabel('en'),
			$ruleset->getPluralLabel(false) => $ruleset->getPluralLabel('en')
		));
	}

	elgg_set_config('inbox_message_types', $message_types);
	elgg_set_config('inbox_user_types', $config->getUserTypes());
	elgg_set_config('inbox_user_relationships', $config->getUserRelationships());
	elgg_set_config('inbox_user_group_relationships', $config->getUserGroupRelationships());
}

/**
 * Initialize the plugin
 * @return void
 */
function init() {

	$plugin_id = HYPEINBOX;

	/**
	 * Actions
	 */
	elgg_register_action("$plugin_id/settings/save", __DIR__ . '/actions/settings/save.php', 'admin');
	elgg_register_action('inbox/admin/import', __DIR__ . '/actions/admin/import.php', 'admin');

	elgg_register_action('messages/send', __DIR__ . '/actions/messages/send.php');
	elgg_register_action('messages/delete', __DIR__ . '/actions/messages/delete.php');
	elgg_register_action('messages/markread', __DIR__ . '/actions/messages/markread.php');
	elgg_register_action('messages/markunread', __DIR__ . '/actions/messages/markunread.php');

	/**
	 * JS/CSS
	 */
	elgg_register_css('inbox.base.css', elgg_get_simplecache_url('css', 'framework/inbox/stylesheet.css'));
	elgg_register_css('fonts.font-awesome', "/mod/$plugin_id/fonts/font-awesome.css");
	
	/**
	 * Hooks
	 */
	// Third party integrations
	elgg_register_plugin_hook_handler('config:user_types', 'framework:inbox', __NAMESPACE__ . '\\integrated_user_types');

	// Menu
	elgg_register_plugin_hook_handler('register', 'menu:page', __NAMESPACE__ . '\\page_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:inbox', __NAMESPACE__ . '\\inbox_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:inbox:thread', __NAMESPACE__ . '\\inbox_thread_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:entity', __NAMESPACE__ . '\\message_menu_setup');

	// Replace user hover menu items
	elgg_unregister_plugin_hook_handler('register', 'menu:user_hover', 'messages_user_hover_menu');
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', __NAMESPACE__ . '\\user_hover_menu_setup');

	// URLs
	elgg_register_plugin_hook_handler('entity:url', 'object', __NAMESPACE__ . '\\message_url');
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', __NAMESPACE__ . '\\message_icon_url');

	/**
	 * Pages
	 */
	// Replace page handler defined by messages plugin
	elgg_unregister_page_handler('messages', 'messages_page_handler');
	elgg_register_page_handler('messages', __NAMESPACE__ . '\\page_handler');

	// Notifications
	$type = Message::TYPE;
	$subtype = Message::SUBTYPE;
	$action = 'send:after';
	elgg_register_notification_event($type, $subtype, array($action));
	elgg_register_plugin_hook_handler('prepare', "notification:$action:$type:$subtype", __NAMESPACE__ . '\\prepare_notification');
	// we only want message recipients to recieve a notification thus late priority
	elgg_register_plugin_hook_handler('get', 'subscriptions', __NAMESPACE__ . '\\get_subscriptions', 9999);
}

/**
 * Run upgrade scripts
 * @return void
 */
function upgrade() {
	if (elgg_is_admin_logged_in()) {
		include_once __DIR__ . '/lib/upgrades.php';
	}
}
