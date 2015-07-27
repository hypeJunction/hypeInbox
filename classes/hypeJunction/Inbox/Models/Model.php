<?php

namespace hypeJunction\Inbox\Models;

use ElggBatch;
use ElggObject;
use ElggUser;
use hypeJunction\Inbox\Config;
use hypeJunction\Inbox\Group;
use hypeJunction\Inbox\Inbox;
use hypeJunction\Inbox\Message;
use hypeJunction\Inbox\Userpicker;
use hypeJunction\Lists\ElggList;
use stdClass;

class Model {

	const EGE = 'elgg_get_entities';
	const EGE_METADATA = 'elgg_get_entities_from_metadata';
	const EGE_RELATIONSHIP = 'elgg_get_entities_from_relationship';

	/**
	 * Config
	 * @var Config
	 */
	private $config;
	private $incomingMessageTypes = array();
	private $outgoingMessageTypes = array();

	/**
	 * Constructor
	 * @param Config $config
	 */
	public function __construct(Config $config) {
		$this->config = $config;
	}

	/**
	 * Get userpicker tokeninput options based on the current message type config
	 *
	 * @param string   $message_type Current message type
	 * @param ElggUser $user         Sender
	 * @return array An array of options
	 */
	public function getUserQueryOptions($message_type = null, $user = null) {
		$userpicker = new Userpicker($message_type, $user);
		return $userpicker->getFilterOptions();
	}

	/**
	 * Check if the user is an admin
	 *
	 * @param ElggUser $user User
	 * @return boolean
	 */
	public function isAdminUser($user) {
		if (!elgg_instanceof($user, 'user')) {
			return false;
		}

		return elgg_is_admin_user($user->guid);
	}

	/**
	 * Get admin users getter options callback
	 * @return array
	 */
	public function getAdminQueryOptions() {
		return array(
			'wheres' => array(
				"ue.admin = 'yes'"
			)
		);
	}

	/**
	 * Get message types the user can receive
	 *
	 * @param ElggUser $user User
	 * @return array An array of message types
	 */
	public function getIncomingMessageTypes($user = null) {

		$return = array();

		if (!elgg_instanceof($user)) {
			$user = elgg_get_logged_in_user_entity();
			if (!$user) {
				return $return;
			}
		}

		if (isset($this->incomingMessageTypes[$user->guid])) {
			return $this->incomingMessageTypes[$user->guid];
		}

		$message_types = $this->config->getMessageTypes();
		$user_types = $this->config->getUserTypes();

		foreach ($message_types as $type => $options) {

			if ($type == Config::TYPE_NOTIFICATION) {
				$methods = get_user_notification_settings($user->guid);
				if (!$methods || !isset($methods->site)) {
					continue;
				}
			}

			$policies = $options['policy'];
			if (!$policies) {
				$return[] = $type;
				continue;
			}

			foreach ($policies as $policy) {

				$recipient_type = $policy['recipient'];

				if ($recipient_type == 'all') {
					$return[] = $type;
					break;
				}

				$validator = $user_types[$recipient_type]['validator'];
				if (is_callable($validator) && call_user_func($validator, $user, $recipient_type)) {
					$return[] = $type;
					break;
				}
			}
		}

		$this->incomingMessageTypes[$user->guid] = $return;
		return $return;
	}

