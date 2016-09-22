<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$full = elgg_extract('full_view', $vars, false);
if (!$full) {
	return true;
}

echo elgg_view('output/attachments', $vars);