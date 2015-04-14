<?php

namespace hypeJunction\Inbox\Listeners;

use hypeJunction\Inbox\Config;
use hypeJunction\Inbox\Models\Model;
use hypeJunction\Inbox\Services\Router;
use hypeJunction\Inbox\Services\Upgrades;

/**
 * Events service
 */
class Events {

	/**
	 * Scripts to require on system upgrade
	 * @var array
	 */
	private $upgradeScripts = array(
		'activate.php',
	);
	private $config;
	private $router;
	private $model;
	private $upgrades;
	private $queue;

	/**
	 * Constructor
	 * @param Config   $config   Config
	 * @param Router   $router   Router
	 * @param Model $model Model
	 * @param Upgrades $upgrades Upgrades
	 */
	public function __construct(Config $config, Router $router, Model $model, Upgrades $upgrades) {
		$this->config = $config;
		$this->router = $router;
		$this->model = $model;
		$this->upgrades = $upgrades;
		$this->queue = array();
	}

	/**
	 * Run tasks on system init
	 * @return void
	 */
	public function init() {
		elgg_register_event_handler('pagesetup', 'system', array($this, 'pagesetup'));
		elgg_register_event_handler('upgrade', 'system', array($this, 'upgrade'));
	}

	/**
	 * Setup menus on page setup
	 * @return void
	 */
	public function pagesetup() {
		elgg_register_menu_item('page', array(
			'name' => 'message_types',
			'text' => elgg_echo('admin:inbox:message_types'),
			'href' => 'admin/inbox/message_types',
			'priority' => 500,
			'contexts' => array('admin'),
			'section' => 'configure'
		));
	}

	/**
	 * Runs upgrade scripts
	 * @return bool
	 */
	public function upgrade() {
		if (elgg_is_admin_logged_in()) {
			foreach ($this->upgradeScripts as $script) {
				$path = $this->config->getPath() . $script;
				if (file_exists($path)) {
					require_once $path;
				}
			}
			$this->upgrades->runUpgrades();
		}
		return true;
	}

}
