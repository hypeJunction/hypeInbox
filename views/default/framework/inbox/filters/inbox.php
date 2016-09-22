<?php

$message_type = elgg_extract('message_type', $vars, hypeJunction\Inbox\Message::TYPE_PRIVATE);
$filter_context = elgg_extract('filter_context', $vars, $message_type);

$user = elgg_get_page_owner_entity();

$tabs = array(
	'sent' => array(
		'text' => elgg_echo('inbox:sent'),
		'href' => "messages/sent/$user->username",
		'priority' => 900,
	),
	'search' => array(
		'text' => elgg_echo('inbox:search'),
		'href' => "messages/search/$user->username",
		'priority' => 950,
	),
);

$message_types = hypeInbox()->model->getIncomingMessageTypes($user);
if ($message_types) {
	foreach ($message_types as $type) {
		$text = elgg_echo("item:object:message:$type:plural");
		$count = hypeInbox()->model->countUnreadMessages($type, $user);
		if ($count) {
			$text .= ' <span class="inbox-unread-count">' . $count . '</span>';
		}

		$tabs[$type] = array(
			'text' => $text,
			'href' => "messages/inbox/$user->username?message_type=$type",
			'priority' => 500,
		);
	}
}

foreach ($tabs as $name => $tab) {
	$tab['name'] = $name;
	$tab['selected'] = ($filter_context == $name);
	elgg_register_menu_item('filter', $tab);
}

echo elgg_view_menu('filter', [
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
]);
