<?php

namespace hypeJunction\Inbox;

require_once __DIR__ . '/vendors/autoload.php';

$plugin_id = basename(__DIR__);

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

if (is_null(elgg_get_plugin_setting('default_message_types', $plugin_id))) {
	elgg_set_plugin_setting('default_message_types', serialize($message_types), $plugin_id);
}

$subtypes = array(
	Message::SUBTYPE => get_class(new Message),
);

foreach ($subtypes as $subtype => $class) {
	if (get_subtype_id('object', $subtype)) {
		update_subtype('object', $subtype, $class);
	} else {
		add_subtype('object', $subtype, $class);
	}
}