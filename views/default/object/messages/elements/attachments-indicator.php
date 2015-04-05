<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);
$threaded = elgg_extract('threaded', $vars, !$full);
$count = $entity->hasAttachments(array(), $threaded);

if (!$count) {
	return true;
}

echo elgg_format_element('span', array(
	'class' => 'inbox-icon-attachment',
	'title' => elgg_echo('inbox:attachments:count', array($count))
));
