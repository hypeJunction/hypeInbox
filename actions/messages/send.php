<?php

use hypeJunction\Access\Collection;
use hypeJunction\Access\EntitySet;
use hypeJunction\Inbox\Message;

$guid = get_input('guid');
$entity = get_entity($guid);

$sender_guid = elgg_get_logged_in_user_guid();
$recipient_guids = EntitySet::create(get_input('recipients', []))->guids();

$subject = htmlspecialchars(get_input('subect', ''), ENT_QUOTES, 'UTF-8');
$body = get_input('body');

if (empty($recipient_guids)) {
	register_error(elgg_echo('inbox:send:error:no_recipients'));
	forward(REFERRER);
}

if (empty($body)) {
	register_error(elgg_echo('inbox:send:error:no_body'));
	forward(REFERRER);
}

if ($entity instanceof Message) {
	$message_hash = $entity->getHash();
	$message_type = $entity->getMessageType();
} else {
	if (!$message_type) {
		$message_type = Message::TYPE_PRIVATE;
	}
}

$access_id = Collection::create(array($sender_guid, $recipient_guids))->getCollectionId();

$guid = Message::factory(array(
	'sender' => $sender_guid,
	'recipients' => $recipient_guids,
	'subject' => $subject,
	'body' => $body,
	'message_hash' => $message_hash,
))->send();

$entity = ($guid) ? get_entity($guid) : false;

if (!$entity) {
	register_error(elgg_echo('inbox:send:error:generic'));
	forward(REFERRER);
}

$sender = $entity->getSender();
$message_type = $entity->getMessageType();
$message_hash = $entity->getHash();

$ruleset = hypeInbox()->config->getRuleset($message_type);

$attachment_urls = array_map(array(hypeInbox()->model, 'getLinkTag'), $entity->getAttachments(['limit' => 0]));

$body = array_filter(array(
	($ruleset->hasSubject()) ? $entity->subject : '',
	$entity->getBody(),
	implode(', ', array_filter($attachment_urls))
));

$notification_body = implode(PHP_EOL, $body);

$recipients = $entity->getRecipients();

foreach ($recipients as $recipient) {
	if ($recipient->guid == $sender->guid) {
		continue;
	}

	$type_label = strtolower($ruleset->getSingularLabel($recipient->language));

	$subject = elgg_echo('inbox:notification:subject', array($type_label), $recipient->language);
	$notification = elgg_echo('inbox:notification:body', array(
		$type_label,
		$sender->name,
		$notification_body,
		elgg_view('output/url', array(
			'href' => $entity->getURL(),
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
		'object' => $entity,
		'recipients' => $recipients,
	));
}

system_message(elgg_echo('inbox:send:success'));
forward($entity->getURL());
