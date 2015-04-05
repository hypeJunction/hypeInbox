<?php

namespace hypeJunction\Inbox\Exceptions;

use Exception as PHPException;

class Exception extends PHPException{

	public function __construct($message, $code, $previous) {
		parent::__construct($message, $code, $previous);
		elgg_log($message, 'ERROR');
	}
}
