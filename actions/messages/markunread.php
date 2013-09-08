<?php

$hashes = get_input('hashes');
$guids = get_input('guids');
$owner_guid = get_input('owner_guid', elgg_get_logged_in_user_guid());

$count = $success = $notfound = $unread = 0;

if ($hashes) {
	foreach ($hashes as $hash) {
		$options = array(
			'types' => 'object',
			'subtypes' => 'messages',
			'owner_guid' => $owner_guid,
			'metadata_name_value_pairs' => array(
				'name' => 'msgHash', 'value' => $hash,
			),
			'limit' => 0,
		);

		$batch = new ElggBatch('elgg_get_entities_from_metadata', $options);
		foreach ($batch as $message) {
			$count++;
			if ($message->readYet) {
				$message->readYet = false;
				$success++;
			} else {
				$unread++;
			}
		}
	}
}

if ($guids) {
	foreach ($guids as $guid) {
		$count++;
		$message = get_entity($guid);
		if (!elgg_instanceof($message, 'object', 'messages')) {
			$notfound++;
			continue;
		}
		if ($message->readYet) {
			$message->readYet = false;
			$success++;
		} else {
			$unread++;
		}
	}
}

if (elgg_is_xhr()) {
	print json_encode(array(
		'unread' => $success + $unread,
		'count' => $count
	));
}

$msg[] = elgg_echo('hj:inbox:markunread:success', array($success, $count));
if ($notfound > 0)
	$msg[] = elgg_echo('hj:approve:error:notfound', array($notfound));

system_message(implode('<br />', $msg));