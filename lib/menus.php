<?php

if (elgg_is_admin_logged_in()) {

	elgg_register_menu_item('page', array(
		'name' => 'message_types',
		'text' => elgg_echo('admin:inbox:message_types'),
		'href' => 'admin/inbox/message_types',
		'priority' => 500,
		'contexts' => array('admin'),
		'section' => 'configure'
	));
}

elgg_register_plugin_hook_handler('register', 'menu:page', 'hj_inbox_page_menu_setup');

// Replace user hover menu items
elgg_unregister_plugin_hook_handler('register', 'menu:user_hover', 'messages_user_hover_menu');
elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'hj_inbox_user_hover_menu_setup');

elgg_register_plugin_hook_handler('register', 'menu:entity', 'hj_inbox_message_menu_setup');

/**
 * Messages page menu setup
 *
 * @param string $hook Equals 'register'
 * @param string $type Equals 'menu:page'
 * @param array $return An array of menu items
 * @param array $params Additional parameters
 * @return array An array of menu items
 */
function hj_inbox_page_menu_setup($hook, $type, $return, $params) {

	if (!elgg_in_context('messages')) {
		return $return;
	}

	$user = elgg_get_page_owner_entity();

	$return = array();

	$return[] = ElggMenuItem::factory(array(
				'name' => 'inbox',
				'text' => elgg_echo('hj:inbox:inbox'),
				'href' => 'messages/inbox',
				'priority' => 100,
				'class' => 'inbox-load'
	));

	$intypes = hj_inbox_get_incoming_message_types($user);

	if ($intypes) {
		foreach ($intypes as $type) {
			$return[] = ElggMenuItem::factory(array(
						'name' => "inbox:$type",
						'parent_name' => 'inbox',
						'text' => elgg_echo("item:object:message:$type:plural"),
						'href' => "messages/inbox?message_type=$type",
						'class' => 'inbox-load'
			));
		}
	}

	$return[] = ElggMenuItem::factory(array(
				'name' => 'sentmessages',
				'text' => elgg_echo('hj:inbox:sent'),
				'href' => 'messages/sent',
				'priority' => 500,
				'class' => 'inbox-load'
	));

	$outtypes = hj_inbox_get_outgoing_message_types($user);

	if ($outtypes) {
		foreach ($outtypes as $type) {
			$return[] = ElggMenuItem::factory(array(
						'name' => "sent:$type",
						'parent_name' => 'sentmessages',
						'text' => elgg_echo("item:object:message:$type:plural"),
						'href' => "messages/sent?message_type=$type",
						'class' => 'inbox-load'
			));
		}
	}

	return $return;
}

/**
 * Register user hover menu items
 */
function hj_inbox_user_hover_menu_setup($hook, $type, $return, $params) {

	$recipient = elgg_extract('entity', $params);
	$sender = elgg_get_logged_in_user_entity();

	if (!$sender || !$recipient) {
		return $return;
	}

	if ($sender->guid == $recipient->guid) {
		return $return;
	}

	$message_types = elgg_get_config('inbox_message_types');
	$user_types = elgg_get_config('inbox_user_types');

	foreach ($message_types as $type => $options) {

		if ($type == HYPEINBOX_NOTIFICATION) {
			continue;
		}

		$valid = false;

		$policies = $options['policy'];
		if (!$policies) {
			$valid = true;
		} else {

			foreach ($policies as $policy) {

				$valid = false;

				$recipient_type = $policy['recipient'];
				$sender_type = $policy['sender'];
				$relationship = $policy['relationship'];
				$inverse_relationship = $policy['inverse_relationship'];
				$group_relationship = $policy['group_relationship'];

				$recipient_validator = $user_types[$recipient_type]['validator'];
				if ($recipient_type == 'all' ||
						($recipient_validator && is_callable($recipient_validator) && call_user_func($recipient_validator, $recipient, $recipient_type))) {

					$sender_validator = $user_types[$sender_type]['validator'];
					if ($sender_type == 'all' ||
							($sender_validator && is_callable($sender_validator) && call_user_func($sender_validator, $sender, $sender_type))) {

						$valid = true;
						if ($relationship && $relationship != 'all') {
							if ($inverse_relationship) {
								$valid = check_entity_relationship($recipient->guid, $relationship, $sender->guid);
							} else {
								$valid = check_entity_relationship($sender->guid, $relationship, $recipient->guid);
							}
						}
						if ($valid && $group_relationship && $group_relationship != 'all') {
							$dbprefix = elgg_get_config('dbprefix');
							$valid = elgg_get_entities_from_relationship(array(
								'types' => 'group',
								'relationship' => 'member',
								'relationship_guid' => $recipient->guid,
								'count' => true,
								'wheres' => array("EXISTS (SELECT * FROM {$dbprefix}entity_relationships WHERE guid_one = $sender->guid AND relationship = '$group_relationship' AND guid_two = r.guid_two)")
							));
						}
					}
				}

				if ($valid) {
					break;
				}
			}
		}
		if ($valid) {
			$return[] = ElggMenuItem::factory(array(
						'name' => "messages:$type",
						'text' => elgg_echo("hj:inbox:send", array(strtolower(elgg_echo("item:object:message:$type:singular")))),
						'href' => elgg_http_add_url_query_elements("messages/compose", array('message_type' => $type, 'send_to' => $recipient->guid)),
						'section' => 'action'
			));
		}
	}

	return $return;
}

