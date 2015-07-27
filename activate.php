<?php

require_once __DIR__ . '/autoloader.php';

$message_types = array(
	'__private' => array(
		'labels' => array(
			'singular' => 'Private Message',
			'plural' => 'Private Messages',
		),
		'multiple' => true,
		'attachments' => true,
		'persistent' => false,
		'allowed_senders' => array(
			'all'
		),
		'policy' => array(
			array(
				'sender' => 'all',
				'recipient' => 'all',
			)
		),
	),
	'__notification' => array(
		'labels' => array(
			'singular' => 'Site Message',
			'plural' => 'Site Messages',
		),
		'persistent' => false,
	),
);

if (is_null(elgg_get_plugin_setting('default_message_types', 'hypeInbox'))) {
	elgg_set_plugin_setting('default_message_types', serialize($message_types), 'hypeInbox');
}

$subtypes = array(
	hypeJunction\Inbox\Message::SUBTYPE => hypeJunction\Inbox\Message::CLASSNAME,
);

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}