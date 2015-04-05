<?php

use hypeJunction\Inbox\Message;

$name = elgg_extract('name', $vars, '__new');

if ($name == Message::TYPE_NOTIFICATION || $name == Message::TYPE_PRIVATE) {
	echo elgg_view('input/hidden', array(
		'name' => "message_types[$name][name]",
		'value' => $name
	));
} else {
	echo '<div>';
	echo '<label>' . elgg_echo('item:object:message:name') . '</label>';
	echo elgg_view('input/text', array(
		'name' => "message_types[$name][name]",
		'value' => ($name != '__new') ? $name : ''
	));
	echo '</div>';
}

echo '<div>';
echo '<label>' . elgg_echo('item:object:message:label:singular') . '</label>';
echo elgg_view('input/text', array(
	'name' => "message_types[$name][labels][singular]",
	'value' => $vars['labels']['singular']
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('item:object:message:label:plural') . '</label>';
echo elgg_view('input/text', array(
	'name' => "message_types[$name][labels][plural]",
	'value' => $vars['labels']['plural']
));
echo '</div>';

echo '<br />';

echo '<div><label>' . elgg_view('input/checkbox', array(
	'name' => "message_types[$name][persistent]",
	'checked' => elgg_extract('persistent', $vars, false),
	'value' => 1,
	'default' => false
)) . elgg_echo("item:object:message:setting:persistent") . '</label></div>';

echo '<br />';

echo '<div><label>' . elgg_view('input/checkbox', array(
	'name' => "message_types[$name][no_subject]",
	'checked' => elgg_extract('no_subject', $vars, false),
	'value' => 1,
	'default' => false
)) . elgg_echo("item:object:message:setting:no_subject") . '</label></div>';

echo '<br />';

if ($name == Message::TYPE_NOTIFICATION) {
	return;
}

if (elgg_is_active_plugin('file')) {
	echo '<div><label>' . elgg_view('input/checkbox', array(
		'name' => "message_types[$name][attachments]",
		'checked' => elgg_extract('attachments', $vars, false),
		'value' => 1,
		'default' => false
	)) . elgg_echo("item:object:message:setting:attachments") . '</label></div>';

	echo '<br />';
}

echo '<div><label>' . elgg_view('input/checkbox', array(
	'name' => "message_types[$name][multiple]",
	'checked' => elgg_extract('multiple', $vars, false),
	'value' => 1,
	'default' => false
)) . elgg_echo("item:object:message:setting:multiple") . '</label></div>';

echo '<br />';

// allowed recipients and senders
$types = hypeInbox()->config->getUserTypes();
foreach ($types as $t => $opts) {
	$user_types_options[$t] = elgg_echo("inbox:user_type:$t");
}

$relationships = hypeInbox()->config->getUserRelationships();
$user_relationships_options = array('all' => '');
foreach ($relationships as $r) {
	$user_relationships_options[$r] = $r;
}

$group_relationships = hypeInbox()->config->getUserGroupRelationships();
$user_group_relationships_options = array('all' => '');
foreach ($group_relationships as $r) {
	$user_group_relationships_options[$r] = $r;
}

$policy = elgg_extract('policy', $vars, array(''));

echo '<label>' . elgg_echo('item:object:message:setting:policy') . '</label>';
echo '<div class="elgg-text-help">' . elgg_echo('item:object:message:setting:policy:help') . '</div>';

echo '<div class="clearfix">';
echo '<div class="elgg-col elgg-col-1of6">' . elgg_echo('inbox:sender') . '</div>';
echo '<div class="elgg-col elgg-col-1of6">' . elgg_echo('inbox:recipient') . '</div>';
echo '<div class="elgg-col elgg-col-1of6">' . elgg_echo('inbox:relationship') . '</div>';
echo '<div class="elgg-col elgg-col-1of6">' . elgg_echo('inbox:inverse_relationship') . '</div>';
echo '<div class="elgg-col elgg-col-1of6">' . elgg_echo('inbox:group_relationship') . '</div>';
echo '</div>';

foreach ($policy as $p) {
	echo '<div class="inbox-policy clearfix">';
	echo '<div class="elgg-col elgg-col-1of6">' . elgg_view('input/dropdown', array(
		'name' => "message_types[$name][policy][sender][]",
		'value' => $p['sender'],
		'options_values' => $user_types_options
	)) . '</div>';
	echo '<div class="elgg-col elgg-col-1of6">' . elgg_view('input/dropdown', array(
		'name' => "message_types[$name][policy][recipient][]",
		'value' => $p['recipient'],
		'options_values' => $user_types_options
	)) . '</div>';
	echo '<div class="elgg-col elgg-col-1of6">' . elgg_view('input/dropdown', array(
		'name' => "message_types[$name][policy][relationship][]",
		'value' => $p['relationship'],
		'options_values' => $user_relationships_options
	)) . '</div>';
	echo '<div class="elgg-col elgg-col-1of6">' . elgg_view('input/dropdown', array(
		'name' => "message_types[$name][policy][inverse_relationship][]",
		'value' => $p['inverse_relationship'],
		'options_values' => array(
			'' => '',
			false => elgg_echo('No'),
			true => elgg_echo('Yes')
		)
	)) . '</div>';
	echo '<div class="elgg-col elgg-col-1of6">' . elgg_view('input/dropdown', array(
		'name' => "message_types[$name][policy][group_relationship][]",
		'value' => $p['group_relationship'],
		'options_values' => $user_group_relationships_options
	)) . '</div>';
	echo '<div class="elgg-col elgg-col-1of6"><i class="inbox-icon-plus"></i><i class="inbox-icon-minus"></i></div>';
	echo '</div>';
}



	
