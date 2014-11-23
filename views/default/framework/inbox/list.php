<?php

namespace hypeJunction\Inbox;

$list_params = array(
	'item_class' => 'inbox-message-item',
	'list_class' => 'inbox-messages-table',
	'full_view' => false,
	'pagination' => true,
	'no_results' => elgg_echo('inbox:nomessages'),
);

$params = array_merge($list_params, $vars);

elgg_push_context('inbox-table');
echo elgg_view('page/components/list', $params);
elgg_pop_context();