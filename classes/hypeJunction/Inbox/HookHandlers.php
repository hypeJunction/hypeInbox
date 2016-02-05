<?php

namespace hypeJunction\Inbox;

use ElggMenuItem;
use hypeJunction\Data\Property;
use hypeJunction\Inbox\Config;
use hypeJunction\Inbox\Message;
use hypeJunction\Inbox\Models\Model;

/**
 * Plugin hooks service
 */
class HookHandlers {

	private $config;
	private $router;
	private $model;

	/**
	 * Constructor
	 * @param Config $config
	 * @param Router $router
	 */
	public function __construct(Config $config, Router $router, Model $model) {
		$this->config = $config;
		$this->router = $router;
		$this->model = $model;
	}

	/**
	 * Add third party user types/roles to the config array
	 *
	 * @param string $hook   "config:user_types"
	 * @param string $type   "framework:inbox"
	 * @param array  $return User types config array
	 * @param array  $params Hook params
	 * @return array
	 */
	public function filterUserTypes($hook, $type, $return, $params) {

		if (elgg_is_active_plugin('hypeApprove')) {
			$return['editor'] = array(
				'validator' => array($this->model, 'hasRole'),
				'getter' => array($this->model, 'getDirectRelationshipTestQuery'),
			);
			$return['supervisor'] = array(
				'validator' => array($this->model, 'hasRole'),
				'getter' => array($this->model, 'getDirectRelationshipTestQuery'),
			);
		}

		if (elgg_is_active_plugin('hypeObserver')) {
			$return['observer'] = array(
				'validator' => array($this->model, 'hasRole'),
				'getter' => array($this->model, 'getDirectRelationshipTestQuery'),
			);
		}

		if (elgg_is_active_plugin('roles')) {
			$roles = roles_get_all_selectable_roles();
			foreach ($roles as $role) {
				$return[$role->name] = array(
					'validator' => array($this->model, 'hasRole'),
					'getter' => array($this->model, 'getRoleTestQuery'),
				);
			}
		}

		return $return;
	}

	/**
	 * Messages page menu setup
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:page"
	 * @param array  $return An array of menu items
	 * @param array  $params Additional parameters
	 * @return array An array of menu items
	 */
	public function setupPageMenu($hook, $type, $return, $params) {

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

		$intypes = $this->model->getIncomingMessageTypes($user);

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

		$outtypes = $this->model->getOutgoingMessageTypes($user);

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
	 * Register user hover menu items
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:user_hover"
	 * @param array  $return An array of menu items
	 * @param array  $params Additional parameters
	 * @return array An array of menu items
	 */
	public function setupUserHoverMenu($hook, $type, $return, $params) {

		$recipient = elgg_extract('entity', $params);
		$sender = elgg_get_logged_in_user_entity();

		if (!$sender || !$recipient) {
			return $return;
		}

		if ($sender->guid == $recipient->guid) {
			return $return;
		}

		$message_types = $this->config->getMessageTypes();
		$user_types = $this->config->getUserTypes();

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
	public function setupMessageMenu($hook, $type, $return, $params) {

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

		$attachments = elgg_view('object/messages/elements/attachments-indicator', $params);
		if ($attachments) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'attachments',
						'text' => $attachments,
						'href' => false,
						'priority' => 50,
			));
		}

		if ($threaded) {
			$unread = elgg_view('object/messages/elements/unread-indicator', $params);
			if ($unread) {
				$return[] = ElggMenuItem::factory(array(
							'name' => 'unread',
							'text' => $unread,
							'href' => false,
							'priority' => 75,
				));
			}
			$return[] = ElggMenuItem::factory(array(
						'name' => 'count',
						'text' => elgg_view('object/messages/elements/count-indicator', $params),
						'href' => false,
						'priority' => 100,
			));
			$return[] = ElggMenuItem::factory(array(
						'name' => 'markread',
						'href' => elgg_http_add_url_query_elements('action/messages/markread', $action_params),
						'text' => elgg_view_icon('eye'),
						'title' => elgg_echo('inbox:markread'),
						'is_action' => true,
						'priority' => 100,
			));
			$return[] = ElggMenuItem::factory(array(
						'name' => 'markunread',
						'href' => elgg_http_add_url_query_elements('action/messages/markunread', $action_params),
						'text' => elgg_view_icon('eye-slash'),
						'title' => elgg_echo('inbox:markunread'),
						'is_action' => true,
						'priority' => 110,
			));
		}

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
	public function setupInboxMenu($hook, $type, $return, $params) {

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
	public function setupInboxThreadMenu($hook, $type, $return, $params) {

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
	public function setupTopbarMenu($hook, $type, $return, $params) {

		if (!elgg_is_logged_in()) {
			return;
		}

		$count = $this->model->countUnreadMessages();
		if ($count > 99) {
			$count = '99+';
		}

		$text = elgg_view_icon('envelope');
		$counter = elgg_format_element('span', [
			'id' => 'inbox-new',
			'class' => $count ? 'inbox-unread-count' : 'inbox-unread-count hidden',
				], $count);

		$return[] = ElggMenuItem::factory(array(
					'name' => 'inbox',
					'href' => '#inbox-popup',
					'text' => $text . $counter,
					'priority' => 600,
					'tooltip' => elgg_echo('inbox:thread:unread', array($count)),
					'rel' => 'popup',
					'id' => 'inbox-popup-link'
		));

		return $return;
	}

	/**
	 * Pretty URL for message objects
	 *
	 * @param string $hook   "entity:url"
	 * @param string $type   "object"
	 * @param string $return Icon URL
	 * @param array  $params Hook params
	 * @return string Filtered URL
	 */
	public function handleMessageURL($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);

		if (!$entity instanceof Message) {
			return $return;
		}

		return $this->router->getMessageURL($entity);
	}

	/**
	 * Replace message icon with a sender icon
	 *
	 * @param string $hook   "entity:icon:url"
	 * @param string $type   "object"
	 * @param string $return Icon URL
	 * @param array  $params Hook params
	 * @return string Filtered URL
	 */
	public function handleMessageIconURL($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);
		$size = elgg_extract('size', $params);

		if (!$entity instanceof Message) {
			return $return;
		}

		return $entity->getSender()->getIconURL($size);
	}

	public function getGraphAlias($hook, $type, $return, $params) {
		$return['object']['messages'] = ':message';
		return $return;
	}

	public function getMessageProperties($hook, $type, $return, $params) {

		$return[] = new Property('thread_id', array(
			'getter' => '\hypeJunction\Inbox\Message::getThreadIdProp',
			'read_only' => true,
		));

		$return[] = new Property('message_type', array(
			'getter' => '\hypeJunction\Inbox\Message::getMessageTypeProp',
			'read_only' => true,
		));

		$return[] = new Property('attachments', array(
			'getter' => '\hypeJunction\Inbox\Message::getAttachmentsProp',
			'read_only' => true,
		));

		return $return;
	}

	/**
	 * Add unread notifications count to the ajax responses
	 *
	 * @param string $hook   "output"
	 * @param string $type   "ajax"
	 * @param array  $return Ajax output
	 * @param array  $params Hook params
	 * @return array
	 */
	public function ajaxOutput($hook, $type, $return, $params) {
		$return['inbox']['unread'] = (int) $this->model->countUnreadMessages();
		return $return;
	}

	/**
	 * Register custom template
	 *
	 * @param string $hook   "get_templates"
	 * @param string $type   "notifications"
	 * @param string $return Template names
	 * @param array  $params Hook params
	 * @return array
	 */
	function addCustomTemplate($hook, $type, $return, $params) {

		$return[] = "messages_send";
		return $return;
	}

}
