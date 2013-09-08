<?php

if (!elgg_is_xhr()) {
	forward(REFERER);
}

$limit = get_input('limit', 20);

$entity = elgg_extract('entity', $vars, false);
$hash = $entity->msgHash;
$notin = elgg_extract('notin', $vars, '0');

$options = array(
	'types' => 'object',
	'subtypes' => 'messages',
	'owner_guid' => $entity->owner_guid,
	'metadata_name_value_pairs' => array(
		'name' => 'msgHash', 'value' => $hash,
	),
	'order_by' => 'e.time_created DESC',
	'offset' => 0,
	'limit' => $limit,
	'wheres' => array(
		"e.time_created <= $entity->time_created AND e.guid != $entity->guid",
		"e.guid NOT IN ($notin)"
	)
);

elgg_push_context('inbox-thread');
$messages = elgg_get_entities_from_metadata($options);

if ($messages) {
	$messages = array_reverse($messages);

	echo elgg_view_entity_list($messages, array(
		'list_class' => 'inbox-messages-thread',
		'full_view' => array($entity->guid),
		'pagination' => false,
	));
}

elgg_pop_context();