<?php

$user = elgg_get_logged_in_user_entity();

$message_type = elgg_extract('message_type', $vars, HYPEINBOX_PRIVATE);
$outtypes = hj_inbox_get_outgoing_message_types($user);

if (!in_array($message_type, $outtypes)) {
	//echo elgg_echo('actionunauthorized');
	return;
}

$action = "messages/send/$message_type";

$view = elgg_view_exists("forms/$action") ? $action : "messages/send";
$action = elgg_action_exists($action) ? "action/$action" : "action/messages/send";

echo elgg_view_form($view, array(
	'action' => $action,
	'enctype' => 'multipart/form-data'
), $vars);