	/**
	 * Get message types the user can send
	 *
	 * @param ElggUser $user User
	 * @return array An array of message types
	 */
	public function getOutgoingMessageTypes($user = null) {

		$return = array();

		if (!elgg_instanceof($user)) {
			$user = elgg_get_logged_in_user_entity();
			if (!$user) {
				return $return;
			}
		}

		if (isset($this->outgoingMessageTypes[$user->guid])) {
			return $this->outgoingMessageTypes[$user->guid];
		}
		
		$message_types = $this->config->getMessageTypes();
		$user_types = $this->config->getUserTypes();

		foreach ($message_types as $type => $options) {

			$policies = $options['policy'];

			if (!$policies) {
				if ($type != Config::TYPE_NOTIFICATION) {
					$return[] = $type;
				}
				continue;
			}

			$getter_options = $this->getUserQueryOptions($type, $user);
			$getter_options['count'] = true;

			$valid_recipients_count = $this->getEntities($getter_options);

			foreach ($policies as $policy) {

				$sender_type = $policy['sender'];

				if ($sender_type == 'all' && $valid_recipients_count) {
					$return[] = $type;
					break;
				}

				$validator = $user_types[$sender_type]['validator'];
				if ($valid_recipients_count && is_callable($validator) && call_user_func($validator, $user, $sender_type)) {
					$return[] = $type;
					break;
				}
			}
		}

		return $return;
	}

	/**
	 * Count unread messages of a given type received by a given user
	 *
	 * @param string   $message_type Message type
	 * @param ElggUser $user         User
	 * @return int Count of unread messages
	 */
	public function countUnreadMessages($message_type = null, $user = null) {
		if (is_null($user)) {
			$user = elgg_get_logged_in_user_entity();
		}
		if (!$user instanceof ElggUser) {
			return 0;
		}
		return Inbox::countUnread($user, $message_type);
	}

	/**
	 * Prepare compose form variables
	 *
	 * @param integer    $recipient_guids GUIDs of recipients if any
	 * @param string     $message_type    Type of the message being composed
	 * @param ElggObject $entity          Message to which the reply is to be sent
	 * @return array An array of form variables
	 */
	public function prepareFormValues($recipient_guids = null, $message_type = null, $entity = null) {

		if (!$message_type) {
			$message_type = Message::TYPE_PRIVATE;
		}

		$recipient_guids = Group::create($recipient_guids)->guids();

		$ruleset = hypeInbox()->config->getRuleset($message_type);

		$values = array(
			'entity' => $entity,
			'multiple' => $ruleset->allowsMultipleRecipients(),
			'has_subject' => $ruleset->hasSubject(),
			'allows_attachments' => $ruleset->allowsAttachments(),
			'subject' => ($entity) ? "Re: $entity->title" : '',
			'body' => '',
			'recipient_guids' => $recipient_guids,
			'message_type' => $message_type,
		);

		if (elgg_is_sticky_form('messages')) {
			$sticky = elgg_get_sticky_values('messages');
			foreach ($sticky as $field => $value) {
				if ($field == 'recipient_guids' && is_string($value)) {
					$value = string_to_tag_array($value);
				}
				$values[$field] = $value;
			}
		}

		elgg_clear_sticky_form('messages');
		return $values;
	}

	/**
	 * Validate that user has a role
	 *
	 * @param ElggUser $user      User
	 * @param string   $role_name Role
	 * @return boolean
	 */
	public function hasRole($user, $role_name) {

		switch ($role_name) {
			case 'editor' :
				if (is_callable('hj_approve_is_editor')) {
					return call_user_func('hj_approve_is_editor', $user);
				}
				break;

			case 'supervisor' :
				if (is_callable('hj_approve_is_supervisor')) {
					return call_user_func('hj_approve_is_supervisor', $user);
				}
				break;
			case 'observer' :
				if (is_callable('hj_observer_is_observer')) {
					return call_user_func('hj_observer_is_observer', $user);
				}
				break;
		}

		if (is_callable('roles_has_role')) {
			return call_user_func('roles_has_role', $user, $role_name);
		}

		return false;
	}

