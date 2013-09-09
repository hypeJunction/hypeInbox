<?php

// Third party integrations
elgg_register_plugin_hook_handler('config:user_types', 'framework:inbox', 'hj_inbox_integrated_user_types');

/**
 * Add third party user types/roles to the config array
 */
function hj_inbox_integrated_user_types($hook, $type, $return, $params) {

	if (elgg_is_active_plugin('hypeApprove')) {
		$return['editor'] = array(
			'validator' => 'hj_inbox_approve_role_validator',
			'getter' => 'hj_inbox_approve_role_getter_options',
		);
		$return['supervisor'] = array(
			'validator' => 'hj_approve_is_supervisor',
			'getter' => 'hj_inbox_approve_role_getter_options',
		);
	}

	if (elgg_is_active_plugin('hypeObserver')) {
		$return['observer'] = array(
			'validator' => 'hj_inbox_observer_role_validator',
			'getter' => 'hj_inbox_observer_role_getter_options',
		);
	}

	if (elgg_is_active_plugin('roles')) {
		$roles = roles_get_all_selectable_roles();
		foreach ($roles as $role) {
			$return[$role->name] = array(
				'validator' => 'hj_inbox_roles_role_validator',
				'getter' => 'hj_inbox_roles_role_getter_options'
			);
		}
	}

	return $return;
}

/**
 * Validate hypeApprove role
 *
 * @param ElggUser $user
 * @param string $role_name
 * @return boolean
 */
function hj_inbox_approve_role_validator($user, $role_name) {

	if (!elgg_is_active_plugin('hypeApprove')) {
		return false;
	}

	if ($role_name == 'editor') {
		return hj_approve_is_editor($user);
	} else if ($role_name == 'supervisor') {
		return hj_approve_is_supervisor($user);
	}
}

/**
 * Get hypeApprove role getter options
 * @return array
 */
function hj_inbox_approve_role_getter_options($role_name) {

	if (!elgg_is_active_plugin('hypeApprove')) {
		return array();
	}

	global $INBOX_TABLE_ITERATOR;
	$INBOX_TABLE_ITERATOR++;

	$table = "inb$INBOX_TABLE_ITERATOR";

	$dbprefix = elgg_get_config('dbprefix');
	return array(
		'types' => 'user',
		'wheres' => array(
			"EXISTS (SELECT * FROM {$dbprefix}entity_relationships $table WHERE $table.guid_one = e.guid AND $table.relationship = '$role_name')"
		)
	);
}


/**
 * Validate hypeObserver role
 *
 * @param ElggUser $user
 * @param string $role_name
 * @return boolean
 */
function hj_inbox_observer_role_validator($user, $role_name) {

	if (elgg_is_active_plugin('hypeObserver')
			&& $role_name == 'observer') {
		return hj_observer_is_observer($user);
	}

	return false;
}

/**
 * Get hypeObserver role getter options
 * @return array
 */
function hj_inbox_observer_role_getter_options($role_name) {

	if (!elgg_is_active_plugin('hypeObserver')) {
		return array();
	}

	global $INBOX_TABLE_ITERATOR;
	$INBOX_TABLE_ITERATOR++;

	$table = "inb$INBOX_TABLE_ITERATOR";

	$dbprefix = elgg_get_config('dbprefix');
	return array(
		'types' => 'user',
		'wheres' => array(
			"EXISTS (SELECT * FROM {$dbprefix}entity_relationships $table WHERE $table.guid_one = e.guid AND $table.relationship = '$role_name')"
		)
	);
}

/**
 * Validate roles plugin role
 *
 * @param ElggUser $user
 * @param string $role_name
 * @return boolean
 */
function hj_inbox_roles_role_validator($user, $role_name) {

	if (!elgg_is_active_plugin('roles')) {
		return false;
	}

	return roles_has_role($user, $role_name);
}

/**
 * Get roles plugin getter options
 * @return array
 */
function hj_inbox_roles_role_getter_options($role_name) {

	if (!elgg_is_active_plugin('roles')) {
		return array();
	}

	$role = roles_get_role_by_name($role_name);
	if (!$role) {
		return array();
	}

	global $INBOX_TABLE_ITERATOR;
	$INBOX_TABLE_ITERATOR++;

	$table = "inb$INBOX_TABLE_ITERATOR";

	$dbprefix = elgg_get_config('dbprefix');
	return array(
		'types' => 'user',
		'wheres' => array(
			"EXISTS (SELECT * FROM {$dbprefix}entity_relationships $table WHERE $table.guid_one = e.guid AND $table.relationship = 'has_role' AND $table.guid_two = $role->guid)"
		)
	);
}