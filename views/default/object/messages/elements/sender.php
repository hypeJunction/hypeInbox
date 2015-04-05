<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_entity_icon($entity, 'small', array(
	'use_hover' => false,
));