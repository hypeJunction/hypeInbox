<?php

use hypeJunction\Inbox\Message;

$page_owner = elgg_get_page_owner_entity();
$message_type = get_input('message_type', Message::TYPE_PRIVATE);

elgg_load_css('fonts.font-awesome');
elgg_load_css('inbox.base.css');
elgg_require_js('framework/inbox/user');

$type_label = elgg_echo("item:object:message:$message_type:plural");
$type_url = "messages/inbox/$page_owner->username?message_type=$message_type";

elgg_push_breadcrumb(elgg_echo('inbox'), "messages/inbox/$page_owner->username");
elgg_push_breadcrumb(elgg_echo('inbox:message_type', array($type_label)), $type_url);
elgg_push_breadcrumb(elgg_echo('inbox:sent'), "messages/inbox/$page_owner->username?message_type=$message_type");

$params = array(
	'message_type' => $message_type
);

$layout = elgg_view_layout('content', array(
	'title' => elgg_echo('inbox:sent'),
	'filter' => elgg_view('framework/inbox/filters/sent', $params),
	'content' => elgg_view('framework/inbox/sent', array(
		'action' => false,
			), $params),
	'sidebar' => elgg_view('framework/inbox/sidebar', $params),
	'class' => 'inbox-layout'
		));

echo elgg_view_page($title, $layout);
