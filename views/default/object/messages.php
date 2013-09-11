<?php

elgg_load_js('inbox.user.js');

$entity = elgg_extract('entity', $vars);

$full = elgg_extract('full_view', $vars, false);
if (is_array($full)) {
	$full = (in_array($entity->guid, $full));
}

$class = "inbox-message clearfix";

if (isset($vars['class'])) {
	$class = "$class {$vars['class']}";
}

$title = ($entity->title) ? $entity->title : elgg_echo('hj:inbox:untitled');
$summary = elgg_get_excerpt(strip_tags($entity->description), 200);
$desc = elgg_view('output/longtext', array(
	'value' => $entity->description,
	'class' => 'inbox-message-body'
		));
$tags = elgg_view('output/tags', array(
	'entity' => $entity
		));

$title = elgg_view('output/url', array(
	'text' => "<b>$title</b> - $summary",
	'href' => $entity->getURL(),
		));

$attachments = elgg_get_entities_from_relationship(array(
	'types' => 'object',
	'subtypes' => 'file',
	'relationship' => 'attached',
	'inverse_relationship' => false,
	'relationship_guid' => $entity->guid,
	'limit' => false
		));

if ($attachments) {
	foreach ($attachments as $attachment) {
		//$attachment->setURL("file/download/$attachment->guid");

		$icon = elgg_view_entity_icon($attachment, 'small');
		$mime = ($attachment->simpletype) ? $attachment->simpletype : $attachment->getMimeType();
		$download_link = elgg_view('output/url', array(
			'text' => elgg_view_icon('clip'),
			'title' => "$mime: $attachment->title",
			'href' => $attachment->getURL()
		));
		$attachments_summary .= $download_link;
		$attachments_list .= elgg_view_entity($attachment, array(
			'full_view' => false
		));
	}
}

access_show_hidden_entities($ha);

if (elgg_in_context('inbox-table') || elgg_in_context('inbox-sent')) {

	$from = $entity->fromId;
	$to = $entity->toId;
	if (!is_array($from)) {
		$from = array($from);
	}
	if (!is_array($to)) {
		$to = array($to);
	}

	$user_guids = array_merge($from, $to);

	$ha = access_get_show_hidden_status();
	access_show_hidden_entities(true);

	$user_names = array();

	foreach ($user_guids as $guid) {
		$user = get_entity($guid);
		if (!elgg_instanceof($user))
			continue;

		$icon = elgg_view('output/img', array(
			'src' => $user->getIconURL('tiny'),
			'width' => 16,
			'height' => 16
		));
		$name = ($user->guid == elgg_get_logged_in_user_guid()) ? elgg_echo('hj:inbox:you') : $user->name;
		$user_names[] = '<span class="inbox-conversation-user">' . $icon . $name . '</span>';
	}

	$conversation = implode('', $user_names);

	$count = elgg_get_entities_from_metadata(array(
		'types' => 'object',
		'subtypes' => 'messages',
		'owner_guid' => $entity->owner_guid,
		'metadata_name_value_pairs' => array(
			'name' => 'msgHash', 'value' => $entity->msgHash
		),
		'count' => true
	));

	$count_unread = elgg_get_entities_from_metadata(array(
		'types' => 'object',
		'subtypes' => 'messages',
		'owner_guid' => $entity->owner_guid,
		'metadata_name_value_pairs' => array(
			array('name' => 'msgHash', 'value' => $entity->msgHash),
			array('name' => 'readYet', 'value' => 1, 'operand' => '!=')
		),
		'count' => true
	));
	if (!elgg_in_context('inbox-sent')) {
		$thread = elgg_view('output/url', array(
			'text' => $count,
			'href' => $entity->getURL(),
			'class' => 'inbox-message-thread-count',
			'title' => elgg_echo('hj:inbox:thread', array($count))
		));

		if ($count_unread <= 0) {
			$unread_class = ' hidden';
		} else {
			$class = "$class inbox-message-thread-unread";
		}

		$thread_unread = elgg_view('output/url', array(
			'text' => $count_unread,
			'title' => elgg_echo('hj:inbox:thread:unread', array($count_unread)),
			'href' => $entity->getURL(),
			'class' => 'inbox-message-thread-count inbox-message-thread-unread-count' . $unread_class,
		));
	}

	$menu = elgg_view_menu('entity', array(
		'entity' => $entity,
		'counter' => array(
			'count' => $count,
			'unread' => $count_unread
		)
	));

	$href = $entity->getURL();
	$body = <<<__MSG
	<div class="inbox-message-conversation">$conversation</div>
	<div class="inbox-message-thread">$thread_unread$thread</div>
	<div class="inbox-message-title">$title$tags</div>
	<div class="inbox-message-attachments">$attachments_summary</div>
	<div class="inbox-message-menu">$menu</div>
__MSG;
} else {

	if (!$entity->readYet) {
		$class = "$class inbox-message-thread-unread";
		$full = true;
	}

	$from = get_entity($entity->fromId);
	$from_icon = elgg_view_entity_icon($from, 'small');
	$from_name = elgg_view('output/url', array(
		'text' => $from->name,
		'href' => "messages/compose?send_to=$from->guid",
	));
	$header = '<div class="inbox-message-from">' . $from_name . '</div>';

	$href = elgg_normalize_url("ajax/view/framework/inbox/thread/message?guid=$entity->guid");

	if ($full) {
		$entity->readYet = true;
		if ($attachments_list) {
			$attachments_list = elgg_view_module('messages-attachments', elgg_echo('messages:attachments'), $attachments_list);
		}
		$body = $desc . $attachments_list;
		$class = "$class inbox-thread-message-full";
	} else {
		$body = $summary . $attachments_summary;
		$class = "$class inbox-thread-message-summary";
	}

	$menu = elgg_view_menu('entity', array(
		'entity' => $entity,
		'full_view' => $full
	));

	$body = elgg_view_image_block($from_icon, $header . $body, array(
		'class' => 'inbox-message-header',
		'image_alt' => $menu
	));
}

echo "<article class=\"$class\" data-href=\"$href\" data-guid=\"$entity->guid\">";
echo $body;
echo "</artcile>";