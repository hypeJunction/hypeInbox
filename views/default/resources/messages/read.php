<?php

namespace hypeJunction\Inbox;

$segments = elgg_extract('segments', $vars, array());
$message = get_entity($segments[1]);

if (!$message instanceof Message) {
	return true;
}

elgg_load_css('fonts.font-awesome');
elgg_load_css('inbox.base.css');
elgg_require_js('framework/inbox/user');

$page_owner = elgg_get_page_owner_entity();
$message_type = get_input('message_type', Message::TYPE_PRIVATE);
$subject = $message->getDisplayName();

$type_label = elgg_echo("item:object:message:$message_type:plural");
$type_url = "messages/inbox/$page_owner->username?message_type=$message_type";

elgg_push_breadcrumb(elgg_echo('inbox'), "messages/inbox/$page_owner->username");
elgg_push_breadcrumb(elgg_echo('inbox:message_type', array($type_label)), $type_url);
elgg_push_breadcrumb(elgg_get_excerpt($subject, 50));

$before = $after = true;
$chronology = $segments[2];
if ($chronology == 'after') {
	$before = false;
} else if ($chronology == 'before') {
	$after = false;
}
$params = array(
	'entity' => $message,
	'message_type' => $message_type,
	'before' => $before,
	'after' => $after,
);

$content['participants'] = elgg_view('framework/inbox/participants', $params);
$content['controls'] = elgg_view('framework/inbox/controls/thread', $params);
$content['thread'] = elgg_view('framework/inbox/thread', $params);
$content['reply'] = elgg_view('framework/inbox/reply', $params);

if (elgg_is_xhr()) {
	echo $content['thread'];
} else {
	$layout = elgg_view_layout('content', array(
		'title' => $subject,
		'filter' => false,
		'content' => implode('', $content),
		'sidebar' => elgg_view('framework/inbox/sidebar', $params),
		'class' => 'inbox-layout inbox-thread-layout',
	));
	echo elgg_view_page($title, $layout);
}
