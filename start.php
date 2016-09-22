<?php

/**
 * Enhanced inbox for Elgg
 *
 * @package hypeJunction
 * @subpackage hypeInbox
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 */
require_once __DIR__ . '/autoloader.php';

use hypeJunction\Inbox\Ajax;
use hypeJunction\Inbox\Config;
use hypeJunction\Inbox\Graph;
use hypeJunction\Inbox\Menus;
use hypeJunction\Inbox\Notifications;
use hypeJunction\Inbox\Router;

elgg_register_event_handler('init', 'system', function() {

	elgg_extend_view('elgg.css', 'framework/inbox.css');
	elgg_extend_view('elgg.js', 'framework/inbox/message.js');

	hypeInbox()->config->registerLabels();

	// URL and page handling
	elgg_register_page_handler('messages', [Router::class, 'handleMessages']);
	elgg_register_plugin_hook_handler('page_owner', 'system', [Router::class, 'resolvePageOwner']);
	elgg_register_plugin_hook_handler('entity:url', 'object', [Router::class, 'messageUrlHandler']);
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', [Router::class, 'messageIconUrlHandler']);

	// Register actions
	elgg_register_action('messages/send', __DIR__ . '/actions/messages/send.php');
	elgg_register_action('messages/delete', __DIR__ . '/actions/messages/delete.php');
	elgg_register_action('messages/markread', __DIR__ . '/actions/messages/markread.php');
	elgg_register_action('messages/markunread', __DIR__ . '/actions/messages/markunread.php');
	elgg_register_action('messages/load', __DIR__ . '/actions/messages/load.php');
	elgg_register_action("hypeInbox/settings/save", __DIR__ . '/actions/settings/save.php', 'admin');
	elgg_register_action('inbox/admin/import', __DIR__ . '/actions/admin/import.php', 'admin');

	// Third party integrations
	elgg_register_plugin_hook_handler('config:user_types', 'framework:inbox', [Config::class, 'filterUserTypes']);

	// Add inbox page menu items
	elgg_register_plugin_hook_handler('register', 'menu:page', [Menus::class, 'setupPageMenu']);

	// Setup page menu items
	elgg_register_plugin_hook_handler('register', 'menu:page', [Menus::class, 'setupAdminPageMenu']);

	// Setup inbox menu
	elgg_register_plugin_hook_handler('register', 'menu:inbox', [Menus::class, 'setupInboxMenu']);

	// Setup message entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', [Menus::class, 'setupMessageMenu']);

	// Replace user hover menu items
	elgg_unregister_plugin_hook_handler('register', 'menu:user_hover', 'messages_user_hover_menu');
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', [Menus::class, 'setupUserHoverMenu']);

	// Export
	elgg_register_plugin_hook_handler('aliases', 'graph', [Graph::class, 'getGraphAlias']);
	elgg_register_plugin_hook_handler('graph:properties', 'object:messages', [Graph::class, 'getMessageProperties']);
	
	// Top bar
	elgg_unregister_plugin_hook_handler('register', 'menu:topbar', 'messages_register_topbar');
	elgg_register_plugin_hook_handler('register', 'menu:topbar', [Menus::class, 'setupTopbarMenu']);
	elgg_register_plugin_hook_handler('output', 'ajax', [Ajax::class, 'setUnreadMessagesCount']);
	elgg_extend_view('page/elements/topbar', 'framework/inbox/popup');

	// Notification Templates
	elgg_register_plugin_hook_handler('get_templates', 'notifications', [Notifications::class, 'registerCustomTemplates']);
});
