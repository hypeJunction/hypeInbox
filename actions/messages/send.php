<?php

namespace hypeJunction\Inbox;

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

$attachments = Group::create(get_input('attachments'))->entities();
/* @var $attachments ElggEntity[] */

$access_id = AccessCollection::create(array($sender_guid, $recipient_guids))->getCollectionId();
foreach ($attachments as $attachment) {
	$attachment->origin = 'messages';
	$attachment->access_id = $access_id;
	$attachment->save();
}

$message = send_message(array(
	'sender' => $sender_guid,
	'recipients' => $recipient_guids,
	'subject' => $subject,
	'body' => $body,
	'message_hash' => $message_hash,
	'attachments' => $attachments,
		));

if (!$message) {
	// delete attachment if  message failed to send
	foreach ($attachments as $attachment) {
		$attachment->delete();
	}

	register_error(elgg_echo('inbox:send:error:generic'));
	forward(REFERER);
}

elgg_clear_sticky_form('messages');

system_message(elgg_echo('inbox:send:success'));

forward($message->getURL());
