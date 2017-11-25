<?php

use hypeJunction\Inbox\Message;

$page_owner = elgg_get_page_owner_entity();
if (!$page_owner || !$page_owner->canEdit()) {
	forward('', '404');
}

elgg_require_js('framework/inbox/user');

$message_type = get_input('message_type', Message::TYPE_PRIVATE);

$type_label = elgg_echo("item:object:message:$message_type:plural");
$type_url = "messages/sent/$page_owner->username?message_type=$message_type";

elgg_push_breadcrumb(elgg_echo('inbox'), "messages/inbox/$page_owner->username");
elgg_push_breadcrumb(elgg_echo('inbox:message_type:sent', array($type_label)), $type_url);

$outgoing_message_types = hypeInbox()->model->getOutgoingMessageTypes();
foreach ($outgoing_message_types as $mt) {
	elgg_register_menu_item('title', [
		'name' => ($mt == HYPEINBOX_PRIVATE) ? "send" : "compose:$mt",
		'text' => elgg_echo('inbox:new', array(elgg_echo("item:object:message:$mt:singular"))),
		'href' => elgg_http_add_url_query_elements('messages/compose', [
			'message_type' => $mt,
			'send_to' => get_input('send_to', null),
		]),
		'link_class' => 'elgg-button elgg-button-action',
	]);
}

$params = [
	'message_type' => $message_type,
	'filter_context' => "sent-$message_type",
];

$layout = elgg_view_layout('content', [
	'title' => elgg_echo('inbox:sent'),
	'filter' => elgg_view('framework/inbox/filters/inbox', $params),
	'content' => elgg_view('framework/inbox/sent', $params),
	'class' => 'inbox-layout'
]);

echo elgg_view_page($title, $layout);
