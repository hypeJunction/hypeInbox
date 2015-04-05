<?php

$message_type = elgg_extract('message_type', $vars, 'all');

$user = elgg_get_page_owner_entity();

$i = 100;

$tabs = array(
//	'all' => array(
//		'text' => elgg_echo('inbox:all'),
//		'href' => "messages/sent/$user->username",
//		'priority' => $i++,
//		'class' => 'inbox-load'
//	)
);

$message_types = hypeInbox()->model->getOutgoingMessageTypes($user);
if ($message_types) {
	foreach ($message_types as $type) {
		$text = elgg_echo("item:object:message:$type:plural");
		$tabs[$type] = array(
			'text' => $text,
			'href' => "messages/sent/$user->username?message_type=$type",
			'priority' => $i++,
			'link_class' => 'inbox-load'
		);
	}
}

foreach ($tabs as $name => $tab) {
	if ($tab) {
		$tab['name'] = $name;
		$tab['selected'] = ($message_type == $name);
		elgg_register_menu_item('filter', $tab);
	}
}

echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz'));
