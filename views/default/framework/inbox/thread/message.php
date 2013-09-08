<?php

elgg_push_context('inbox-thread');
echo elgg_view_entity($vars['entity'], array(
	'full_view' => true
));
elgg_pop_context();