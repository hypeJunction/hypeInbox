<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);
if (!$full) {
	return true;
}

$threaded = elgg_extract('threaded', $vars, !$full);

$count = $entity->hasAttachments(array(), $threaded);
if (!$count) {
	return true;
}

$attachments = $entity->getAttachments(array('limit' => 0), $threaded);
$images = $details = array();
foreach ($attachments as $attachment) {
	if ($attachment->simpletype == 'image') {
		$images[] = '<div>' . elgg_view_entity_icon($attachment, 'large', array(
			'href' => false,
		)) . '</div>';
	}

	$icon = elgg_view_entity_icon($attachment, 'small', array(
		'href' => false,
	));

	$download = elgg_view('output/url', array(
		'text' => elgg_view_icon('download'),
		'href' => "file/download/$attachment->guid"
	));

	$item = elgg_view_image_block($icon, $attachment->title, array(
		'image_alt' => $download,
	));
	$details[] = elgg_format_element('div', array(
		'class' => 'inbox-message-attachment-row',
			), $item);
}

echo '<div class="inbox-message-attachments">';
echo implode('', $details);
echo '</div>';

if (count($images)) {
	echo '<div class="inbox-message-image-attachments">';
	echo implode('', $images);
	echo '</div>';
}