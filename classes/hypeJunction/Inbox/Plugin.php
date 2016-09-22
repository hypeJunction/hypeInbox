<?php

namespace hypeJunction\Inbox;

use Elgg\Di\DiContainer;
use ElggPlugin;
use hypeJunction\Inbox\Models\Model;

/**
 * Inbox service provider
 *
 * @property-read ElggPlugin   $plugin
 * @property-read Config       $config
 * @property-read HookHandlers $hooks
 * @property-read Router	   $router
 * @property-read Model        $model
 */
final class Plugin extends DiContainer {

	/**
	 * Constructor
	 *
	 * @param ElggPlugin $plugin Plugin object
	 */
	public function __construct(ElggPlugin $plugin) {

		$this->setValue('plugin', $plugin);

		$this->setFactory('config', function (Plugin $p) {
			return new Config($p->plugin);
		});

		$this->setClassName('hooks', HookHandlers::class);
		
		$this->setClassName('router', Router::class);

		$this->setFactory('model', function (Plugin $c) {
			return new Model($c->config);
		});
	}

	/**
	 * @deprecated 6.0
	 */
	public static function factory() {
		return hypeInbox();
	}

	/**
	 * @deprecated 6.0
	 */
	public function boot() {}

	/**
	 * @deprecated 6.0
	 */
	public function init() {}

}