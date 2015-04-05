<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);
if (!$full) {
	return true;
}

$qualifiers = elgg_trigger_plugin_hook('extract:qualifiers', 'messages', array('source' => $entity->getBody()), array());

if (!empty($qualifiers['urls'])) {
	foreach ($qualifiers['urls'] as $url) {
		echo elgg_trigger_plugin_hook('format:src', 'embed', array(
			'src' => $url,
				), '');
	}
}
