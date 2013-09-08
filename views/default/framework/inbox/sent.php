<?php

$user = elgg_get_page_owner_entity();
$message_type = elgg_extract('message_type', $vars, 'all');

elgg_push_context('inbox-sent');
echo elgg_list_entities_from_metadata(array(
	'list_class' => 'inbox-messages-table',
	'type' => 'object',
	'subtype' => 'messages',
	'limit' => get_input('limit', 20),
	'pagination' => true,
	'metadata_name_value_pairs' => array(
		0 => array('name' => 'fromId', 'value' => $user->guid, 'operand' => '='),
		1 => ($message_type !== 'all') ? array('name' => 'msgType', 'value' => $message_type, 'operand' => '=') : null
	),
	'owner_guid' => elgg_get_page_owner_guid(),
	'full_view' => false,
));
elgg_pop_context();