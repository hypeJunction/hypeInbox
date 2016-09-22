<?php

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$threaded = elgg_extract('threaded', $vars, !$full);

$sender = $entity->getSender();

if ($sender->guid == elgg_get_logged_in_user_guid()) {
	$recipients = $entity->getRecipients();
	$count = count($recipients);

	if ($count <= 5) {
		foreach ($recipients as $key => $user) {
			if (isset($participants[$user->guid]) || $user->guid == $sender->guid) {
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

	$to = implode(', ', array_filter($participants));
	echo elgg_echo('inbox:to', [$to]);
} else {
	$by = $sender->getDisplayName();
	if ($threaded) {
		echo elgg_echo('inbox:byline:thread', [$by]);
	} else {
		echo elgg_echo('inbox:byline', [$by]);
	}
}
