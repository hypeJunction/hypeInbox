<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view('input/checkbox', array(
	'name' => 'guids[]',
	'default' => false,
	'value' => $entity->guid,
));