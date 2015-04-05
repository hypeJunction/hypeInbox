<?php

use hypeJunction\Inbox\Message;
use hypeJunction\Inbox\Thread;

$limit = get_input('limit', 5);
$offset = get_input('offset', null);

$user = elgg_get_page_owner_entity();

$entity = elgg_extract('entity', $vars, false);
if (!$entity instanceof Message) {
	return true;
}

$thread = new Thread($entity);
if (is_null($offset)) {
	$offset = $thread->getOffset($limit);
}
$messages = $thread->getMessages(array(
	'limit' => $limit,
	'offset' => $offset,
));
$count = $thread->getCount();

elgg_push_context('inbox-thread');
echo elgg_view('framework/inbox/list', array(
	'items' => $messages,
	'count' => $count,
	'limit' => $limit,
	'offset' => $offset,
	'full_view' => true,
	'threaded' => false,
	'list_class' => 'inbox-messages-thread-full',
	
	// hypeList options
	'list_id' => "inbox-thread-" . $entity->getHash(),
	'pagination' => true,
	'position' => 'both',
	'pagination_type' => 'infinite',
	'auto_refresh' => 3,
	'reversed' => true,
	'data-key-text-before' => 'inbox:load:before',
	'data-key-text-after' => 'inbox:load:after',
));
elgg_pop_context();

