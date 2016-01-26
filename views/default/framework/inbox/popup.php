<?php

/**
 * Create an empty popup module to be populated on demand via XHR request.
 */

if (!elgg_is_logged_in) {
	return;
}

elgg_require_js('framework/inbox/popup');

$list = elgg_format_element('div', [
	'id' => 'inbox-messages'
		]);

$footer = elgg_view('output/url', array(
	'href' => hypeInbox()->router->normalize('incoming'),
	'text' => elgg_echo('inbox:inbox'),
	'is_trusted' => true,
		));

$footer = elgg_format_element('div', ['class' => 'elgg-foot'], $footer);
$body = $list . $footer;

echo elgg_format_element('div', [
	'class' => 'elgg-module elgg-module-popup elgg-inbox-popup hidden',
	'id' => 'inbox-popup'
		], $body);
