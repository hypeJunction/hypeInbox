<?php

namespace hypeJunction\Inbox;

$limit = get_input('limit', 2);
$user = elgg_get_page_owner_entity();

$entity = elgg_extract('entity', $vars, false);
if (!$entity instanceof Message) {
	return true;
}

$thread = new Thread($entity);

$before_flag = elgg_extract('before', $vars, true);
$after_flag = elgg_extract('after', $vars, true);

if ($before_flag) {
	$count_before = $thread->getMessagesBefore(array('count' => true));
	$before = ($count_before) ? $thread->getMessagesBefore(array('limit' => $limit)) : array();
	if ($count_before > $limit) {
		$items_to_load = ($count_before - $limit > $limit) ? $limit : $count_before - $limit;
		$before_link = elgg_view('output/url', array(
			'text' => elgg_echo('inbox:load:before', array($items_to_load)),
			'href' => "messages/thread/{$before[0]->guid}/before?limit=$limit",
			'class' => 'inbox-thread-load-before'
		));
	}
} else {
	$before = array();
}

if ($after_flag) {
	$count_after = $thread->getMessagesAfter(array('count' => true));
	$after = ($count_after) ? $thread->getMessagesAfter(array('limit' => $limit)) : array();

	if ($count_after > $limit) {
		$items_to_load = ($count_after - $limit > $limit) ? $limit : $count_after - $limit;
		$after_link = elgg_view('output/url', array(
			'text' => elgg_echo('inbox:load:after', array($items_to_load)),
			'href' => "messages/thread/{$after[$limit - 1]->guid}/after?limit=$limit",
			'class' => 'inbox-thread-load-after'
		));
	}
} else {
	$after = array();
}

if ($before_flag && $after_flag) {
	$messages = array_merge($before, array($entity), $after);
} else if ($before_flag) {
	$messages = $before;
} else {
	$messages = $after;
}

elgg_push_context('inbox-thread');

echo $before_link;
echo elgg_view('framework/inbox/list', array(
	'items' => $messages,
	'pagination' => false,
	'full_view' => true,
));
echo $after_link;

elgg_pop_context();