	/**
	 * Prepare getter options where direct relationship exists
	 *
	 * @param string $relationship Relationship (role) name
	 * @return array
	 */
	public function getDirectRelationshipTestQuery($relationship) {

		global $INBOX_TABLE_ITERATOR;
		$INBOX_TABLE_ITERATOR++;

		$table = "inb$INBOX_TABLE_ITERATOR";

		$relationship = sanitize_string($relationship);

		$dbprefix = elgg_get_config('dbprefix');
		return array(
			'wheres' => array(
				"EXISTS (SELECT * FROM {$dbprefix}entity_relationships {$table}
				WHERE {$table}.guid_one = e.guid AND {$table}.relationship = '{$relationship}')"
			)
		);
	}

	/**
	 * Get roles plugin getter options
	 *
	 * @param string $role_name Role name
	 * @return array
	 */
	public function getRoleTestQuery($role_name) {

		if (is_callable('roles_get_role_by_name')) {
			$role = call_user_func('roles_get_role_by_name', $role_name);
		}

		$role_guid = (elgg_instanceof($role)) ? $role->guid : ELGG_ENTITIES_NO_VALUE;

		global $INBOX_TABLE_ITERATOR;
		$INBOX_TABLE_ITERATOR++;

		$table = "inb$INBOX_TABLE_ITERATOR";

		$dbprefix = elgg_get_config('dbprefix');
		return array(
			'wheres' => array(
				"EXISTS (SELECT * FROM {$dbprefix}entity_relationships {$table}
				WHERE {$table}.guid_one = e.guid AND {$table}.relationship = 'has_role' AND {$table}.guid_two = {$role_guid})"
			)
		);
	}

	/**
	 * Get messages that have not been assigned a hash
	 *
	 * @param array $options Getter options
	 * @return ElggBatch
	 */
	public function getUnhashedMessages(array $options = array()) {

		$name_id = elgg_get_metastring_id('msgHash');
		$dbprefix = elgg_get_config('dbprefix');

		$defaults = array(
			'types' => 'object',
			'subtypes' => Message::SUBTYPE,
			'wheres' => array(
				"NOT EXISTS (SELECT 1 FROM {$dbprefix}metadata md WHERE md.entity_guid = e.guid
			AND md.name_id = {$name_id})"
			),
			'order_by' => 'e.guid ASC',
		);

		$options = array_merge($defaults, $options);

		return $this->getEntities($options);
	}

	/**
	 * Tokeninput callback
	 *
	 * @param string $term Query string
	 * @return array
	 */
	public function searchRecipients($term) {

		$term = sanitize_string($term);
		
		// replace mysql vars with escaped strings
		$q = str_replace(array('_', '%'), array('\_', '\%'), $term);

		$message_type = get_input('message_type', Message::TYPE_PRIVATE);
		$options = $this->getUserQueryOptions($message_type);

		$list = new ElggList($options);
		$list->setSearchQuery(array('user' => $q));

		$batch = $list->getItems();
		/* @var \ElggBatch $batch */

		$results = array();
		foreach ($batch as $b) {
			$results[] = $b;
		}

		return $results;
	}

	/**
	 * Get entity URL wrapped in an <a></a> tag
	 * @return string
	 */
	public function getLinkTag($entity) {
		if (elgg_instanceof($entity)) {
			return elgg_view('output/url', array(
				'text' => $entity->getDisplayName(),
				'href' => $entity->getURL(),
				'is_trusted' => true,
			));
		}
		return '';
	}

	/**
	 * Returns a batch of entities or an array of guids
	 *
	 * @param array    $options  ege* options
	 * @param bool     $as_guids Only return guids
	 * @param callable $ege      ege* callable
	 * @return ElggBatch|array
	 */
	protected function getEntities(array $options = array(), $as_guids = false, callable $ege = null) {

		if (!$ege) {
			$ege = self::EGE;
		}

		if (!is_callable($ege)) {
			return array();
		}

		if (!empty($options['count'])) {
			return call_user_func($ege, $options);
		}

		if ($as_guids) {
			$options['callback'] = array($this, 'rowToGUID');
		}

		return new ElggBatch($ege, $options);
	}

	/**
	 * Callback function for ege* to only return guids
	 * 
	 * @param stdClass $row DB row
	 * @return int
	 */
	public static function rowToGUID($row) {
		return (int) $row->guid;
	}

}
