<?php

namespace hypeJunction\Inbox;

$entity = elgg_extract('entity', $vars);

$count = $entity->thread()->getUnreadCount();

if ($count) {
	echo elgg_format_element('span', array(
		'class' => 'inbox-message-unread-indicator',
		'title' => elgg_echo('inbox:thread:unread', array($count))
	));
} else {
	echo elgg_format_element('span', array(
		'class' => 'inbox-message-unread-indicator',
		'title' => elgg_echo('inbox:thread:new')
	));
}