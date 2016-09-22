<?php

$logged_in = elgg_get_logged_in_user_entity();
$entity = elgg_extract('entity', $vars);
$full = elgg_extract('full_view', $vars, false);
$threaded = elgg_extract('threaded', $vars, !$full);

if ($full) {
	return;
}

$sender = $entity->getSender();
if ($sender->guid == $logged_in->guid) {
	$participants[$sender->guid] = elgg_format_element('span', array(
		'class' => 'inbox-message-sender',
	), elgg_echo('inbox:me'));
} else {
	$participants[$sender->guid] = elgg_format_element('span', array(
		'class' => 'inbox-message-sender',
	), $sender->name);
}

$recipients = $entity->getRecipients();
$count = count($recipients);

if ($count <= 5) {
	foreach ($recipients as $key => $user) {
		if (isset($participants[$user->guid])) {
			continue;
		}
		$participants[$user->guid] = elgg_format_element('span', [
			'class' => 'inbox-message-participant',
		], ($logged_in->guid == $user->guid) ? elgg_echo('inbox:me') : $user->name);
	}
}

if ($count > 5) {
	$participants[] = elgg_echo('inbox:recipients:others', array($count));
}

echo implode(', ', array_filter($participants));
