<?php

namespace hypeJunction\Inbox;

use ElggBatch;

/**
 * Messages page handler
 *   /messages/inbox/<username>?message_type=<message_type>
 *   /messages/sent/<username>?message_type=<message_type>
 *   /messages/read/<guid>
 *   /messages/thread/<guid>
 *   /messages/thread/<guid>/before
 *   /messages/thread/<guid>/after
 *   /messages/compose?send_to=<guid>
 *
 * @param array $segments An array of URL segments
 * @return boolean Outputs a page or returns false on failure
 */
function page_handler($segments) {

	gatekeeper();

	$page_owner = get_page_owner($segments);
	if (!$page_owner->canEdit()) {
		forward('', '403');
	}

	elgg_set_page_owner_guid($page_owner->guid);

	switch ($segments[0]) {

		default :
		case 'inbox' :
		case 'incoming' :
			$page = elgg_view('resources/messages/inbox', array(
				'segments' => $segments,
			));
			break;

		case 'outbox' :
		case 'outgoing' :
		case 'sent' :
			$page = elgg_view('resources/messages/sent', array(
				'segments' => $segments,
			));
			break;

		case 'read' :
		case 'view' :
		case 'reply' :
			$page = elgg_view('resources/messages/read', array(
				'segments' => $segments,
			));
			break;

		case 'thread' :
			$page = elgg_view('resources/messages/thread', array(
				'segments' => $segments,
			));
			break;

		case 'compose' :
		case 'add' :

			$page = elgg_view('resources/messages/compose', array(
				'segments' => $segments,
			));
			break;
	}

	if (!$page) {
		return false;
	}

	echo $page;
	return true;
}
