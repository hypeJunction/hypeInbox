<?php

namespace hypeJunction\Inbox;

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
