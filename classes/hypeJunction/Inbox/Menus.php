<?php

namespace hypeJunction\Inbox;

use ElggMenuItem;

class Menus {

	/**
	 * Messages page menu setup
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:page"
	 * @param array  $return An array of menu items
	 * @param array  $params Additional parameters
	 * @return array An array of menu items
	 */
	public static function setupPageMenu($hook, $type, $return, $params) {

		if (!elgg_in_context('messages')) {
			return $return;
		}

		$user = elgg_get_page_owner_entity();

		$return = array();

		$return[] = ElggMenuItem::factory(array(
			'name' => 'inbox',
			'text' => elgg_echo('inbox:inbox'),
			'href' => 'messages/inbox',
			'priority' => 100,
			'link_class' => 'inbox-load'
		));

		$intypes = hypeInbox()->model->getIncomingMessageTypes($user);

		if ($intypes) {
			foreach ($intypes as $type) {
				$return[] = ElggMenuItem::factory(array(
					'name' => "inbox:$type",
					'parent_name' => 'inbox',
					'text' => elgg_echo("item:object:message:$type:plural"),
					'href' => "messages/inbox?message_type=$type",
					'link_class' => 'inbox-load'
				));
			}
		}

		$return[] = ElggMenuItem::factory(array(
			'name' => 'sentmessages',
			'text' => elgg_echo('inbox:sent'),
			'href' => 'messages/sent',
			'priority' => 500,
			'link_class' => 'inbox-load'
		));

		$outtypes = hypeInbox()->model->getOutgoingMessageTypes($user);

		if ($outtypes) {
			foreach ($outtypes as $type) {
				$return[] = ElggMenuItem::factory(array(
					'name' => "sent:$type",
					'parent_name' => 'sentmessages',
					'text' => elgg_echo("item:object:message:$type:plural"),
					'href' => "messages/sent?message_type=$type",
					'link_class' => 'inbox-load'
				));
			}
		}

		return $return;
	}

	/**
	 * Admin menu setup
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:page"
	 * @param array  $return An array of menu items
	 * @param array  $params Additional parameters
	 * @return array An array of menu items
	 */
	public static function setupAdminPageMenu($hook, $type, $return, $params) {

		if (!elgg_in_context('admin')) {
			return;
		}

		$return[] = ElggMenuItem::factory([
			'name' => 'message_types',
			'text' => elgg_echo('admin:inbox:message_types'),
			'href' => 'admin/inbox/message_types',
			'priority' => 500,
			'contexts' => array('admin'),
			'section' => 'configure'
		]);

		return $return;
	}

