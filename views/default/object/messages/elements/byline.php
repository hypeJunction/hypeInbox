<?php

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$threaded = elgg_extract('threaded', $vars, !$full);

$sender = $entity->getSender();

if ($sender->guid == elgg_get_logged_in_user_guid()) {
	$by = elgg_echo('inbox:me');
} else {
	$by = $sender->getDisplayName();
}

if ($threaded) {
	echo elgg_echo('inbox:byline:thread', [$by]);
} else {
	echo elgg_echo('inbox:byline', [$by]);
}
