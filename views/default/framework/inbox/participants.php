<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars, false);
if (!$entity instanceof Message) {
	return true;
}

echo '<div class="inbox-thread-participants">';
echo '<label>' . elgg_echo('inbox:thread:participants') . '</label>';
echo elgg_view('page/components/gallery', array(
	'items' => $entity->getParticipants(),
	'gallery_class' => 'elgg-gallery-users',
	'size' => 'small',
	'limit' => 0,
));
echo '</div>';