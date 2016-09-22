<?php

$page_owner = elgg_get_page_owner_entity();

elgg_require_js('framework/inbox/user');

elgg_push_breadcrumb(elgg_echo('inbox'), "messages/inbox/$page_owner->username");
elgg_push_breadcrumb(elgg_echo('inbox:sent'), "messages/outbox/$page_owner->username");

$params = array(
	'filter_context' => 'sent',
);

$layout = elgg_view_layout('content', array(
	'title' => elgg_echo('inbox:sent'),
	'filter' => elgg_view('framework/inbox/filters/inbox', $params),
	'content' => elgg_view('framework/inbox/sent', $params),
	'sidebar' => elgg_view('framework/inbox/sidebar', $params),
	'class' => 'inbox-layout'
		));

echo elgg_view_page($title, $layout);
