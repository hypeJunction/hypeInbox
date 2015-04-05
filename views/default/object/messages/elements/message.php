<?php

$title = ($entity->title) ? $entity->title : elgg_echo('inbox:untitled');
$summary = elgg_get_excerpt(strip_tags($entity->description), 200);
$desc = elgg_view('output/longtext', array(
	'value' => $entity->description,
	'class' => 'inbox-message-body'
		));
$tags = elgg_view('output/tags', array(
	'entity' => $entity
		));

echo elgg_view('output/url', array(
	'text' => "<b>$title</b> - $summary",
	'href' => $entity->getURL(),
		));
