<?php

use hypeJunction\Inbox\Inbox;

elgg_ajax_gatekeeper();

$user = elgg_get_logged_in_user_entity();

$inbox = new Inbox();
$inbox->setOwner($user)
//->setDirection(Inbox::DIRECTION_ALL)
->displayThreaded(true);

$count = $inbox->getCount();
$messages = $inbox->getMessages();

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

$unread = Inbox::countUnread($user);

elgg_push_context('widgets');
$list = elgg_view_entity_list($messages, array(
	'list_class' => 'elgg-list-inbox',
	'no_results' => elgg_echo('inbox:empty'),
	'full_view' => false,
	'size' => 'tiny',
	'threaded' => false,
	'pagination' => false,
	'threaded' => true,
));

elgg_pop_context();

echo json_encode(array(
	'list' => $list,
	'unread' => $unread,
	'count' => $count,
));

