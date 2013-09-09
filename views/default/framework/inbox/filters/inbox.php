<?php

$message_type = elgg_extract('message_type', $vars, 'all');

$user = elgg_get_page_owner_entity();

$i = 100;

$text = elgg_echo('hj:inbox:all');
$count = hj_inbox_count_unread_messages(null, $user);
if ($count) {
	$text .= ' <span class="inbox-unread-count">' . $count . '</span>';
}

$tabs = array(
//	'all' => array(
//		'text' => $text,
//		'href' => "messages/inbox/$user->username?message_type=all",
//		'priority' => $i++,
//		'class' => 'inbox-load'
//	)
);

$message_types = hj_inbox_get_incoming_message_types($user);
if ($message_types) {
	foreach ($message_types as $type) {
		$text = elgg_echo("item:object:message:$type:plural");
		$count = hj_inbox_count_unread_messages($type, $user);
		if ($count) {
			$text .= ' <span class="inbox-unread-count">' . $count . '</span>';
		}

		$tabs[$type] = array(
			'text' => $text,
			'href' => "messages/inbox/$user->username?message_type=$type",
			'priority' => $i++,
			'class' => 'inbox-load'
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
