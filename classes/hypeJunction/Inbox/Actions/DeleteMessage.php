<?php

namespace hypeJunction\Inbox\Actions;

use hypeJunction\Controllers\Action;
use hypeJunction\Exceptions\ActionValidationException;
use hypeJunction\Inbox\Message;

/**
 * @property bool  $threaded
 * @property int[] $guids
 */
class DeleteMessage extends Action {

	/**
	 * {@inheritdoc}
	 */
	public function validate() {
		if (!is_array($this->guids) || !count($this->guids)) {
			throw new ActionValidationException(elgg_echo('inbox:delete:error'));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {

		$count = count($this->guids);
		$error = $success = $persistent = $notfound = 0;

		foreach ($this->guids as $guid) {
			$message = get_entity($guid);
			if (!$message instanceof Message) {
				$notfound++;
				continue;
			}
			if ($message->isPersistent()) {
				$persistent++;
				continue;
			}
			if (!$message->delete(true, $this->threaded)) {
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
			$this->result->addError($msg);
		} else {
			$this->result->addMessage($msg);
		}
		$this->result->setForwardURL($forward);
	}

}
