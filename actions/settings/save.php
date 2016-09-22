<?php

function prepareMessageTypes() {

	$config = array();
	$message_types = get_input('message_types');
	if (!is_array($message_types)) {
		return;
	}
	
	foreach ($message_types as $name => $options) {

		if (empty($options['name'])) {
			continue;
		}

		if ($name == '__new') {
			$name = strtolower(str_replace(' ', '_', $options['name']));
		}

		$config[$name] = array(
			'labels' => $options['labels'],
			'attachments' => elgg_extract('attachments', $options, false),
			'persistent' => elgg_extract('persistent', $options, false),
			'multiple' => elgg_extract('multiple', $options, false),
			'no_subject' => elgg_extract('no_subject', $options, false),
		);

		if (isset($options['policy'])) {
			for ($i = 0; $i < count($options['policy']['sender']); $i++) {
				$config[$name]['policy'][$i] = array(
					'sender' => $options['policy']['sender'][$i],
					'recipient' => $options['policy']['recipient'][$i],
					'relationship' => $options['policy']['relationship'][$i],
					'inverse_relationship' => $options['policy']['inverse_relationship'][$i],
					'group_relationship' => $options['policy']['group_relationship'][$i],
				);
			}
		}
	}

	return $config;
}

$plugin = elgg_get_plugin_from_id('hypeInbox');
$params = (array) get_input('params', array());

$message_types = prepareMessageTypes();
if (isset($message_types)) {
	$params['message_types'] = $message_types;
}

if (!$plugin instanceof ElggPlugin) {
	register_error(elgg_echo('plugins:settings:save:fail', array('hypeInbox')));
	return false;
}

$plugin_name = $plugin->getManifest()->getName();

foreach ($params as $k => $v) {
	if (is_array($v)) {
		$v = serialize($v);
	}
	$result = $plugin->setSetting($k, $v);
	if (!$result) {
		register_error(elgg_echo('plugins:settings:save:fail', array($plugin_name)));
	}
}

if ($result) {
	system_message(elgg_echo('plugins:settings:save:ok', array($plugin_name)));
}
