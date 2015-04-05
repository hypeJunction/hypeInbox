<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);
$threaded = elgg_extract('threaded', $vars, !$full);

if ($threaded) {
	$count = $entity->thread()->getCount();
}

if ($count) {
	echo elgg_format_element('span', array(
		'class' => 'inbox-message-count-indicator',
		'title' => elgg_echo('inbox:thread:count', array($count))
			), $count);
}