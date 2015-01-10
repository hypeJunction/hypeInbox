<?php

namespace hypeJunction\Inbox;

use ElggUser;

class Config {

	private $dbprefix;
	static $messageTypes;
	static $userTypes;
	static $userRelationships;
	static $userGroupRelationships;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->dbprefix = elgg_get_config('dbprefix');
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

		if (isset(self::$userGroupRelationships)) {
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
		$value = elgg_get_plugin_setting($name, HYPEINBOX);
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
