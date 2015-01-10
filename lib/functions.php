<?php

namespace hypeJunction\Inbox;

use ElggEntity;
use ElggObject;
use ElggUser;
use hypeJunction\Lists\ElggList;

/**
 * Returns an array of predefined and admin defined message types
 * @return array
 */
function get_message_types() {
	$policy = new Config;
	return $policy->getMessageTypes();
}

/**
 * Returns an array of define sender and recipient types
 * @return array
 */
function get_user_types() {
	$policy = new Config;
	return $policy->getUserTypes();
}

/**
 * Returns an array of existing <user>-<user> relationship names
 * @return array
 */
function get_user_relationships() {
	$policy = new Config;
	return $policy->getUserRelationships();
}

/**
 * Returns an array of existing <user>-<group> relationship names
 * @return array
 */
function get_user_group_relationships() {
	$policy = new Config;
	return $policy->getUserGroupRelationships();
}

/**
 * Get userpicker tokeninput options based on the current message type config
 *
 * @param string   $message_type Current message type
 * @param ElggUser $user         Sender
 * @return array An array of options
 */
function get_userpicker_options($message_type = null, $user = null) {
	$userpicker = new Userpicker($message_type, $user);
	return $userpicker->getFilterOptions();
}

/**
 * Check if the user is an admin
 * 
 * @param ElggUser $user User
 * @return boolean
 */
function is_admin_user($user) {
	if (!elgg_instanceof($user, 'user')) {
		return false;
	}

	return elgg_is_admin_user($user->guid);
}

/**
 * Get admin users getter options callback
 * @return array
 */
function admin_getter_options() {
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
function get_incoming_message_types($user = null) {

	$return = array();

	if (!elgg_instanceof($user)) {
		$user = elgg_get_logged_in_user_entity();
		if (!$user) {
			return $return;
		}
	}

	$message_types = elgg_get_config('inbox_message_types');
	$user_types = elgg_get_config('inbox_user_types');

	foreach ($message_types as $type => $options) {

		if ($type == HYPEINBOX_NOTIFICATION) {
			$methods = (array) get_user_notification_settings($user->guid);
			if (!array_key_exists('site', $methods)) {
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
			if ($validator && is_callable($validator) && call_user_func($validator, $user, $recipient_type)) {
				$return[] = $type;
				break;
			}
		}
	}

	return $return;
}

/**
 * Get message types the user can send
 * 
 * @param ElggUser $user User
 * @return array An array of message types
 */
function get_outgoing_message_types($user = null) {

	$return = array();

	if (!elgg_instanceof($user)) {
		$user = elgg_get_logged_in_user_entity();
		if (!$user) {
			return $return;
		}
	}

	$message_types = elgg_get_config('inbox_message_types');
	$user_types = elgg_get_config('inbox_user_types');

	foreach ($message_types as $type => $options) {

		$policies = $options['policy'];

		if (!$policies) {
			if ($type != HYPEINBOX_NOTIFICATION) {
				$return[] = $type;
			}
			continue;
		}

		$getter_options = get_userpicker_options($type, $user);
		$getter_options['count'] = true;

		$valid_recipients_count = elgg_get_entities($getter_options);

		foreach ($policies as $policy) {

			$sender_type = $policy['sender'];

			if ($sender_type == 'all' && $valid_recipients_count) {
				$return[] = $type;
				break;
			}

			$validator = $user_types[$sender_type]['validator'];
			if ($validator && is_callable($validator) && call_user_func($validator, $user, $sender_type) && $valid_recipients_count) {
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
function count_unread_messages($message_type = null, $user = null) {
	if (is_null($user)) {
		$user = elgg_get_logged_in_user_entity();
	}
	if (!elgg_instanceof($user, 'user')) {
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
function prepare_form_vars($recipient_guids = null, $message_type = null, $entity = null) {

	if (!$message_type) {
		$message_type = Message::TYPE_PRIVATE;
	}

	$recipient_guids = Group::create($recipient_guids)->guids();
	
	$policy = new Config();
	$ruleset = $policy->getRuleset($message_type);

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
 * Send a message to specified recipients
 *
 * @param array $options An array of options
 *   'sender'       => Sender guid or entity
 *   'recipients'   => Recipient guid or entity, or an array of guids or entities
 *   'subject'      => Message subject
 *   'hash'         => Message hash
 *   'body'         => Message body
 *   'message_type' => Message type
 *   'attachments'  => Entities to attach, or their guids
 * @return Message|false Sent message or false on error
 */
function send_message($options = array()) {

	$message = Message::construct($options);
	$guid = $message->send();
	return ($guid) ? get_entity($guid) : false;
}

/**
 * Validate that user has a role
 *
 * @param ElggUser $user      User
 * @param string   $role_name Role
 * @return boolean
 */
function has_role($user, $role_name) {

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
function getter_direct_relationship_exists($relationship) {

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
function getter_role_exists($role_name) {

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
 * @return Message[]
 */
function get_unhashed_messages(array $options = array()) {

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

	return elgg_get_entities($options);
}

/**
 * Tokeninput callback
 * 
 * @param string $term Query string
 * @return array
 */
function recipient_tokeninput_callback($term) {

	$term = sanitize_string($term);

	// replace mysql vars with escaped strings
	$q = str_replace(array('_', '%'), array('\_', '\%'), $term);

	$message_type = get_input('message_type', Message::TYPE_PRIVATE);
	$options = get_userpicker_options($message_type);
	
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
 * Get page owner from URL segments
 * Defaults to logged in user
 * 
 * @param array $segments URL segments
 * @return ElggEntity
 */
function get_page_owner($segments = array()) {

	$owner = elgg_get_logged_in_user_entity();

	if (is_array($segments)) {
		foreach ($segments as $segment) {
			$user = get_user_by_username($segment);
			if (elgg_instanceof($user)) {
				$owner = $user;
				break;
			}
		}
	}
	
	return $owner;
}

/**
 * Get entity URL wrapped in an <a></a> tag
 * @return string
 */
function get_linked_entity_name($entity) {
	if (elgg_instanceof($entity)) {
		return elgg_view('output/url', array(
			'text' => $entity->getDisplayName(),
			'href' => $entity->getURL(),
			'is_trusted' => true,
		));
	}
	return '';
}