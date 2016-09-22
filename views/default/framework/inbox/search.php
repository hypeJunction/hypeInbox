<?php

$options = array(
	'type' => 'object',
	'subtype' => 'messages',
	'order_by' => 'e.time_created desc',
	'limit' => elgg_extract('limit', $vars, max(20, elgg_get_config('default_limit'))),
	'full_view' => false,
	'no_results' => elgg_echo('messages:no_results'),
	'preload_owners' => true,
	'preload_containers' => true,
	'list_id' => 'messages-search',
	'base_url' => elgg_normalize_url('messages/search'),
	'pagination_type' => 'infinite',
	'pagination' => elgg_extract('pagination', $vars),
	'owner_guid' => (int) elgg_get_page_owner_guid(),
	'threaded' => false,
);

echo elgg_view('lists/objects', [
	'show_filter' => false,
	'filter_target' => null,
	'show_search' => true,
	'show_sort' => true,
	'sort_options' => [
		'time_created::desc',
		'time_created::asc',
	],
	'sort' => get_input('sort', 'time_created::desc'),
	'options' => $options,
]);

