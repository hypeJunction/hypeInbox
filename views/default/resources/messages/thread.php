<?php

use hypeJunction\Inbox\Message;

$hash = get_input('hash');

if (is_numeric($hash)) {
	$entity = get_entity($hash);
} else if (is_string($hash)) {
	$entities = elgg_get_entities_from_metadata([
		'types' => 'object',
		'subtypes' => 'messages',
		'owner_guid' => elgg_get_logged_in_user_guid(),
		'metadata_name_value_pairs' => [
			'name' => 'msgHash', 'value' => $hash
		],
		'order_by' => 'e.time_created DESC',
		'limit' => 1
	]);

	$entity = $entities[0];
}

if ($entity instanceof Message) {
	echo elgg_view('resources/messages/read', [
		'guid' => $entity->guid,
	]);
}