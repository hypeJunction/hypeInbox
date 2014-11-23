<?php

namespace hypeJunction\Inbox;

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
		$images[] = elgg_view_entity_icon($attachment, 'large', array(
			'href' => false,
		));
	}

	$icon = elgg_view_entity_icon($attachment, 'small', array(
		'href' => false,
	));

	$download = elgg_view('output/url', array(
		'text' => elgg_view_icon('download'),
		'href' => "file/download/$attachment->guid"
	));
	$item = '<li>';
	$item .= '<div class="icon">' . $icon . '</div>';
	$item .= '<div>' . $attachment->title . '</div>';
	$item .= '<div>' . $download . '</div>';
	$item .= '</li>';
	$details[] = $item;
}

echo '<ul class="inbox-message-attachments">';
echo implode('', $details);
echo '</ul>';

if (count($images)) {
	echo '<div class="inbox-message-image-attachments">';
	echo implode('', $images);
	echo '</div>';
}