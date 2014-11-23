<?php

namespace hypeJunction\Inbox;

use ElggUser;

$user = elgg_get_page_owner_entity();
if (!$user instanceof ElggUser) {
	return true;
}

$limit = get_input('limit', 20);
$offset = get_input('offset', 0);

$message_type = elgg_extract('message_type', $vars);
$read = elgg_extract('read', $vars);

$inbox = new Inbox();
$inbox->setOwner($user)
		->setMessageType($message_type)
		->setReadStatus($read)
		->setDirection(Inbox::DIRECTION_SENT);

$count = $inbox->getCount();
$messages = $inbox->getMessages(array(
	'limit' => $limit,
	'offset' => $offset,
		));

$params = array(
	'items' => $messages,
	'limit' => $limit,
	'offset' => $offset,
	'count' => $count,
	'threaded' => false,
);

elgg_push_context('inbox-form');
echo elgg_view('framework/inbox/controls/inbox', $params);
echo elgg_view('framework/inbox/list', $params);
echo elgg_view('input/submit', array(
	'class' => 'inbox-hidden',
));
elgg_pop_context();
