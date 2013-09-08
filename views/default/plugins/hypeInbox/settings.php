<?php
		
$ha = access_get_show_hidden_status();
access_show_hidden_entities(true);

$name_id = get_metastring_id('msgHash');
if (!$name_id) {
	$name_id = add_metastring('msgHash');
}

$dbprefix = elgg_get_config('dbprefix');
$messages = elgg_get_entities(array(
	'types' => 'object',
	'subtypes' => array('messages'),
	'wheres' => array(
		"NOT EXISTS (SELECT 1 FROM {$dbprefix}metadata md WHERE md.entity_guid = e.guid
            AND md.name_id = $name_id)"
	),
	'order_by' => 'e.guid ASC',
	'count' => true
		));

access_show_hidden_entities($ha);

if ($messages) {
	echo elgg_view('framework/inbox/admin/import', array(
		'count' => $messages
	));
}