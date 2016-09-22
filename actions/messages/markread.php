<?php

use hypeJunction\Inbox\Message;

$guids = get_input('guids', []);
$threaded = get_input('threaded');

if (!is_array($guids) || empty($guids)) {
	register_error(elgg_echo('inbox:markread:error'));
	forward(REFERRER);
}

$count = count($guids);
$success = $notfound = 0;

foreach ($guids as $guid) {
	$message = get_entity($guid);
	if (!$message instanceof Message) {
		$notfound++;
		continue;
	}
	$message->markRead($threaded);
	$success++;
}

if ($count > 1) {
	$msg[] = elgg_echo('inbox:markread:success', array($success));
	if ($notfound > 0) {
		$msg[] = elgg_echo('inbox:error:notfound', array($notfound));
	}
} else if ($success) {
	$msg[] = elgg_echo('inbox:markread:success:single');
} else {
	$msg[] = elgg_echo('inbox:markread:error');
}

$msg = implode('<br />', $msg);
if ($success < $count) {
	register_error($msg);
} else {
	system_message($msg);
}