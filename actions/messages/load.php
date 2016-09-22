<?php

use hypeJunction\Inbox\Inbox;

elgg_ajax_gatekeeper();

$user = elgg_get_logged_in_user_entity();

$inbox = new Inbox();
$inbox->setOwner($user)
		->setDirection(Inbox::DIRECTION_ALL)
		->displayThreaded(false);

$count = $inbox->getCount();
$messages = $inbox->getMessages();
$unread = Inbox::countUnread($user);

elgg_push_context('widgets');
$list = elgg_view_entity_list($messages, array(
	'list_class' => 'elgg-list-inbox',
	'no_results' => elgg_echo('inbox:empty'),
	'full_view' => false,
	'size' => 'tiny',
	'threaded' => false,
	'pagination' => false,
		));

elgg_pop_context();

echo json_encode(array(
	'list' => $list,
	'unread' => $unread,
	'count' => $count,
));

