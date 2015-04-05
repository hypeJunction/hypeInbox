<?php

$full = elgg_extract('full_view', $vars, false);
if ($full) {
	return true;
}

$entity = elgg_extract('entity', $vars);
if ($entity->title) {
	echo elgg_format_element('span', array(
		'class' => 'inbox-message-subject',
			), $entity->title);
}