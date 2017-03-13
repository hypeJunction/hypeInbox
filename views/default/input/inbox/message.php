<?php

/**
 * Comment input
 */
$defaults = [
	'rows' => 5,
	'placeholder' => elgg_echo('inbox:message:body'),
	'id' => "elgg-input-" . base_convert(mt_rand(), 10, 36),
	'editor' => true,
	'visual' => false,
];

$enable_html = elgg_get_plugin_setting('enable_html', 'hypeInbox');

$class = (array) elgg_extract('class', $vars, []);
$class[] = 'elgg-input-message-body';
$vars['class'] = $class;

$vars = array_merge($defaults, $vars);

if ($enable_html) {
	echo elgg_view_input('longtext', $vars);
} else {
	echo elgg_view_input('plaintext', $vars);
}