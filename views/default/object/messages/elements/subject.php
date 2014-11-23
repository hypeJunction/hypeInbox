<?php

namespace hypeJunction\Inbox;
$full = elgg_extract('full_view', $vars, false);
if ($full) {
	return true;
}

$entity = elgg_extract('entity', $vars);
echo $entity->title;