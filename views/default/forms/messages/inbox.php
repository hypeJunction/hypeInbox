<?php

use ElggUser;
use hypeJunction\Inbox\Inbox;

$user = elgg_get_page_owner_entity();
if (!$user instanceof ElggUser) {
	return true;
}

$limit = get_input('limit', 20);
$offset = get_input('offset', 0);

$message_type = elgg_extract('message_type', $vars);
$read = elgg_extract('read', $vars);
$threaded = elgg_extract('threaded', $vars, true);

$inbox = new Inbox();
$inbox->setOwner($user)
		->setMessageType($message_type)
		->setReadStatus($read)
		//->setDirection(Inbox::DIRECTION_RECEIVED)
		->displayThreaded($threaded);


$count = $inbox->getCount();
$messages = $inbox->getMessages(array(
	'limit' => $limit,
	'offset' => $offset,
		));

if ($threaded && $messages) {
	$latest_messages = array();
// Fix for 'GROUP_BY' statememtn returning wrong order
	foreach ($messages as $msg) {
		$lastMsg = $msg->getVolatileData('select:lastMsg');
		if ($lastMsg && $lastMsg != $msg->guid) {
			$latest_messages[] = get_entity($lastMsg);
		} else {
			$latest_messages[] = $msg;
		}
	}
	$messages = $latest_messages;
}

$params = array(
	'items' => $messages,
	'limit' => $limit,
	'offset' => $offset,
	'count' => $count,
	'threaded' => $threaded,
);

elgg_push_context('inbox-form');
echo elgg_view('framework/inbox/controls/inbox', $params);
echo elgg_view('framework/inbox/list', $params);
echo elgg_view('input/submit', array(
	'class' => 'inbox-hidden',
));
elgg_pop_context();
