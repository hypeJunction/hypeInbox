<?php

namespace hypeJunction\Inbox;

$threaded = get_input('threaded', false);
$guids = get_input('guids', array());
if (!is_array($guids) || !count($guids)) {
	register_error(elgg_echo('inbox:delete:error'));
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
		if (!$message->isPersistent()) {
			if (!$message->delete(true, $threaded)) {
				$error++;
			} else {
				$success++;
			}
		} else {
			$persistent++;
		}
	}
}

if ($count > 1) {
	$msg[] = elgg_echo('inbox:delete:success', array($success));
	if ($notfound > 0) {
		$msg[] = elgg_echo('hj:approve:error:notfound', array($notfound));
	}
	if ($persistent > 0) {
		$msg[] = elgg_echo('hj:approve:error:canedit', array($persistent));
	}
	if ($error > 0) {
		$msg[] = elgg_echo('hj:approve:error:unknown', array($error));
	}
} else if ($success) {
	$msg[] = elgg_echo('inbox:delete:success:single');
} else {
	$msg[] = elgg_echo('inbox:delete:error');
}

$msg = implode('<br />', $msg);
if ($success < $count) {
	register_error($msg);
} else {
	system_message($msg);
}
