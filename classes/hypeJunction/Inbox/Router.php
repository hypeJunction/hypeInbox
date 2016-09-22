<?php

namespace hypeJunction\Inbox;

use ElggEntity;
use hypeJunction\Inbox\Message;

class Router {

	/**
	 * Messages page handler
	 *   /messages/inbox/<username>?message_type=<message_type>
	 *   /messages/sent/<username>?message_type=<message_type>
	 *   /messages/read/<guid>
	 *   /messages/thread/<hash>
	 *   /messages/compose?send_to=<guid>
	 *
	 * @param array $segments An array of URL segments
	 * @return boolean Outputs a page or returns false on failure
	 */
	public static function handleMessages($segments) {

		$page = array_shift($segments);

		switch ($page) {

			default :
			case 'inbox' :
			case 'incoming' :
				echo elgg_view_resource('messages/inbox');
				return true;

			case 'outbox' :
			case 'outgoing' :
			case 'sent' :
				echo elgg_view_resource('messages/sent');
				return true;

			case 'read' :
			case 'view' :
			case 'reply' :
				$guid = array_shift($segments);
				set_input('guid', $guid);
				echo elgg_view_resource('messages/read', [
					'guid' => $guid,
				]);
				return true;

			case 'thread' :
				$hash = array_shift($segments);
				set_input('hash', $hash);
				echo elgg_view_resource('messages/thread', [
					'hash' => $hash,
				]);
				return true;

			case 'compose' :
			case 'add' :
				$guid = array_shift($segments);
				set_input('guid', $guid);
				echo elgg_view_resource('messages/compose', [
					'guid' => $guid,
				]);
				return true;
		}

		return false;
	}

	/**
	 * Helper handler to correctly resolve page owners
	 *
	 * @see default_page_owner_handler()
	 *
	 * @param string $hook   "page_owner"
	 * @param string $type   "system"
	 * @param int    $return Page owner guid
	 * @param array  $params Hook params
	 * @return int|void
	 */
	public static function resolvePageOwner($hook, $type, $return, $params) {

		if ($return) {
			return;
		}

		$segments = _elgg_services()->request->getUrlSegments();
		$identifier = array_shift($segments);

		if ($identifier !== 'messages') {
			return;
		}

		$page = array_shift($segments) ? : 'inbox';

		switch ($page) {

			case 'read' :
			case 'view' :
			case 'reply' :
			case 'compose' :
			case 'add' :
				$guid = array_shift($segments);
				if (!$guid) {
					return;
				}
				$entity = get_entity($guid);
				if (!$entity) {
					return;
				}
				$container = $entity->getContainerEntity();
				if (!$container) {
					return;
				}
				return $container->guid;

			case 'inbox' :
			case 'incoming' :
			case 'outbox' :
			case 'outgoing' :
			case 'sent' :
			case 'received' :
				$username = array_shift($segments);
				if ($username) {
					$user = get_user_by_username($username);
				} else {
					$user = elgg_get_logged_in_user_entity();
				}
				if (!$user) {
					return;
				}
				return $user->guid;
		}
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
	public static function messageUrlHandler($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);

		if (!$entity instanceof Message) {
			return $return;
		}

		return elgg_normalize_url("messages/read/$entity->guid#elgg-object-$entity->guid");
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
	public static function messageIconUrlHandler($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);
		$size = elgg_extract('size', $params);

		if (!$entity instanceof Message) {
			return $return;
		}

		$sender = $entity->getSender();
		if ($sender) {
			return $sender->getIconURL($size);
		}
	}

	/**
	 * Messages page handler
	 *   /messages/inbox/<username>?message_type=<message_type>
	 *   /messages/sent/<username>?message_type=<message_type>
	 *   /messages/read/<guid>
	 *   /messages/thread/<hash>
	 *   /messages/compose?send_to=<guid>
	 *
	 * @param array $segments An array of URL segments
	 * @return boolean Outputs a page or returns false on failure
	 * @deprecated 6.0
	 */
	public function handlePages($segments) {
		return Router::handleMessages($segments);
	}

	/**
	 * Returns page handler ID
	 * @return string
	 * @deprecated 6.0
	 */
	public function getPageHandlerId() {
		return hypeInbox()->config->get('pagehandler_id', 'messages');
	}

	/**
	 * Returns normalized message URL
	 * 
	 * @param Message $entity Message
	 * @return string
	 * @deprecated 6.0
	 */
	public function getMessageURL(Message $entity) {
		$friendly = elgg_get_friendly_title($entity->getDisplayName());
		return $this->normalize(array('read', $entity->guid, $friendly . "#elgg-object-{$entity->guid}"));
	}

	/**
	 * Prefixes the URL with the page handler ID and normalizes it
	 *
	 * @param mixed $url   URL as string or array of segments
	 * @param array $query Query params to add to the URL
	 * @return string
	 * @deprecated 6.0
	 */
	public function normalize($url = '', $query = array()) {

		if (is_array($url)) {
			$url = implode('/', $url);
		}

		$url = implode('/', array($this->getPageHandlerId(), $url));

		if (!empty($query)) {
			$url = elgg_http_add_url_query_elements($url, $query);
		}

		return elgg_normalize_url($url);
	}

	/**
	 * Get page owner from URL segments
	 * Defaults to logged in user
	 *
	 * @param array $segments URL segments
	 * @return ElggEntity
	 * @deprecated 6.0
	 */
	public function getPageOwner($segments = array()) {

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

}