/**
 * Register title menu items
 */
function hj_inbox_title_menu_setup($entity = null) {

	$user = elgg_get_page_owner_entity();

	if (!elgg_instanceof($entity)) {
		$outtypes = hj_inbox_get_outgoing_message_types($user);

		if ($outtypes) {
			foreach ($outtypes as $type) {
				elgg_register_menu_item('title', array(
					'name' => "compose:$type",
					'text' => '<i class="inbox-icon-message-plus icon-small"></i><span>' . elgg_echo("item:object:message:$type:singular") . '</span>',
					'href' => elgg_http_add_url_query_elements("messages/compose", array('message_type' => $type, 'send_to' => get_input('send_to', null))),
					'class' => 'elgg-button elgg-button-action'
				));
			}
		}
	}
}

/**
 * Message menu setup
 *
 * @param string $hook Equals 'register'
 * @param string $type Equals 'menu:entity'
 * @param array $return An array of menu items
 * @param array $params An array of additional parameters
 * @return array An array of menu items
 */
function hj_inbox_message_menu_setup($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);

	if (!elgg_instanceof($entity, 'object', 'messages')) {
		return $return;
	}

	$return = array();

	$message_types = elgg_get_config('inbox_message_types');
	$rules = elgg_extract($entity->msgType, $message_types);

	$return[] = ElggMenuItem::factory(array(
				'name' => 'timestamp',
				'text' => elgg_view_friendly_time($entity->time_created),
				'href' => false,
	));

	if (!elgg_extract('persistent', $rules, false) && ($params['full_view'] || elgg_in_context('inbox-table'))) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'delete',
					'text' => '<i class="inbox-icon-delete icon-small"></i>',
					'title' => elgg_echo('hj:inbox:delete'),
					'href' => (elgg_in_context('inbox-table')) ? "action/messages/delete?hashes[]=$entity->msgHash&owner_guid=$entity->owner_guid" : "action/messages/delete?guids[]=$entity->guid",
					'is_action' => true,
					'data-confirm' => (elgg_in_context('inbox-table')) ? elgg_echo('hj:inbox:delete:thread:confirm') : elgg_echo('hj:inbox:delete:message:confirm')
		));
	}

	if (elgg_in_context('inbox-table')) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'markread',
					'text' => '<i class="inbox-icon-markread icon-small"></i>',
					'title' => elgg_echo('hj:inbox:markread'),
					'href' => "action/messages/markread?hashes[]=$entity->msgHash&owner_guid=$entity->owner_guid",
					'is_action' => true,
					'item_class' => ($params['counter']['unread'] > 0) ? 'visible' : 'hidden'
		));

		$return[] = ElggMenuItem::factory(array(
					'name' => 'markunread',
					'text' => '<i class="inbox-icon-markunread icon-small"></i>',
					'title' => elgg_echo('hj:inbox:markunread'),
					'href' => "action/messages/markunread?hashes[]=$entity->msgHash&owner_guid=$entity->owner_guid",
					'is_action' => true,
					'item_class' => ($params['counter']['count'] > $params['counter']['unread']) ? 'visible' : 'hidden'
		));
	} else {
		if ($params['full_view']) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'markread',
						'text' => '<i class="inbox-icon-markread icon-small"></i>',
						'title' => elgg_echo('hj:inbox:markread'),
						'href' => "action/messages/markread?guids[]=$entity->guid",
						'is_action' => true,
						'item_class' => ($entity->readYet) ? 'hidden' : ''
			));

			$return[] = ElggMenuItem::factory(array(
						'name' => 'markunread',
						'text' => '<i class="inbox-icon-markunread icon-small"></i>',
						'title' => elgg_echo('hj:inbox:markunread'),
						'href' => "action/messages/markunread?guids[]=$entity->guid",
						'is_action' => true,
						'item_class' => (!$entity->readYet) ? 'hidden' : ''
			));
		}
	}

	return $return;
}