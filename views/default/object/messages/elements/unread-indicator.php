<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);
$threaded = elgg_extract('threaded', $vars, !$full);

$count = 0;
if ($threaded) {
	$count = $entity->thread()->getUnreadCount();
}

echo elgg_format_element('span', array(
	'class' => 'inbox-message-unread-indicator',
	'title' => elgg_echo('inbox:thread:unread', array($count))
		), $count);
