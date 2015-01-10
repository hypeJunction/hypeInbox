<?php

namespace hypeJunction\Inbox;

$user = elgg_get_logged_in_user_entity();

$message_type = elgg_extract('message_type', $vars, Message::TYPE_PRIVATE);
$outtypes = get_outgoing_message_types($user);

if (!in_array($message_type, $outtypes)) {
	//echo elgg_echo('actionunauthorized');
	return;
}

$action = "messages/send/$message_type";
$form = elgg_view_exists("forms/$action") ? $action : "messages/send";

echo elgg_view_form($form, array(
	'action' => elgg_action_exists($action) ? "action/$action" : "action/messages/send",
		), $vars);
