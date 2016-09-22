<?php

/**
 * Compose message form
 */
$original_message = elgg_extract('entity', $vars, false);
$message_type = elgg_extract('message_type', $vars);
$recipient_guids = elgg_extract('recipient_guids', $vars, array());
$subject = elgg_extract('subject', $vars, '');
$message = elgg_extract('body', $vars, '');
$multiple = elgg_extract('multiple', $vars, false);
$has_subject = elgg_extract('has_subject', $vars, true);
$allows_attachments = elgg_extract('allows_attachments', $vars, false);

echo elgg_view_input('hidden', array(
	'name' => 'message_type',
	'value' => $message_type,
));
echo elgg_view_input('hidden', array(
	'name' => 'original_guid',
	'value' => $original_message->guid,
));

if (!$original_message) {
	echo elgg_view_input('tokeninput', array(
		'name' => 'recipients',
		'value' => $recipient_guids,
		'multiple' => $multiple,
		'callback' => 'hypeJunction\\Inbox\\Search\\Recipients::search',
		'query' => array(
			'message_type' => $message_type,
		),
		'label' => ($multiple) ? elgg_echo('inbox:message:recipients') : elgg_echo('inbox:message:recipient'),
	));
} else {
	foreach ($recipient_guids as $guid) {
		echo elgg_view_input('hidden', array(
			'name' => 'recipients[]',
			'value' => $guid,
		));
	}
}

if ($has_subject) {
	if (!$original_message) {
		echo elgg_view_input('text', array(
			'name' => 'subject',
			'value' => $subject,
			'label' => elgg_echo('inbox:message:subject'),
		));
	} else {
		$subject = $original_message->getReplySubject();
		echo elgg_view_input('hidden', array(
			'name' => 'subject',
			'value' => $subject,
		));
	}
}

echo elgg_view_input('inbox/message', array(
	'name' => 'body',
	'value' => $message,
	'rows' => 5,
	'label' => ($original_message) ? '' : elgg_echo('inbox:message:body'),
));

echo elgg_view('forms/messages/send/extend', $vars);

if ($allows_attachments) {
	echo elgg_view_input('attachments', [
		'name' => 'message_attachments',
		'expand' => false,
		'field_class' => 'clearfix',
	]);
}

echo elgg_view_input('submit', array(
	'value' => elgg_echo('inbox:message:send'),
	'field_class' => 'elgg-foot',
));
