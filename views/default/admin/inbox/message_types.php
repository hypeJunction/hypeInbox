<?php

elgg_load_css('inbox.base.css');
elgg_load_js('inbox.admin.js');

$message_types = elgg_get_config('inbox_message_types');

foreach ($message_types as $type => $options) {

	$title = elgg_echo("item:object:message:$type:plural") . " ($type)";

	$options['name'] = $type;
	$body = '<div class="inbox-folder-options">';
	$body .= elgg_view('forms/framework/inbox/message_type', $options);
	$body .= '</div>';

	$body = '<div class="pal">' . $body . '</div>';
	$form .= elgg_view_module('widget', $title, $body);
}

$title = elgg_echo("item:object:message:create");
$body = '<div class="inbox-folder-options">';
$body .= elgg_view('forms/framework/inbox/message_type');
$body .= '</div>';

$body = '<div class="pal">' . $body . '</div>';
$form .= elgg_view_module('widget', $title, $body);

$form .= '<div class="elgg-foot">';
$form .= elgg_view('input/submit', array(
	'value' => elgg_echo('save')
));
$form .= '</div>';

echo elgg_view('input/form', array(
	'action' => 'action/hypeInbox/settings/save',
	'body' => $form,
));
