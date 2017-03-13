<?php

use hypeJunction\Inbox\Message;

elgg_gatekeeper();

elgg_require_js('framework/inbox/user');

$guid = get_input('guid');
$message = get_entity($guid);

if (!$message instanceof Message) {
	forward('', '404');
}

$message_type = $message->getMessageType();
$action = 'compose';

$params = hypeInbox()->model->prepareFormValues([], $message_type);
$params['forward'] = $message;

$params['subject'] = "Fwd: $message->title";

$enable_html = elgg_get_plugin_setting('enable_html', 'hypeInbox');
if ($enable_html) {
	$params['body'] = '<p>' . elgg_echo('messages:forward:byline', [
				$message->getSender()->getDisplayName(),
				date('H:i j M, Y', $message->time_created),
			]) . '</p>';

	$params['body'] .= '<p><blockquote>' . $message->getBody() . '</blockquote></p>';
} else {
	$params['body'] = PHP_EOL . elgg_echo('messages:forward:byline', [
				$message->getSender()->getDisplayName(),
				date('H:i j M, Y', $message->time_created),
			]) . PHP_EOL . PHP_EOL;
	$lines = explode(PHP_EOL, $message->getBody());
	$lines = array_map(function($elem) {
		return "  >> $elem";
	}, $lines);
	$params['body'] .= implode(PHP_EOL, $lines);
}

$title = elgg_echo("inbox:$action:message_type", array(elgg_echo("item:object:message:$message_type:singular")));

$type_label = elgg_echo("item:object:message:$message_type:plural");
$type_url = "messages/inbox/$page_owner->username?message_type=$message_type";

elgg_push_breadcrumb(elgg_echo('inbox'), "messages/inbox/$page_owner->username");
elgg_push_breadcrumb(elgg_echo('inbox:message_type', array($type_label)), $type_url);
elgg_push_breadcrumb($title);

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'filter' => false,
	'content' => elgg_view('framework/inbox/compose', $params),
	'sidebar' => elgg_view('framework/inbox/sidebar', $params),
	'class' => 'inbox-layout inbox-form-layout'
		));

echo elgg_view_page($title, $layout);
