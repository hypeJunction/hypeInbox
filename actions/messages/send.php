<?php

use hypeJunction\Access\EntitySet;
use hypeJunction\Inbox\Message;

$original_msg_guid = get_input('original_guid');
$original_message = get_entity($original_msg_guid);

$sender_guid = elgg_get_logged_in_user_guid();
$recipient_guids = EntitySet::create(get_input('recipients', []))->guids();

$subject = htmlspecialchars(get_input('subject', ''), ENT_QUOTES, 'UTF-8');
$body = get_input('body');

if (empty($recipient_guids)) {
	register_error(elgg_echo('inbox:send:error:no_recipients'));
	forward(REFERRER);
}

if (empty(elgg_strip_tags($body))) {
	register_error(elgg_echo('inbox:send:error:no_body'));
	forward(REFERRER);
}

$enable_html = elgg_get_plugin_setting('enable_html', 'hypeInbox');
if (!$enable_html) {
	$body = elgg_strip_tags($body);
}

$message_hash = '';
$message_type = get_input('message_type', Message::TYPE_PRIVATE);
if ($original_message instanceof Message) {
	$message_hash = $original_message->getHash();
	$message_type = $original_message->getMessageType();
}

$message = Message::factory(array(
	'sender' => $sender_guid,
	'recipients' => $recipient_guids,
	'subject' => $subject,
	'body' => $body,
	'hash' => $message_hash,
	'message_type' => $message_type,
));

$guid = $message->send();

if (!$guid) {
	register_error(elgg_echo('inbox:send:error:generic'));
	forward(REFERRER);
}

$new_message = get_entity($guid);

$sender = $new_message->getSender();
$message_type = $new_message->getMessageType();
$message_hash = $new_message->getHash();

$ruleset = hypeInbox()->config->getRuleset($message_type);

$recipients = $new_message->getRecipients();

foreach ($recipients as $recipient) {
	if ($recipient->guid == $sender->guid) {
		continue;
	}

	$type_label = strtolower($ruleset->getSingularLabel($recipient->language));

	$subject = elgg_echo('inbox:notification:subject', array($type_label), $recipient->language);
	$notification = elgg_echo('inbox:notification:body', array(
		$type_label,
		$sender->name,
		$body,
		elgg_view('output/url', array(
			'href' => $new_message->getURL(),
		)),
		$sender->name,
		elgg_view('output/url', array(
			'href' => elgg_normalize_url("messages/thread/$message_hash#reply")
		)),
	), $recipient->language);
	
	notify_user($recipient->guid, $sender->guid, $subject, $notification, array(
		'attachments' => $attachments,
		'template' => 'messages_send',
		'action' => 'send',
		'object' => $new_message,
		'recipients' => $recipients,
	));
}

system_message(elgg_echo('inbox:send:success'));
forward($new_message->getURL());
