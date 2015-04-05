<?php

use ElggEntity;
use hypeJunction\Filestore\UploadHandler;
use hypeJunction\Inbox\AccessCollection;
use hypeJunction\Inbox\Config;
use hypeJunction\Inbox\Group;
use hypeJunction\Inbox\Message;

elgg_make_sticky_form('messages');

$guid = get_input('guid');
$entity = get_entity($guid);

$sender_guid = elgg_get_logged_in_user_guid();

$recipient_guids = Group::create(get_input('recipient_guids'))->guids();
if (empty($recipient_guids)) {
	register_error(elgg_echo('inbox:send:error:no_recipients'));
	forward(REFERER);
}

$subject = strip_tags(get_input('subject', ''));
$body = get_input('body', '');
if (empty($body)) {
	register_error(elgg_echo('inbox:send:error:no_body'));
	forward(REFERER);
}

if ($entity instanceof Message) {
	$message_hash = $entity->getHash();
	$message_type = $entity->getMessageType();
} else {
	$message_type = get_input('message_type', Message::TYPE_PRIVATE);
}

$attachment_guids = get_input('attachments', array());

if (class_exists('hypeJunction\\Filestore\\UploadHandler')) {
	// files being uploaded via $_FILES
	$uploads = UploadHandler::handle('attachments');
	if ($uploads) {
		foreach ($uploads as $upload) {
			if ($upload->guid) {
				$attachment_guids[] = $upload->guid;
			}
		}
	}
}

$attachments = Group::create($attachment_guids)->entities();
/* @var $attachments ElggEntity[] */

$access_id = AccessCollection::create(array($sender_guid, $recipient_guids))->getCollectionId();
foreach ($attachments as $attachment) {
	$attachment->origin = 'messages';
	$attachment->access_id = $access_id;
	$attachment->save();
}

$message = hypeInbox()->actions->sendMessage(array(
	'sender' => $sender_guid,
	'recipients' => $recipient_guids,
	'subject' => $subject,
	'body' => $body,
	'message_hash' => $message_hash,
	'attachments' => $attachments,
		));

if (!$message) {
	// delete attachment if message failed to send
	foreach ($attachments as $attachment) {
		$attachment->delete();
	}

	register_error(elgg_echo('inbox:send:error:generic'));
	forward(REFERER);
}


$sender = $message->getSender();
$message_type = $message->getMessageType();
$message_hash = $message->getHash();

$config = new Config;
$ruleset = $config->getRuleset($message_type);
$type_label = $ruleset->getSingularLabel($language);

$attachments = $message->getAttachments(array('limit' => 0));
if ($attachments && count($attachments)) {
	$attachments = array_map(array(hypeInbox()->model, 'getLinkTag'), $attachments);
}

$body = array_filter(array(
	($ruleset->hasSubject()) ? $message->subject : '',
	$message->getBody(),
	implode(', ', array_filter($attachments))
		));

$notification_body = implode(PHP_EOL, $body);

foreach ($recipient_guids as $recipient_guid) {
	$recipient = get_entity($recipient_guid);

	$subject = elgg_echo('inbox:notification:subject', array($type_label), $recipient->language);
	$notification = elgg_echo('inbox:notification:body', array(
		$type_label,
		$sender->name,
		$notification_body,
		elgg_view('output/url', array(
			'href' => $message->getURL(),
		)),
		$sender->name,
		elgg_view('output/url', array(
			'href' => elgg_normalize_url("messages/thread/$message_hash#reply")
		)),
			), $recipient->language);

	$summary = elgg_echo('inbox:notification:summary', array($type_label), $recipient->language);

	notify_user($recipient->guid, $sender->guid, $subject, $notification, array(
		'action' => 'send',
		'object' => $message,
		'summary' => $summary,
	));
}

elgg_clear_sticky_form('messages');

system_message(elgg_echo('inbox:send:success'));

forward($message->getURL());
