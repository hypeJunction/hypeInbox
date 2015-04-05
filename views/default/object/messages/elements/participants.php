<?php

$logged_in = elgg_get_logged_in_user_entity();
$entity = elgg_extract('entity', $vars);
$full = elgg_extract('full_view', $vars, false);

$sender = $entity->getSender();
if ($sender->guid == $logged_in->guid) {
	$participants['me'] = elgg_format_element('strong', array(
		'class' => 'inbox-message-sender',
			), elgg_echo('inbox:me'));
} else {
	$participants['sender'] = elgg_format_element('strong', array(
		'class' => 'inbox-message-sender',
			), $sender->name);
}

if (!$full) {
	$recipients = $entity->getRecipients();
	$count = count($recipients);

	foreach ($recipients as $key => $user) {
		if ($user->guid == elgg_get_logged_in_user_guid()) {
			$participants['me'] = elgg_echo('inbox:me');
			$count--;
		} else if ($cont <= 5) {
			$participants[] = $user->name;
		}
	}

	if ($count > 5) {
		$participants[] = elgg_echo('inbox:recipients:others', array($count));
	}
}

echo implode(', ', array_filter($participants));
