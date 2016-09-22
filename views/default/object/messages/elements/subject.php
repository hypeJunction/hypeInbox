<?php

$full = elgg_extract('full_view', $vars, false);
if ($full) {
	return;
}

$entity = elgg_extract('entity', $vars);
if ($entity->title) {

	if (elgg_is_active_plugin('search') && get_input('query')) {
		if ($entity->getVolatileData('search_matched_title')) {
			$title = $entity->getVolatileData('search_matched_title');
		} else {
			$title = search_get_highlighted_relevant_substrings($entity->getDisplayName(), get_input('query'), 5, 5000);
		}
	} else {
		$title = $entity->title;
	}

	echo elgg_format_element('span', array(
		'class' => 'inbox-message-subject',
	), $title);
}