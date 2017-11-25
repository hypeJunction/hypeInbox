<?php

$page_owner = elgg_get_page_owner_entity();
if (!$page_owner || !$page_owner->canEdit()) {
	forward('', '404');
}

elgg_require_js('framework/inbox/user');

elgg_push_breadcrumb(elgg_echo('inbox'), "messages/inbox/$page_owner->username");
elgg_push_breadcrumb(elgg_echo('inbox:search'));

$params = [
	'filter_context' => 'search',
];

$content = elgg_view('framework/inbox/search', $params);
if (elgg_is_xhr()) {
	echo $content;
} else {
	$layout = elgg_view_layout('content', [
		'title' => elgg_echo('inbox:search'),
		'filter' => elgg_view('framework/inbox/filters/inbox', $params),
		'content' => $content,
		'class' => 'inbox-layout'
	]);
	echo elgg_view_page($title, $layout);
}
