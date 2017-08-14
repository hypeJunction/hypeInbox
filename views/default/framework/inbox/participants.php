<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars, false);
if (!$entity instanceof Message) {
	return true;
}

echo '<div class="inbox-thread-participants">';
echo '<h5 class="title is-5">' . elgg_echo('inbox:thread:participants') . '</h5>';
echo elgg_view_entity_list($entity->getParticipants(), [
	'list_type' => 'gallery',
	'full_view' => false,
	'gallery_class' => 'elgg-gallery-users',
	'size' => 'small',
	'limit' => 0,
	'pagination' => false,
]);
echo '</div>';