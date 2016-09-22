<?php

/**
 * Migrates notifications to private messages
 */
function inbox_upgrade_20162209() {

	$setting = elgg_get_plugin_setting('default_message_types', 'hypeInbox');
	if ($setting) {
		$setting = unserialize($setting);
		unset($setting['__notification']);
		elgg_set_plugin_setting('default_message_types', serialize($setting), 'hypeInbox');
	}

	$messages = new ElggBatch('elgg_get_entities_from_metadata', [
		'types' => 'object',
		'subtypes' => 'messages',
		'metadata_name_value_pairs' => [
			'name' => 'msgType',
			'value' => '__notification',
		],
		'limit' => 0,
	]);

	$messages->setIncrementOffset(false);

	foreach ($messages as $message) {
		$message->msgType = hypeJunction\Inbox\Message::TYPE_PRIVATE;
	}

}