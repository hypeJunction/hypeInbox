<?php

$user = elgg_get_page_owner_entity();

echo '<div>';
echo '<label>' . elgg_echo('hj:inbox:usersettings:grouping') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[grouping]',
	'value' => elgg_get_plugin_user_setting('grouping', $user->guid, 'hypeInbox'),
	'options_values' => array(
		true => elgg_echo('hj:inbox:group'),
		false => elgg_echo('hj:inbox:dontgroup')
	)
));
echo '</div>';