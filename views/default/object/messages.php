<?php

/**
 * Display a message
 * @uses $vars['entity']   Message
 * @uses $vars['threaded'] Threaded message display
 */

namespace hypeJunction\Inbox;

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);
$threaded = elgg_extract('threaded', $vars, !$full);

$elements = array(
	'controls' => array_filter(array(
		elgg_in_context('inbox-form') ? 'checkbox' : null,
		'attachments-indicator',
		elgg_in_context('inbox-table') || elgg_in_context('inbox-sent') ? 'unread-indicator' : null,
	)),
	'sender',
	'details' => array(
		'participants',
		'subject',
		'body',
		'time',
		'attachments',
		'embeds',
	),
	'menu',
);

$body = '';
foreach ($elements as $group => $elements) {
	if (is_string($elements)) {
		$group = $elements;
		$elements = array($elements);
	}
	$cell = '';
	foreach ($elements as $elem) {
		$view = elgg_view("object/messages/elements/$elem", $vars);
		if ($view) {
			$cell .= "<div class=\"inbox-message-element-$elem\">$view</div>";
		}
	}

	$body .= elgg_format_element('div', array('class' => "inbox-message-cell-$group"), $cell);
}

$attrs = elgg_format_attributes(array(
	'data-resource' => 'messages',
	'data-href' => ($full) ? false : $entity->getURL(),
	'data-guid' => $entity->guid,
	'data-hash' => $entity->getHash(),
	'data-read' => ($entity->isRead($threaded)) ? 'yes' : 'no',
	'data-form' => (elgg_in_context('inbox-form')) ? 'yes' : 'no',
	'class' => implode(' ', array_filter(array(
		elgg_extract('class', $vars, null),
		'inbox-message',
		'inbox-message-format-table',
	))),
));

echo "<article $attrs>$body</article>";

if ($full) {
	$entity->markRead();
}