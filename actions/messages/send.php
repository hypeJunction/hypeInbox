<?php

elgg_make_sticky_form('messages');

$subject = strip_tags(get_input('subject', elgg_echo('hj:inbox:untitled')));
$message = get_input('body', '');
$message_type = get_input('message_type', HYPEINBOX_PRIVATE);
$message_hash = get_input('message_hash', null);

$sender_guid = elgg_get_logged_in_user_guid();
$recipient_guids = get_input('recipient_guids');
if (!$recipient_guids) {
	register_error(elgg_echo("messages:user:blank"));
	forward("messages/compose");
} else if (!is_array($recipient_guids)) {
	$recipient_guids = array($recipient_guids);
}

$result = hj_inbox_send_message($sender_guid, $recipient_guids, $subject, $message, $message_type, array(
	'message_hash' => $message_hash,
	'attachments' => $_FILES['attachments']
		));

if (!$result) {
	register_error(elgg_echo("messages:error"));
	forward("messages/compose");
}

elgg_clear_sticky_form('messages');

system_message(elgg_echo("messages:posted"));

forward('messages/inbox/' . elgg_get_logged_in_user_entity()->username . '?message_type=' . $message_type);
