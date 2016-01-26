<?php

if (elgg_in_context('widgets')) {
	return;
}

$params = array(
	'handler' => 'messages',
	'sort_by' => 'priority',
	'class' => 'inbox-menu elgg-menu-hz',
);
$params = array_merge($vars, $params);
echo elgg_view_menu('entity', $params);
