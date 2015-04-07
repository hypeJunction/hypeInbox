<?php

namespace hypeJunction\Inbox;

class Config {

	private $dbprefix;
	private $plugin;
	private $settings;
	private $config = array(
		'legacy_mode' => true,
		'pagehandler_id' => 'messages',
	);
	static $messageTypes;
	static $userTypes;
	static $userRelationships;
	static $userGroupRelationships;

	const PLUGIN_ID = 'hypeInbox';
	const TYPE_NOTIFICATION = '__notification';
	const TYPE_PRIVATE = '__private';

	/**
	 * Constructor
	 * @param ElggPlugin $plugin ElggPlugin
	 */
	public function __construct($plugin = null) {
		if (!$plugin) {
			return self::factory();
		}
		$this->plugin = $plugin;
		$this->dbprefix = elgg_get_config('dbprefix');
	}

	/**
	 * Config factory
	 * @return Config
	 */
	public static function factory() {
		$plugin = elgg_get_plugin_from_id(self::PLUGIN_ID);
		return new Config($plugin);
	}

	/**
	 * Initializes config values on system init
	 * @return void
	 */
	public function setLegacyConfig() {

		// legacy definitions
		define('HYPEINBOX', self::PLUGIN_ID);
		define('HYPEINBOX_NOTIFICATION', self::TYPE_NOTIFICATION);
		define('HYPEINBOX_PRIVATE', self::TYPE_PRIVATE);

		//$message_types = $this->getMessageTypes();
		//elgg_set_config('inbox_message_types', $message_types);
		//elgg_set_config('inbox_user_types', $this->getUserTypes());
		//elgg_set_config('inbox_user_relationships', $this->getUserRelationships());
		//elgg_set_config('inbox_user_group_relationships', $this->getUserGroupRelationships());
	}

	/**
	 * Registers label translations
	 * @return void
	 */
	public function registerLabels() {
		$message_types = $this->getMessageTypes();
		
		// Register label translations for custom message types
		foreach ($message_types as $type => $options) {
			$ruleset = $this->getRuleset($type);
			add_translation('en', array(
				$ruleset->getSingularLabel(false) => $ruleset->getSingularLabel('en'),
				$ruleset->getPluralLabel(false) => $ruleset->getPluralLabel('en')
			));
		}
	}

	/**
	 * Returns all plugin settings
	 * @return array
	 */
	public function all() {
		if (!isset($this->settings)) {
			$this->settings = array_merge($this->config, $this->plugin->getAllSettings());
		}
		return $this->settings;
	}

	/**
	 * Returns a plugin setting
	 *
	 * @param string $name Setting name
	 * @return mixed
	 */
	public function get($name, $default = null) {
		return elgg_extract($name, $this->all(), $default);
	}

	/**
	 * Returns plugin path
	 * @return string
	 */
	public function getPath() {
		return $this->plugin->getPath();
	}

	/**
	 * Returns an array of predefined and admin defined message types
	 * @return array
	 */
	public function getMessageTypes() {
		if (!isset(self::$messageTypes)) {
			$default_message_types = $this->getSetting('default_message_types');
			$message_types = $this->getSetting('message_types');
			self::$messageTypes = array_merge($default_message_types, $message_types);
		}
		return self::$messageTypes;
	}

	/**
	 * Returns a set of rules for a given message type
	 * 
	 * @param string $message_type Message type
	 * @return Ruleset
	 */
	public function getRuleset($message_type = '') {
		$ruleset = array();
		$types = $this->getMessageTypes();
		if (isset($types[$message_type])) {
			$ruleset = $types[$message_type];
		}
		return new Ruleset($message_type, $ruleset);
	}

	/**
	 * Filters an array of configured sender and recipient types
	 * These will be used when applying message type rules
	 * - 'validation' callback function will be used to identify whether or not a user belongs to that user type group 
	 *    (user entity will be passed to this callback function)
	 * - 'getter' callback function will be used to populate tokeninput options
	 * 
	 * Use 'config:user_types','framework:inbox' plugin hook to extend this array
	 * Callbacks should only return an array with 'joins' and 'wheres'. User table will be joined automatically with 'ue' prefix
	 * @return array
	 */
	public function getUserTypes() {
		if (!isset(self::$userTypes)) {
			$config = array(
				'all' => array(),
				'admin' => array(
					'validator' => __NAMESPACE__ . '\\is_admin_user',
					'getter' => __NAMESPACE__ . '\\admin_getter_options'
				),
			);

			self::$userTypes = $this->triggerHook('config:user_types', 'framework:inbox', null, $config);
		}
		return self::$userTypes;
	}

	/**
	 * Get a list of existing <user>-<user> relationships
	 * @return array
	 */
	public function getUserRelationships() {

		if (!isset(self::$userRelationships)) {
			$relationships = array();

			$query = "SELECT DISTINCT(er.relationship)
				FROM {$this->dbprefix}entity_relationships er
				JOIN {$this->dbprefix}entities e1 ON e1.guid = er.guid_one
				JOIN {$this->dbprefix}entities e2 ON e2.guid = er.guid_two
				WHERE e1.type = 'user' AND e2.type = 'user'
				ORDER BY er.relationship ASC";

			$data = $this->getData($query);
			foreach ($data as $rel) {
				$relationships[] = $rel->relationship;
			}

			self::$userRelationships = $relationships;
		}
		return self::$userRelationships;
	}

	/**
	 * Get a list of existing <user>-<group> relationships
	 * @return array
	 */
	public function getUserGroupRelationships() {

		if (!isset(self::$userGroupRelationships)) {
			$relationships = array();

			$query = "SELECT DISTINCT(er.relationship)
				FROM {$this->dbprefix}entity_relationships er
				JOIN {$this->dbprefix}entities e1 ON e1.guid = er.guid_one
				JOIN {$this->dbprefix}entities e2 ON e2.guid = er.guid_two
				WHERE (e1.type = 'user' AND e2.type = 'group')";

			$data = $this->getData($query);
			foreach ($data as $rel) {
				$relationships[] = $rel->relationship;
			}

			self::$userGroupRelationships = $relationships;
		}
		return self::$userGroupRelationships;
	}

	/**
	 * Returns an unserialize plugin setting value
	 * 
	 * @param string $name Plugin setting name
	 * @return array
	 */
	public function getSetting($name = '') {
		$value = $this->get($name);
		return (is_string($value)) ? unserialize($value) : array();
	}

	/**
	 * Triggers a plugin hooks
	 * 
	 * @param string $hook   Hook name
	 * @param string $type   Hook type
	 * @param mixed  $params Hook params
	 * @param mixed  $return Default return value
	 * @return mixed
	 */
	public function triggerHook($hook, $type, $params, $return) {
		if (is_callable('elgg_trigger_plugin_hook')) {
			return elgg_trigger_plugin_hook($hook, $type, $params, $return);
		}
		return $return;
	}

	/**
	 * Returns data rows from mysql tables for a given query
	 * 
	 * @param string $query
	 * @return array
	 */
	public function getData($query, $callback = '') {
		if (is_callable('get_data')) {
			return get_data($query, $callback);
		}
		return array();
	}

}
