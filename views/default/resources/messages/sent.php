<?php
use hypeJunction\Inbox\Message;

$page_owner = elgg_get_page_owner_entity();
if (!$page_owner || !$page_owner->canEdit()) {
	forward('', '404');
}

elgg_require_js('framework/inbox/user');

elgg_push_breadcrumb(elgg_echo('inbox'), "messages/inbox/$page_owner->username");
elgg_push_breadcrumb(elgg_echo('inbox:sent'), "messages/outbox/$page_owner->username");

$outgoing_message_types = hypeInbox()->model->getOutgoingMessageTypes();
foreach ($outgoing_message_types as $mt) {
	elgg_register_menu_item('title', array(
		'name' => ($mt == HYPEINBOX_PRIVATE) ? "send" : "compose:$mt",
		'text' => elgg_echo('inbox:new', array(elgg_echo("item:object:message:$mt:singular"))),
		'href' => elgg_http_add_url_query_elements('messages/compose', array(
			'message_type' => $mt,
			'send_to' => get_input('send_to', null),
		)),
		'link_class' => 'elgg-button elgg-button-action',
	));
}

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
