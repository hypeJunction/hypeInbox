<?php

/**
 * Compose message form
 */
elgg_load_js('inbox.user.js');

$message_type = elgg_extract('message_type', $vars, HYPEINBOX_PRIVATE);
$entity = elgg_extract('entity', $vars, false);

$recipient_guids = elgg_extract('recipient_guids', $vars, false);
$subject = elgg_extract('subject', $vars, '');
$message = elgg_extract('body', $vars, '');

$message_types = elgg_get_config('inbox_message_types');
$rules = elgg_extract($message_type, $message_types);

if (!is_array($recipient_guids) && elgg_instanceof($entity, 'object', 'messages')) {

	$from = $entity->fromId;
	$to = $entity->toId;
	if (!is_array($from)) {
		$from = array($from);
	}
	if (!is_array($to)) {
		$to = array($to);
	}

	$recipient_guids = array_merge($from, $to);
}

if (is_array($recipient_guids)) {

	foreach ($recipient_guids as $guid) {

		if ($guid == elgg_get_logged_in_user_guid()) {
			continue;
		}

		$user = get_entity($guid);
		if (!elgg_instanceof($user))
			continue;

		$icon = elgg_view('output/img', array(
			'src' => $user->getIconURL('tiny'),
			'width' => 16,
			'height' => 16
		));
		$name = $user->name;
		$user_names[] = '<span class="inbox-conversation-user">' . $icon . $name . '</span>';

		$recipients .= elgg_view('input/hidden', array(
			'name' => 'recipient_guids[]',
			'value' => $user->guid
		));
	}

	$conversation = implode('', $user_names);
} else {

	$recipients = elgg_view('input/messages/userpicker', array(
		'name' => 'recipient_guids',
		'endpoint' => "messages/userpicker?message_type=$message_type",
		'value' => $recipient_guids,
		'multiple' => elgg_extract('multiple', $rules, false)
	));
}

$body .= (elgg_in_context('inbox-reply')) ? '<div class="hidden">' : '<div>';
$body .= '<label>' . elgg_echo("messages:to") . '</label>';
$body .= $recipients;
$body .= $conversation;
$body .= '</div>';

if (!$entity) {
	$body .= '<div>';
	$body .= '<label>' . elgg_echo("messages:title") . '</label>';
	$body .= elgg_view('input/text', array(
		'name' => 'subject',
		'value' => $subject
	));
	$body .= '</div>';
} else {
	$body .= elgg_view('input/hidden', array(
		'name' => 'subject',
		'value' => "Re: " . trim(str_replace('re:', '', strtolower($entity->title)))
	));
}


$body .= '<div>';
$body .= '<label>' . elgg_echo("messages:message") . '</label>';
$body .= elgg_view("input/plaintext", array(
	'name' => 'body',
	'value' => $message,
		));
$body .= '</div>';

if (elgg_extract('attachments', $rules, false)) {
	$body .= '<div>';
	$body .= '<label>' . elgg_echo("messages:attachments") . '</label>';
	$body .= elgg_view("input/file", array(
		'name' => 'attachments[]',
		'multiple' => true
	));
	$body .= '</div>';
}


$body .= '<div class = "elgg-foot">';
if ($entity) {
	$body .= elgg_view('input/hidden', array(
		'name' => 'message_type',
		'value' => $entity->msgType
	));
	$body .= elgg_view('input/hidden', array(
		'name' => 'message_hash',
		'value' => $entity->msgHash
	));
} else {
	$body .= elgg_view('input/hidden', array(
		'name' => 'message_type',
		'value' => $message_type
	));
}

$body .= elgg_view('input/submit', array(
	'value' => elgg_echo('messages:send')
		));
$body .= '</div>';

echo $body;