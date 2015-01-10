<?php

namespace hypeJunction\Inbox;

$threaded = get_input('threaded', false);
$guids = get_input('guids', array());
if (!is_array($guids) || !count($guids)) {
	register_error(elgg_echo('inbox:markunread:error'));
	forward(REFERER);
}

$count = count($guids);
$error = $success = $persistent = $notfound = 0;

if (!empty($guids)) {
	foreach ($guids as $guid) {
		$message = get_entity($guid);
		if (!$message instanceof Message) {
			$notfound++;
			continue;
		}
		$message->markUnread($threaded);
		$success++;
	}
}

if ($count > 1) {
	$msg[] = elgg_echo('inbox:markunread:success', array($success));
	if ($notfound > 0) {
		$msg[] = elgg_echo('inbox:error:notfound', array($notfound));
	}
} else if ($success) {
	$msg[] = elgg_echo('inbox:markunread:success:single');
} else {
	$msg[] = elgg_echo('inbox:markunread:error');
}

$msg = implode('<br />', $msg);
if ($success < $count) {
	register_error($msg);
} else {
	system_message($msg);
}

forward('messages');