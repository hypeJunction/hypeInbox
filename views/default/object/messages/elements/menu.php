<?php

namespace hypeJunction\Inbox;

$entity = elgg_extract('entity', $vars);

echo elgg_view_menu('entity', array(
	'entity' => $entity,
	'handler' => 'messages',
	'sort_by' => 'priority',
	'params' => $vars,
	'class' => 'inbox-menu',
));
