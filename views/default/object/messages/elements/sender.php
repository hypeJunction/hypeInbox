<?php

$size = elgg_extract('size', $vars, 'small');
$entity = elgg_extract('entity', $vars);

echo elgg_view_entity_icon($entity, $size, array(
	'use_hover' => elgg_extract('full_view', $vars, false),
));