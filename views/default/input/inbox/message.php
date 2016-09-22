<?php

/**
 * Comment input
 */
$defaults = [
	'rows' => 5,
	'placeholder' => elgg_echo('inbox:message:body'),
	'id' => "elgg-input-" . base_convert(mt_rand(), 10, 36),
];

$enable_html = elgg_get_plugin_setting('enable_html', 'hypeInbox');

$class = (array) elgg_extract('class', $vars, []);
$class[] = 'elgg-input-message-body';
$vars['class'] = $class;

$vars = array_merge($defaults, $vars);

$value = htmlspecialchars($vars['value'], ENT_QUOTES, 'UTF-8');
unset($vars['value']);

if ($enable_html) {
	echo elgg_view_menu('longtext', array(
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz',
		'id' => $vars['id'],
	));
}

echo elgg_format_element('textarea', $vars, $value);

if (elgg_is_active_plugin('ckeditor') && $enable_html) {
	?>
	<script>
		require(['input/inbox/message'], function (input) {
			input.init();
		});
	</script>
	<?php

}