<?php

use hypeJunction\Inbox\Message;

$guids = get_input('guids', []);
$threaded = get_input('threaded');

if (!is_array($guids) || empty($guids)) {
	register_error(elgg_echo('inbox:delete:error'));
	forward(REFERRER);
}

$count = count($guids);
$error = $success = $persistent = $notfound = 0;

foreach ($guids as $guid) {
	$message = get_entity($guid);
	if (!$message instanceof Message) {
		$notfound++;
		continue;
	}
	if ($message->isPersistent()) {
		$persistent++;
		continue;
	}
	if (!$message->delete(true, $threaded)) {
		$error++;
	} else {
		$success++;
	}
}

if ($count > 1) {
	$msg[] = elgg_echo('inbox:delete:success', array($success));
	if ($notfound > 0) {
		$msg[] = elgg_echo('inbox:error:notfound', array($notfound));
	}
	if ($persistent > 0) {
		$msg[] = elgg_echo('inbox:error:canedit', array($persistent));
	}
	if ($error > 0) {
		$msg[] = elgg_echo('inbox:error:unknown', array($error));
	}
	$forward = REFERRER;
} else if ($success) {
	$msg[] = elgg_echo('inbox:delete:success:single');
	$forward = 'messages';
} else {
	$msg[] = elgg_echo('inbox:delete:error');
	$forward = REFERRER;
}

$msg = implode('<br />', $msg);
if ($success < $count) {
	register_error($msg);
} else {
	system_message($msg);
}

forward($forward);
