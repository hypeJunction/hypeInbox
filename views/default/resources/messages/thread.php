<?php

namespace hypeJunction\Inbox;

$segments = elgg_extract('segments', $vars, array());
$hash = $segments[1];

if (is_numeric($hash)) {
	$entity = get_entity($hash);
} else if (is_string($hash)) {
	$entities = elgg_get_entities_from_metadata(array(
		'types' => 'object',
		'subtypes' => 'messages',
		'owner_guid' => elgg_get_logged_in_user_guid(),
		'metadata_name_value_pairs' => array(
			'name' => 'msgHash', 'value' => $hash
		),
		'order_by' => 'e.time_created DESC',
		'limit' => 1
	));

	$entity = $entities[0];
}

if ($entity instanceof Message) {
	$segments[1] = $entity->guid;
	echo elgg_view('resources/messages/read', array(
		'segments' => $segments,
	));
}