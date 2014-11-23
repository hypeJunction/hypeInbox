<?php

namespace hypeJunction\Inbox;

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);

if ($full) {
	$body = elgg_trigger_plugin_hook('link:qualifiers', 'messages', array('source' => $entity->getBody()), $body);
	echo elgg_view('output/longtext', array(
		'value' => $body,
	));
} else {
	echo elgg_get_excerpt($entity->description, 100);
}