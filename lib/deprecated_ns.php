<?php

namespace hypeJunction\Inbox;

use ElggBatch;
use ElggEntity;
use ElggObject;
use ElggUser;

/**
 * Returns an array of predefined and admin defined message types
 * @return array
 * @deprecated since version 3.1
 */
function get_message_types() {
	return hypeInbox()->config->getMessageTypes();
}

/**
 * Returns an array of define sender and recipient types
 * @return array
 * @deprecated since version 3.1
 */
function get_user_types() {
	return hypeInbox()->config->getUserTypes();
}

/**
 * Returns an array of existing <user>-<user> relationship names
 * @return array
 * @deprecated since version 3.1
 */
function get_user_relationships() {
	return hypeInbox()->config->getUserRelationships();
}

/**
 * Returns an array of existing <user>-<group> relationship names
 * @return array
 * @deprecated since version 3.1
 */
function get_user_group_relationships() {
	return hypeInbox()->config->getUserGroupRelationships();
}

/**
 * Get userpicker tokeninput options based on the current message type config
 *
 * @param string   $message_type Current message type
 * @param ElggUser $user         Sender
 * @return array An array of options
 * @deprecated since version 3.1
 */
function get_userpicker_options($message_type = null, $user = null) {
	return hypeInbox()->model->getUserQueryOptions($message_type, $user);
}

/**
 * Check if the user is an admin
 * 
 * @param ElggUser $user User
 * @return boolean
 * @deprecated since version 3.1
 */
function is_admin_user($user) {
	return hypeInbox()->model->isAdminUser($user);
}

/**
 * Get admin users getter options callback
 * @return array
 * @deprecated since version 3.1
 */
function admin_getter_options() {
	return hypeInbox()->model->getAdminQueryOptions();
}

/**
 * Get message types the user can receive
 * 
 * @param ElggUser $user User
 * @return array An array of message types
 * @deprecated since version 3.1
 */
function get_incoming_message_types($user = null) {
	return hypeInbox()->model->getIncomingMessageTypes($user);
}

/**
 * Get message types the user can send
 * 
 * @param ElggUser $user User
 * @return array An array of message types
 * @deprecated since version 3.1
 */
function get_outgoing_message_types($user = null) {
	return hypeInbox()->model->getOutgoingMessageTypes($user);
}

/**
 * Count unread messages of a given type received by a given user
 * 
 * @param string   $message_type Message type
 * @param ElggUser $user         User
 * @return int Count of unread messages
 * @deprecated since version 3.1
 */
function count_unread_messages($message_type = null, $user = null) {
	return hypeInbox()->model->countUnreadMessages($message_type, $user);
}

/**
 * Prepare compose form variables
 *
 * @param integer    $recipient_guids GUIDs of recipients if any
 * @param string     $message_type    Type of the message being composed
 * @param ElggObject $entity          Message to which the reply is to be sent
 * @return array An array of form variables
 * @deprecated since version 3.1
 */
function prepare_form_vars($recipient_guids = null, $message_type = null, $entity = null) {
	return hypeInbox()->model->prepareFormValues($recipient_guids, $message_type, $entity);
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
 * @deprecated since version 3.1
 */
function send_message($options = array()) {
	return hypeInbox()->actions->sendMessage($options);
}

/**
 * Validate that user has a role
 *
 * @param ElggUser $user      User
 * @param string   $role_name Role
 * @return boolean
 * @deprecated since version 3.1
 */
function has_role($user, $role_name) {
	return hypeInbox()->model->hasRole($user, $role_name);
}

/**
 * Prepare getter options where direct relationship exists
 * 
 * @param string $relationship Relationship (role) name
 * @return array
 * @deprecated since version 3.1
 */
function getter_direct_relationship_exists($relationship) {
	return hypeInbox()->model->getDirectRelationshipTestQuery($relationship);
}

/**
 * Get roles plugin getter options
 * 
 * @param string $role_name Role name
 * @return array
 * @deprecated since version 3.1
 */
function getter_role_exists($role_name) {
	return hypeInbox()->model->getRoleTestQuery($role_name);
}

/**
 * Get messages that have not been assigned a hash
 * 
 * @param array $options Getter options
 * @return Message[]
 * @deprecated since version 3.1
 */
function get_unhashed_messages(array $options = array()) {
	$batch = hypeInbox()->model->getUnhashedMessages($options);
	if (!$batch instanceof ElggBatch) {
		return $batch;
	}
	$messages = array();
	foreach ($batch as $message) {
		$messages[] = $message;
	}
	return $messages;
}

/**
 * Tokeninput callback
 * 
 * @param string $term Query string
 * @return array
 * @deprecated since version 3.1
 */
function recipient_tokeninput_callback($term) {
	return hypeInbox()->model->searchRecipients($term);
}

/**
 * Get page owner from URL segments
 * Defaults to logged in user
 * 
 * @param array $segments URL segments
 * @return ElggEntity
 * @deprecated since version 3.1
 */
function get_page_owner($segments = array()) {
	return hypeInbox()->router->getPageOwner($segments);
}

/**
 * Get entity URL wrapped in an <a></a> tag
 * @return string
 * @deprecated since version 3.1
 */
function get_linked_entity_name($entity) {
	return hypeInbox()->model->getLinkTag($entity);
}
