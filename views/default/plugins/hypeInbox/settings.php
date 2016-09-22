<?php

$entity = elgg_extract('entity', $params);

$ha = access_get_show_hidden_status();
access_show_hidden_entities(true);

$messages = hypeInbox()->model->getUnhashedMessages(array('count' => true));

access_show_hidden_entities($ha);

if ($messages) {
	echo elgg_view('framework/inbox/admin/import', array(
		'count' => $messages
	));
}

echo elgg_view_input('select', [
	'name' => 'params[enable_html]',
	'value' => $entity->enable_html,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
	'label' => elgg_echo('inbox:settings:enable_html'),
	'help' => elgg_echo('inbox:settings:enable_html:help'),
]);

