<?php

/**
 * Get user types
 * @return array
 * @deprecated 3.0
 */
function hj_inbox_get_user_types() {
	return hypeJunction\Inbox\get_user_types();
}

/**
 * Get user-user relationships
 * @return array
 * @deprecated 3.0
 */
function hj_inbox_get_user_relationships() {
	return hypeJunction\Inbox\get_user_relationships();
}

/**
 * Get user-group relationships
 * @return array
 * @deprecated 3.0
 */
function hj_inbox_get_user_group_relationships() {
	return hypeJunction\Inbox\get_user_group_relationships();
}

/**
 * Get userpicker tokeninput options based on the current message type config
 *
 * @param string   $message_type Current message type
 * @param ElggUser $user         Sender
 * @return array An array of options
 * @deprecated 3.0
 */
function hj_inbox_get_userpicker_options($message_type = null, $user = null) {
	return hypeJunction\Inbox\get_userpicker_options($message_type, $user);
}

/**
 * Check if the user is an admin
 * 
 * @param ElggUser $user User
 * @return boolean
 * @deprecated 3.0
 */
function hj_inbox_is_admin_user($user) {
	return hypeJunction\Inbox\is_admin_user($user);
}

/**
 * Get admin users getter options
 * @return array
 * @deprecated 3.0
 */
function hj_inbox_admin_getter_options() {
	return hypeJunction\Inbox\admin_getter_options();
}

/**
 * Get message types the user can receive
 * 
 * @param ElggUser $user User
 * @return array An array of message types
 * @deprecated 3.0
 */
function hj_inbox_get_incoming_message_types($user = null) {
	return hypeJunction\Inbox\get_incoming_message_types($user);
}

/**
 * Get message types the user can send
 * 
 * @param ElggUser $user User
 * @return array An array of message types
 * @deprecated 3.0
 */
function hj_inbox_get_outgoing_message_types($user = null) {
	return hypeJunction\Inbox\get_outgoing_message_types($user);
}

/**
 * Count unread messages of a given type received by a given user
 * 
 * @param string   $message_type Message type
 * @param ElggUser $user         User
 * @return int Count of unread messages
 * @deprecated 3.0
 */
function hj_inbox_count_unread_messages($message_type = null, $user = null) {
	return hypeJunction\Inbox\count_unread_messages($message_type, $user);
}

/**
 * Prepare compose form variables
 *
 * @param integer    $recipient_guids GUIDs of recipients if any
 * @param string     $message_type    Type of the message being composed
 * @param ElggObject $entity          Message to which the reply is to be sent
 * @return array An array of form variables
 * @deprecated 3.0
 */
function hj_inbox_prepare_form_vars($recipient_guids = null, $message_type = null, $entity = null) {
	return hypeJunction\Inbox\prepare_form_vars($recipient_guids, $message_type, $entity);
}

/**
 * Send a message to specified recipients
 *
 * @param int   $sender_guid     GUID of the sender entity
 * @param array $recipient_guids An array of recipient GUIDs
 * @param str   $subject         Subject of the message
 * @param str   $message         Body of the message
 * @param str   $message_type    Type of the message
 * @param array $params          Additional parameters, e.g. 'message_hash', 'attachments'
 * @return boolean
 * @deprecated 3.0
 */
function hj_inbox_send_message($sender_guid, $recipient_guids, $subject = '', $message = '', $message_type = '', array $params = array()) {
	$params['sender'] = $sender_guid;
	$params['recipients'] = $recipient_guids;
	$params['subject'] = $subject;
	$params['message_type'] = $message_type;
	return hypeJunction\Inbox\send_message($params);
}

/**
 * Add third party user types/roles to the config array
 * 
 * @param string $hook   "config:user_types"
 * @param string $type   "framework:inbox"
 * @param array  $return User types config array
 * @param array  $params Hook params
 * @return array
 * @deprecated 3.0
 */
function hj_inbox_integrated_user_types($hook, $type, $return, $params) {
	return hypeJunction\Inbox\filter_user_types($hook, $type, $return, $params);
}

/**
 * Validate hypeApprove role
 *
 * @param ElggUser $user      User
 * @param string   $role_name Role
 * @return boolean
 * @deprecated 3.0
 */
function hj_inbox_approve_role_validator($user, $role_name) {
	return hypeJunction\Inbox\has_role($user, $role_name);
}

/**
 * Get hypeApprove role getter options
 * @return array
 * @deprecated 3.0
 */
function hj_inbox_approve_role_getter_options($role_name) {
	return hypeJunction\Inbox\getter_direct_relationship_exists($role_name);
}

/**
 * Validate hypeObserver role
 *
 * @param ElggUser $user      User
 * @param string   $role_name Role
 * @return boolean
 * @deprecated 3.0
 */
function hj_inbox_observer_role_validator($user, $role_name) {
	return hypeJunction\Inbox\has_role($user, $role_name);
}

/**
 * Get hypeObserver role getter options
 * 
 * @param string $role_name Role name
 * @return array
 * @deprecated 3.0
 */
function hj_inbox_observer_role_getter_options($role_name) {
	return hypeJunction\Inbox\getter_direct_relationship_exists($role_name);
}

/**
 * Validate roles plugin role
 *
 * @param ElggUser $user      User
 * @param string   $role_name Role
 * @return boolean
 * @deprecated 3.0
 */
function hj_inbox_roles_role_validator($user, $role_name) {
	return hypeJunction\Inbox\has_role($user, $role_name);
}

/**
 * Get roles plugin getter options
 * 
 * @params string $role_name Role name
 * @return array
 * @deprecated 3.0
 */
function hj_inbox_roles_role_getter_options($role_name) {
	return hypeJunction\Inbox\getter_role_exists($role_name);
}

/**
 * Notification handler
 *
 * @param ElggEntity $from    Sender entity
 * @param ElggUser   $to      Recipient entity
 * @param string     $subject Subject string
 * @param string     $message Message body
 * @param array      $params  Additional params
 * @return void
 * @deprecated 3.0
 */
function hj_inbox_site_notify_handler(ElggEntity $from, ElggUser $to, $subject, $message, array $params = array()) {

}

/**
 * Register title menu items
 *
 * @param ElggEntity $entity Message
 * @return void
 * @deprecated 3.0
 */
function title_menu_setup($entity = null) {

}
