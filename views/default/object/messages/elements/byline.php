<?php

$entity = elgg_extract('entity', $vars);
/* @var $entity Message */

$threaded = elgg_extract('threaded', $vars, !$full);

$sender = $entity->getSender();

if ($threaded) {
	echo elgg_echo('inbox:byline:thread', [$sender->getDisplayName()]);
} else {
	echo elgg_echo('inbox:byline', [$sender->getDisplayName()]);
}