	/**
	 * Register user hover menu items
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:user_hover"
	 * @param array  $return An array of menu items
	 * @param array  $params Additional parameters
	 * @return array An array of menu items
	 */
	public static function setupUserHoverMenu($hook, $type, $return, $params) {

		$recipient = elgg_extract('entity', $params);
		$sender = elgg_get_logged_in_user_entity();

		if (!$sender || !$recipient) {
			return $return;
		}

		if ($sender->guid == $recipient->guid) {
			return $return;
		}

		$message_types = hypeInbox()->config->getMessageTypes();
		$user_types = hypeInbox()->config->getUserTypes();

		foreach ($message_types as $type => $options) {

			if ($type == Config::TYPE_NOTIFICATION) {
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
									'wheres' => array("EXISTS (SELECT * FROM {$dbprefix}entity_relationships
										WHERE guid_one = $sender->guid AND relationship = '$group_relationship' AND guid_two = r.guid_two)")
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
					'name' => "inbox:$type",
					'text' => elgg_echo("inbox:send", array(strtolower(elgg_echo("item:object:message:$type:singular")))),
					'href' => elgg_http_add_url_query_elements("messages/compose", array('message_type' => $type, 'send_to' => $recipient->guid)),
					'section' => 'action',
				));
			}
		}

		return $return;
	}

	/**
	 * Message entity menu setup
	 *
	 * @param string $hook "register"
	 * @param string $type "menu:entity"
	 * @param array $return An array of menu items
	 * @param array $params An array of additional parameters
	 * @return array An array of menu items
	 */
	public static function setupMessageMenu($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);

		if (!$entity instanceof Message || !$entity->canEdit()) {
			return $return;
		}

		$threaded = elgg_extract('threaded', $params, false);
		$action_params = array(
			'guids' => array($entity->guid),
			'threaded' => $threaded,
		);

		$return = array();

		$return[] = ElggMenuItem::factory(array(
			'name' => 'forward',
			'text' => elgg_view_icon('mail-forward'),
			'title' => elgg_echo('inbox:forward'),
			'href' => "messages/forward/$entity->guid/",
		));
		
		if (!$entity->isPersistent()) {
			$return[] = ElggMenuItem::factory(array(
				'name' => 'delete',
				'text' => elgg_view_icon('delete'),
				'title' => elgg_echo('inbox:delete'),
				'href' => elgg_http_add_url_query_elements('action/messages/delete', $action_params),
				'data-confirm' => ($threaded) ? elgg_echo('inbox:delete:thread:confirm') : elgg_echo('inbox:delete:message:confirm'),
				'is_action' => true,
				'priority' => 900,
			));
		}

		return $return;
	}

	/**
	 * Inbox controls setup
	 *
	 * @param string $hook "register"
	 * @param string $type "menu:inbox"
	 * @param array $return An array of menu items
	 * @param array $params An array of additional parameters
	 * @return array An array of menu items
	 */
	public static function setupInboxMenu($hook, $type, $return, $params) {

		$count = elgg_extract('count', $params);

		if ($count) {
			$chkbx = elgg_view('input/checkbox', array(
				'id' => 'inbox-form-toggle-all',
			)) . elgg_echo('inbox:form:toggle_all');

			$return[] = ElggMenuItem::factory(array(
				'name' => 'toggle',
				'text' => elgg_format_element('label', array(), $chkbx, array('encode_text' => false)),
				'href' => false,
				'priority' => 50,
				'link_class' => 'elgg-button elgg-button-action',
			));
			
			if (!elgg_in_context('sent-form')) {
				$return[] = ElggMenuItem::factory(array(
					'name' => 'markread',
					'text' => elgg_echo('inbox:markread'),
					'href' => 'action/messages/markread',
					'data-submit' => true,
					'priority' => 100,
					'link_class' => 'elgg-button elgg-button-action',
					'item_class' => 'inbox-action hidden',
				));
				$return[] = ElggMenuItem::factory(array(
					'name' => 'markunread',
					'text' => elgg_echo('inbox:markunread'),
					'href' => 'action/messages/markunread',
					'link_class' => 'elgg-button elgg-button-action',
					'data-submit' => true,
					'priority' => 200,
					'item_class' => 'inbox-action hidden',
				));
			}
			
			$return[] = ElggMenuItem::factory(array(
				'name' => 'delete',
				'text' => elgg_echo('inbox:delete'),
				'href' => 'action/messages/delete',
				'data-confirm' => elgg_echo('inbox:delete:inbox:confirm'),
				'data-submit' => true,
				'priority' => 300,
				'link_class' => 'elgg-button elgg-button-delete',
				'item_class' => 'inbox-action hidden',
			));
		}

		return $return;
	}

	/**
	 * Thread controls setup
	 *
	 * @param string $hook "register"
	 * @param string $type "menu:inbox:thread"
	 * @param array $return An array of menu items
	 * @param array $params An array of additional parameters
	 * @return array An array of menu items
	 */
	public static function setupInboxThreadMenu($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);

		if (!$entity instanceof Message || !$entity->canEdit()) {
			return $return;
		}

		$action_params = array(
			'guids' => array($entity->guid),
			'threaded' => true,
		);

		$return = array();

		$return[] = ElggMenuItem::factory(array(
			'name' => 'reply',
			'href' => '#reply',
			'text' => elgg_echo('inbox:reply'),
			'priority' => 100,
			'link_class' => 'elgg-button elgg-button-action',
		));

		$return[] = ElggMenuItem::factory(array(
			'name' => 'markread',
			'href' => elgg_http_add_url_query_elements('action/messages/markread', $action_params),
			'text' => elgg_echo('inbox:markread'),
			'is_action' => true,
			'priority' => 200,
			'link_class' => 'elgg-button elgg-button-action',
		));

		$return[] = ElggMenuItem::factory(array(
			'name' => 'markunread',
			'href' => elgg_http_add_url_query_elements('action/messages/markunread', $action_params),
			'text' => elgg_echo('inbox:markunread'),
			'is_action' => true,
			'priority' => 210,
			'link_class' => 'elgg-button elgg-button-action',
		));

		if (!$entity->isPersistent()) {
			$return[] = ElggMenuItem::factory(array(
				'name' => 'delete',
				'text' => elgg_echo('inbox:delete'),
				'href' => elgg_http_add_url_query_elements('action/messages/delete', $action_params),
				'data-confirm' => elgg_echo('inbox:delete:thread:confirm'),
				'is_action' => true,
				'priority' => 900,
				'link_class' => 'elgg-button elgg-button-delete',
			));
		}

		return $return;
	}

	/**
	 * Setup topbar menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:topbar"
	 * @param ElggMenuItem[] $return  Menu
	 * @param array          $params  Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupTopbarMenu($hook, $type, $return, $params) {

		if (!elgg_is_logged_in()) {
			return;
		}

		$count = hypeInbox()->model->countUnreadMessages();
		if ($count > 99) {
			$count = '99+';
		}

		if (elgg_is_active_plugin('hypeUI')) {

			$return[] = ElggMenuItem::factory([
				'name' => 'inbox',
				'href' => 'messages#inbox-popup',
				'text' => elgg_echo('inbox'),
				'badge' => $count ? : '',
				'priority' => 600,
				'tooltip' => elgg_echo('notifications:thread:unread', [$count]),
				'link_class' => 'has-hidden-label',
				'tooltip' => elgg_echo('inbox:thread:unread', [$count]),
				'icon' => 'envelope-o',
				'rel' => 'popup',
				'id' => 'inbox-popup-link',
				'data-position' => json_encode([
					'my' => 'center top',
					'of' => 'center bottom',
					'of' => '.elgg-menu-topbar > .elgg-menu-item-notifications',
					'collission' => 'fit fit',
				]),
			]);

		} else {
			$text = elgg_view_icon('envelope');
			$counter = elgg_format_element('span', [
				'id' => 'inbox-new',
				'class' => $count ? 'inbox-unread-count' : 'inbox-unread-count hidden',
			], $count);

			$return[] = ElggMenuItem::factory([
				'name' => 'inbox',
				'href' => 'messages#inbox-popup',
				'text' => $text . $counter,
				'priority' => 600,
				'tooltip' => elgg_echo('inbox:thread:unread', [$count]),
				'rel' => 'popup',
				'id' => 'inbox-popup-link',
				'data-position' => json_encode([
					'my' => 'center top',
					'of' => 'center bottom',
					'of' => '.elgg-menu-topbar > .elgg-menu-item-notifications',
					'collission' => 'fit fit',
				]),
			]);
		}

		return $return;
	}

}
