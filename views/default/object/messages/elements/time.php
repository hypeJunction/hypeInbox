<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_friendly_time($entity->time_created);