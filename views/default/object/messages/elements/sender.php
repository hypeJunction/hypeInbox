<?php

$size = elgg_extract('size', $vars, 'small');
$entity = elgg_extract('entity', $vars);
/* @var $entity \hypeJunction\Inbox\Message */

$sender = $entity->getSender();

echo elgg_view_entity_icon($sender, $size, array(
	'use_hover' => elgg_extract('full_view', $vars, false),
	'use_link' => false,
	'href' => false,
));