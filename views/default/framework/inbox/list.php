<?php

$list_params = array(
	'container_class' => 'inbox-messages',
	'full_view' => false,
	'pagination' => true,
	'no_results' => elgg_echo('inbox:nomessages'),
);

$params = array_merge($list_params, $vars);

elgg_push_context('inbox-table');
echo elgg_view('page/components/list', $params);
elgg_pop_context();