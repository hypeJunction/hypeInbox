<?php

$limit = get_input('limit', 20);
$offset = get_input('offset', 0);

$ha = access_get_show_hidden_status();
access_show_hidden_entities(true);

$name_id = get_metastring_id('msgHash');
if (!$name_id) {
	$name_id = add_metastring('msgHash');
}

$dbprefix = elgg_get_config('dbprefix');
$messages = elgg_get_entities(array(
	'types' => 'object',
	'subtypes' => array('messages'),
	'wheres' => array(
		"NOT EXISTS (SELECT 1 FROM {$dbprefix}metadata md WHERE md.entity_guid = e.guid
            AND md.name_id = $name_id)"
	),
	'order_by' => 'e.guid ASC',
	'limit' => $limit,
	'offset' => $offset,
		));

		error_log(print_r($messages, true));
		
if (!$messages) {
	print json_encode(array('complete' => true));
	forward(REFERER);
}

foreach ($messages as $msg) {

	$to = $msg->toId;
	$from = $msg->fromId;

	if (!is_array($to)) {
		$to = array($to);
	}
	if (!is_array($from)) {
		$from = array($from);
	}

	$user_guids = array_merge($to, $from);
	sort($user_guids);

	$title = strtolower($msg->title);
	$title = trim(str_replace('re:', '', $title));

	$hash = sha1(implode(':', $user_guids) . $title);

	$msg->msgHash = $hash;
	if ($msg->fromId == elgg_get_site_entity()->guid) {
		$msg->msgType = HYPEINBOX_NOTIFICATION;
	} else {
		$msg->msgType = HYPEINBOX_PRIVATE;
	}
	elgg_log("Updated message $msg->guid (hash : $msg->msgHash; type : $msg->msgType");

	if (!$msg->save()) {
		$offset++;
	}
}

access_show_hidden_entities($ha);

print json_encode(array(
	'offset' => $offset
));
forward(REFERER);