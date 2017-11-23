<?php

/**
 * Display a message
 * 
 * @uses $vars['entity']   Message
 * @uses $vars['threaded'] Threaded message display
 */
use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);
$threaded = elgg_extract('threaded', $vars, !$full);

$icon = elgg_view('object/messages/elements/sender', $vars);
$title = elgg_view('object/messages/elements/subject', $vars);
if (!$title) {
	$title = elgg_view('object/messages/elements/participants', $vars);
}
$subtitle = [];
$subtitle[] = elgg_view('object/messages/elements/byline', $vars);
$subtitle[] = elgg_view('object/messages/elements/time', $vars);

if (elgg_is_active_plugin('hypeUI')) {
	$metadata = null;
} else {
	$metadata = elgg_view('object/messages/elements/menu', $vars);
}
$content .= elgg_view('object/messages/elements/body', $vars);
$content .= elgg_view('object/messages/elements/attachments', $vars);
$content .= elgg_view('object/messages/elements/embeds', $vars);

$summary = elgg_view('object/elements/summary', array(
	'entity' => $entity,
	'title' => $title ? : false,
	'subtitle' => implode(' ', $subtitle),
	'metadata' => $metadata,
	'content' => $content,
	'icon' => $icon,
));

$checkbox = '';
if (elgg_in_context('inbox-form') || elgg_in_context('sent-form')) {
	$checkbox = elgg_format_element('div', [
		'class' => 'inbox-message-checkbox',
	], elgg_view('object/messages/elements/checkbox', $vars));
}

$icon = elgg_format_element('div', ['class' => 'inbox-message-icon'], $icon);
$summary = elgg_format_element('div', ['class' => 'inbox-message-content'], $summary);

$body = $checkbox . $summary;

$attrs = elgg_format_attributes(array(
	'data-href' => ($full) ? false : $entity->getURL(),
	'data-guid' => $entity->guid,
	'class' => implode(' ', array_filter(array(
		elgg_extract('class', $vars, null),
		'inbox-message',
		($entity->isRead($threaded)) ? 'inbox-message-read' : 'inbox-message-unread',
		($threaded) ? 'inbox-message-threaded' : 'inbox-message-full',
		(elgg_in_context('inbox-form') || elgg_in_context('sent-form')) ? 'inbox-message-form-row' : '',
	))),
));

echo "<article $attrs>$body</article>";

if ($full && !$entity->isRead()) {
	$entity->markRead();
	echo elgg_view('notifier/view_listener', array(
		'entity' => $entity,
	));
}