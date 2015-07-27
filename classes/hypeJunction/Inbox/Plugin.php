<?php

namespace hypeJunction\Inbox;

/**
 * Inbox service provider
 *
 * @property-read \ElggPlugin                      $plugin
 * @property-read \hypeJunction\Inbox\Config       $config
 * @property-read \hypeJunction\Inbox\HookHandlers $hooks
 * @property-read \hypeJunction\Inbox\Router	   $router
 * @property-read \hypeJunction\Inbox\Models\Model $model
 */
final class Plugin extends \hypeJunction\Plugin {

	/**
	 * {@inheritdoc}
	 */
	static $instance;

	/**
	 * {@inheritdoc}
	 */
	public function __construct(\ElggPlugin $plugin) {

		$this->setValue('plugin', $plugin);
		$this->setFactory('config', function (\hypeJunction\Inbox\Plugin $p) {
			return new \hypeJunction\Inbox\Config($p->plugin);
		});
		$this->setFactory('hooks', function (\hypeJunction\Inbox\Plugin $p) {
			return new \hypeJunction\Inbox\HookHandlers($p->config, $p->router, $p->model);
		});
		$this->setFactory('router', function (\hypeJunction\Inbox\Plugin $p) {
			return new \hypeJunction\Inbox\Router($p->config);
		});
		$this->setFactory('model', function (\hypeJunction\Inbox\Plugin $c) {
			return new \hypeJunction\Inbox\Models\Model($c->config);
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public static function factory() {
		if (null === self::$instance) {
			$plugin = elgg_get_plugin_from_id('hypeInbox');
			self::$instance = new self($plugin);
		}
		return self::$instance;
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot() {
		elgg_register_event_handler('init', 'system', array($this, 'init'));
	}

	/**
	 * System init callback
	 * @return void
	 */
	public function init() {

		hypeInbox()->config->registerLabels();

		elgg_register_menu_item('page', array(
			'name' => 'message_types',
			'text' => elgg_echo('admin:inbox:message_types'),
			'href' => 'admin/inbox/message_types',
			'priority' => 500,
			'contexts' => array('admin'),
			'section' => 'configure'
		));

		elgg_register_css('inbox.base.css', elgg_get_simplecache_url('css', 'framework/inbox/stylesheet.css'));

		elgg_unregister_page_handler('messages', 'messages_page_handler');
		elgg_register_page_handler($this->config->pagehandler_id, array($this->router, 'handlePages'));

		$action_path = $this->plugin->getPath() . '/actions/';
		elgg_register_action("hypeInbox/settings/save", $action_path . 'settings/save.php', 'admin');
		elgg_register_action('inbox/admin/import', $action_path . 'admin/import.php', 'admin');

		elgg_register_action('messages/send', $action_path . 'messages/send.php');
		elgg_register_action('messages/delete', $action_path . 'messages/delete.php');
		elgg_register_action('messages/markread', $action_path . 'messages/markread.php');
		elgg_register_action('messages/markunread', $action_path . 'messages/markunread.php');

		// Third party integrations
		elgg_register_plugin_hook_handler('config:user_types', 'framework:inbox', array($this->hooks, 'filterUserTypes'));

		// Menu
		elgg_register_plugin_hook_handler('register', 'menu:page', array($this->hooks, 'setupPageMenu'));
		elgg_register_plugin_hook_handler('register', 'menu:inbox', array($this->hooks, 'setupInboxMenu'));
		elgg_register_plugin_hook_handler('register', 'menu:inbox:thread', array($this->hooks, 'setupInboxThreadMenu'));
		elgg_register_plugin_hook_handler('register', 'menu:entity', array($this->hooks, 'setupMessageMenu'));

		// Replace user hover menu items
		elgg_unregister_plugin_hook_handler('register', 'menu:user_hover', 'messages_user_hover_menu');
		elgg_register_plugin_hook_handler('register', 'menu:user_hover', array($this->hooks, 'setupUserHoverMenu'));

		// URLs
		elgg_register_plugin_hook_handler('entity:url', 'object', array($this->hooks, 'handleMessageURL'));
		elgg_register_plugin_hook_handler('entity:icon:url', 'object', array($this->hooks, 'handleMessageIconURL'));
	}

}
