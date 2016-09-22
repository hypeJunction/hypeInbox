<?php

use hypeJunction\Inbox\Message;

$guid = elgg_extract('guid', $vars);

elgg_entity_gatekeeper($guid);

$message = get_entity($guid);

elgg_require_js('framework/inbox/user');

$message_type = $message->msgType;
$subject = $message->getDisplayName();

$type_label = elgg_echo("item:object:message:$message_type:plural");
$type_url = "messages/inbox/$page_owner->username?message_type=$message_type";

elgg_push_breadcrumb(elgg_echo('inbox'), "messages/inbox/$page_owner->username");
elgg_push_breadcrumb(elgg_echo('inbox:message_type', array($type_label)), $type_url);
elgg_push_breadcrumb(elgg_get_excerpt($subject, 50));

$params = array(
	'entity' => $message,
	'message_type' => $message_type,
);

$menu_items = hypeInbox()->hooks->setupInboxThreadMenu(null, null, array(), array('entity' => $message));
foreach ($menu_items as $item) {
	elgg_register_menu_item('title', $item);
}

$thread = elgg_view('framework/inbox/thread', $params);

if (elgg_is_xhr()) {
	echo $thread;
} else {
	$content = elgg_view('framework/inbox/participants', $params);
	$content .= elgg_view('framework/inbox/controls/thread', $params);
	$content .= $thread;
	$content .= elgg_view('framework/inbox/reply', $params);

	$content = elgg_format_element('div', [
		'class' => 'inbox-message-block',
	], $content);
	
	$layout = elgg_view_layout('content', array(
		'title' => $subject,
		'filter' => false,
		'content' => $content,
		'sidebar' => elgg_view('framework/inbox/sidebar', $params),
		'class' => 'inbox-layout inbox-thread-layout',
	));
	echo elgg_view_page($title, $layout);
}
