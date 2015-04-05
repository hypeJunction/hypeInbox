<?php

namespace hypeJunction\Inbox\Controllers\Actions;

class ActionResult {
	
	protected $forward;
	protected $messages = array();
	protected $errors = array();

	public $data;
	
	public function __construct() {
		$this->setForwardURL();
	}

	public function setForwardURL($url = REFERER) {
		$this->forward = $url;
	}

	public function getForwardURL() {
		return $this->forward;
	}
	
	public function addError($error = '') {
		if ($error) {
			$this->errors[] = $error;
		}
		return $this;
	}

	public function addMessage($message = '') {
		if ($message) {
			$this->messages[] = $message;
		}
		return $this;
	}

	public function getErrors() {
		return $this->errors;
	}

	public function getMessages() {
		return $this->messages;
	}
}
