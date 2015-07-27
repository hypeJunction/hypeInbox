<?php

namespace hypeJunction\Inbox\Actions;

use hypeJunction\Controllers\Action;
use hypeJunction\Exceptions\ActionValidationException;
use hypeJunction\Inbox\Message;

/**
 * @property bool  $threaded
 * @property int[] $guids
 */
class MarkAsRead extends Action {

	/**
	 * {@inheritdoc}
	 */
	public function validate() {
		if (!is_array($this->guids) || !count($this->guids)) {
			throw new ActionValidationException(elgg_echo('inbox:markread:error'));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {

		$count = count($this->guids);
		$success = $notfound = 0;

		foreach ($this->guids as $guid) {
			$message = get_entity($guid);
			if (!$message instanceof Message) {
				$notfound++;
				continue;
			}
			$message->markRead($this->threaded);
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
			$this->result->addError($msg);
		} else {
			$this->result->addMessage($msg);
		}
	}

}
