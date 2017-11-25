<?php

$message_type = elgg_extract('message_type', $vars, hypeJunction\Inbox\Message::TYPE_PRIVATE);
$filter_context = elgg_extract('filter_context', $vars, $message_type);

$user = elgg_get_page_owner_entity();

$tabs = [
	'search' => [
		'text' => elgg_echo('inbox:search'),
		'href' => "messages/search/$user->username",
		'priority' => 950,
	],
];

$message_types = hypeInbox()->model->getIncomingMessageTypes($user);
if ($message_types) {
	foreach ($message_types as $type) {
		$text = elgg_echo("item:object:message:$type:plural");
		$count = hypeInbox()->model->countUnreadMessages($type, $user);
		if ($count) {
			$text .= elgg_format_element('span', ['class' => 'inbox-unread-count mls'], $count);
		}

		$tabs[$type] = [
			'text' => $text,
			'href' => "messages/inbox/$user->username?message_type=$type",
			'priority' => 500,
		];
	}
}

$outtypes = hypeInbox()->model->getOutgoingMessageTypes($user);
if ($outtypes) {
	foreach ($outtypes as $type) {
		$out = elgg_echo("item:object:message:$type:plural");
		$text = elgg_echo('inbox:message_type:sent', array($out));

		$tabs["sent-$type"] = [
			'text' => $text,
			'href' => "messages/sent/$user->username?message_type=$type",
			'priority' => 900,
		];
	}
}

foreach ($tabs as $name => $tab) {
	$tab['name'] = $name;
	$tab['selected'] = ($filter_context == $name);
	$tabs[$name] = $tab;
}

echo elgg_view_menu('filter', [
	'items' => $tabs,
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
]);
