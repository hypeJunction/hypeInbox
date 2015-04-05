<?php

elgg_require_js('framework/inbox/admin');

$title = elgg_echo('inbox:admin:import');

$body = '<p class="mam">' . elgg_echo('inbox:admin:import_stats', array($vars['count'])) . '</p>';
$body .= elgg_view('output/url', array(
	'id' => 'inbox-admin-import',
	'text' => elgg_echo('inbox:admin:import_start'),
	'class' => 'elgg-button elgg-button-action float mam',
	'rel' => elgg_echo('inbox:admin:import_warning'),
	'data-count' => $vars['count']
));
$body .= '<div id="import-progress" class="mam"></div>';

echo elgg_view_module('widget', $title, $body);