<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);

if ($full) {
	$body = $entity->getBody();
	if (elgg_view_exists('output/linkify')) {
		$body = elgg_view('output/linkify', array(
			'value' => $body,
		));
	}
	echo elgg_view('output/longtext', array(
		'value' => $body,
		'class' => 'inbox-message-body',
			));
} else {
//	$body = elgg_get_excerpt($entity->description);
//	echo elgg_format_element('span', array(
//		'class' => 'inbox-message-body-excerpt',
//			), $body);
}
