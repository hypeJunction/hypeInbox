<?php

use hypeJunction\Inbox\Message;

$limit = get_input('limit', 20);
$offset = get_input('offset', 0);

$ha = access_get_show_hidden_status();
access_show_hidden_entities(true);

$messages = array();
$batch = hypeInbox()->model->getUnhashedMessages(array(
	'limit' => $limit,
	'offset' => $offset,
		));

foreach ($batch as $message) {
	$messages[] = $message;
}

if (empty($messages)) {
	print json_encode(array('complete' => true));
	forward(REFERER);
}

$site = elgg_get_site_entity();

foreach ($messages as $msg) {

	if (!$msg instanceof Message) {
		continue;
	}
	
	$msg->msgHash = $msg->calcHash();

	if ($msg->fromId == $site->guid) {
		// if sent by site, qualify as a notification
		$msg->msgType = Message::TYPE_NOTIFICATION;
	} else {
		$msg->msgType = Message::TYPE_PRIVATE;
	}

	elgg_log("Updated message $msg->guid (hash : $msg->msgHash; type : $msg->msgType");

	if (!$msg->save()) {
		$offset++;
	}
}

print json_encode(array(
	'offset' => $offset
));

access_show_hidden_entities($ha);

forward(REFERER);
