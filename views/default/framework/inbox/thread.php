<?php

$limit = get_input('limit', 20);


$user = elgg_get_page_owner_entity();
$entity = elgg_extract('entity', $vars, false);
$hash = elgg_extract('message_hash', $vars, false);

if (!$entity && !$hash) {
	return;
}
if (!$hash) {
	$hash = $entity->msgHash;
}

if (!$entity) {
	$entities = elgg_get_entities_from_metadata(array(
		'types' => 'object',
		'subtypes' => 'messages',
		'owner_guid' => $user->guid,
		'metadata_name_value_pairs' => array(
			'name' => 'msgHash', 'value' => $hash
		),
		'order_by' => 'e.time_created DESC',
		'limit' => 1
	));

	$entity = $entities[0];
}

$options = array(
	'types' => 'object',
	'subtypes' => 'messages',
	'owner_guid' => $user->guid,
	'metadata_name_value_pairs' => array(
		'name' => 'msgHash', 'value' => $hash,
	),
	'order_by' => 'e.time_created DESC',
	'offset' => 0,
	'limit' => $limit,
	'count' => true
);

// Earlier messages
$options['wheres'] = array("e.time_created <= $entity->time_created AND e.guid != $entity->guid");
$count_before = elgg_get_entities_from_metadata($options);
$options['count'] = false;
$before = elgg_get_entities_from_metadata($options);
if (!$before) {
	$before = array();
} else {
	$before = array_reverse($before);
}

// Earlier messages
$options['count'] = true;
$options['wheres'] = array("e.time_created > $entity->time_created");
$count_after = elgg_get_entities_from_metadata($options);
$options['count'] = false;
$options['order_by'] = 'e.time_created ASC';
$after = elgg_get_entities_from_metadata($options);
if (!$after) {
	$after = array();
}

if ($count_before > $limit) {
	$before_link = elgg_view('output/url', array(
		'text' => elgg_echo('hj:inbox:load:before', array($limit)),
		'href' => "ajax/view/framework/inbox/thread/before?guid=$entity->guid&limit=$limit",
		'class' => 'inbox-thread-load-before'
	));
}

if ($count_after > $limit) {
	$after_link = elgg_view('output/url', array(
		'text' => elgg_echo('hj:inbox:load:after', array($limit)),
		'href' => "ajax/view/framework/inbox/thread/after?guid=$entity->guid&limit=$limit",
		'class' => 'inbox-thread-load-after'
	));
}

$messages = array_merge($before, array($entity), $after);

if ($entity->msgType != HYPEINBOX_NOTIFICATION) {
	$from = $entity->fromId;
	$to = $entity->toId;
	if (!is_array($from)) {
		$from = array($from);
	}
	if (!is_array($to)) {
		$to = array($to);
	}

	$user_guids = array_merge($from, $to);

	foreach ($user_guids as $guid) {
		$user = get_entity($guid);
		if (!elgg_instanceof($user))
			continue;
		$users[$user->guid] = $user;
	}
}

elgg_push_context('inbox-thread');

if ($users) {
	echo elgg_view_entity_list($users, array(
		'list_type' => 'gallery',
		'gallery_class' => 'elgg-gallery-users',
		'size' => 'small'
	));
}

echo $before_link;
echo elgg_view_entity_list($messages, array(
	'list_class' => 'inbox-messages-thread',
	'full_view' => array($entity->guid),
	'pagination' => false,
));
echo $after_link;


// Reply form
elgg_push_context('inbox-reply');
$form = elgg_view('framework/inbox/compose', array(
	'message_type' => $entity->msgType,
	'entity' => $entity
		));
if ($form) {
	echo elgg_view_module('messages-reply', elgg_echo('messages:answer'), $form);
}
elgg_pop_context();


elgg_pop_context();

