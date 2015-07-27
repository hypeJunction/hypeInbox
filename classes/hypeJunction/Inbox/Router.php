<?php

namespace hypeJunction\Inbox;

use ElggEntity;
use hypeJunction\Inbox\Config;
use hypeJunction\Inbox\Message;

/**
 * Routing and page handling service
 */
class Router {

	protected $config;

	/**
	 * Constructor
	 * @param Config $config
	 */
	public function __construct(Config $config) {
		$this->config = $config;
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
	 */
	function handlePages($segments) {

		gatekeeper();

		$page_owner = $this->getPageOwner($segments);
		if (!$page_owner || !$page_owner->canEdit()) {
			forward('', '403');
		}

		elgg_set_page_owner_guid($page_owner->guid);

		switch ($segments[0]) {

			default :
			case 'inbox' :
			case 'incoming' :
				$page = elgg_view('resources/messages/inbox');
				break;

			case 'outbox' :
			case 'outgoing' :
			case 'sent' :
				$page = elgg_view('resources/messages/sent');
				break;

			case 'read' :
			case 'view' :
			case 'reply' :
				set_input('guid', $segments[1]);
				$page = elgg_view('resources/messages/read');
				break;

			case 'thread' :
				set_input('hash', $segments[1]);
				$page = elgg_view('resources/messages/thread');
				break;

			case 'compose' :
			case 'add' :
				set_input('guid', $segments[1]);
				$page = elgg_view('resources/messages/compose');
				break;
		}

		if (!$page) {
			return false;
		}

		echo $page;
		return true;
	}

	/**
	 * Returns page handler ID
	 * @return string
	 */
	public function getPageHandlerId() {
		return hypeInbox()->config->get('pagehandler_id', 'messages');
	}

	/**
	 * Returns normalized message URL
	 * 
	 * @param Message $entity Message
	 * @return string
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
