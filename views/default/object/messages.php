<?php

use hypeJunction\Inbox\Message;

/**
 * Display a message
 * @uses $vars['entity']   Message
 * @uses $vars['threaded'] Threaded message display
 */

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);
$threaded = elgg_extract('threaded', $vars, !$full);

$icon = elgg_view('object/messages/elements/sender', $vars);
$title = elgg_view('object/messages/elements/participants', $vars);
$subtitle = elgg_view('object/messages/elements/time', $vars);
$metadata = elgg_view('object/messages/elements/menu', $vars);

$content = elgg_view('object/messages/elements/subject', $vars);
$content .= elgg_view('object/messages/elements/body', $vars);
$content .= elgg_view('object/messages/elements/attachments', $vars);
$content .= elgg_view('object/messages/elements/embeds', $vars);

$summary = elgg_view('object/elements/summary', array(
	'entity' => $entity,
	'title' => ($title) ?: false,
	'subtitle' => $subtitle,
	'metadata' => $metadata,
	'content' => $content,
		));

$body = elgg_view_image_block($icon, $summary, array(
	'class' => 'inbox-message-image-block',
));

if (elgg_in_context('inbox-form')) {
	$checkbox = elgg_view('object/messages/elements/checkbox', $vars);
	$body = $checkbox . $body;
}

$attrs = elgg_format_attributes(array(
	'data-href' => ($full) ? false : $entity->getURL(),
	'data-guid' => $entity->guid,
	'class' => implode(' ', array_filter(array(
		elgg_extract('class', $vars, null),
		'inbox-message',
		($entity->isRead($threaded)) ? 'inbox-message-read' : 'inbox-message-unread',
		($threaded) ? 'inbox-message-threaded' : 'inbox-message-full',
		(elgg_in_context('inbox-form')) ? 'inbox-message-form-row' : '',
	))),
		));

echo "<article $attrs>$body</article>";

if ($full && !$entity->isRead()) {
	$entity->markRead();
}