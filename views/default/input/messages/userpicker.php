<?php
/**
 * Glossary input field
 *
 * @uses $vars['endpoint'] Endpoint url used to populate the picker
 * @uses $vars['name'] Input name
 * @uses $vars['value']  Current value
 * @uses $vars['class']  Additional CSS class
 */

$endpoint = elgg_extract('endpoint', $vars, '');
$name = elgg_extract('name', $vars, 'guids');
$value = elgg_extract('value', $vars, false);
$multiple = elgg_extract('multiple', $vars, false);

$class = 'userpicker-glossary';

if (isset($vars['class'])) {
	$class = "$class {$vars['class']}";
	unset($vars['class']);
}

$alphabet = range('a', 'z');
$alphabet[] = '*';

if (!is_array($value)) {
	$value = array();
}


foreach ($alphabet as $letter) {
	$link = elgg_view('output/url', array(
		'text' => $letter,
		'title' => $letter,
		'href' => elgg_http_add_url_query_elements($endpoint, array('letter' => $letter)),
		'rel' => false,
		'data-glossary' => $letter,
		'data-limit' => HYPEINBOX_USERPICKER_BATCH_SIZE
	));

	$tabs .= <<<__TAB
<li class="elgg-tab">
	$link
	<div data-glossary-info="$letter" class="elgg-content"></div>
</li>
__TAB;

}

$hidden = elgg_view('input/hidden', array(
	'name' => $name,
	'value' => 0
));

$attr = elgg_format_attributes(array(
	'class' => $class,
	'id' => str_replace(array(' ', '.'), '-', microtime()),
	'data-endpoint' => $endpoint,
	'data-name' => $name,
	'data-value' => ($value) ? json_encode($value) : '[]',
	'data-limit' => HYPEINBOX_USERPICKER_BATCH_SIZE,
	'data-multiple' => $multiple,
));

echo <<<__HTML
$hidden
<div $attr>
	<ul data-tabs class="elgg-tabs">$tabs</ul>
</div>

__HTML;

