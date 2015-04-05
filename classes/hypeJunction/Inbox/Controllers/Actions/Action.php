<?php

namespace hypeJunction\Inbox\Controllers\Actions;

use hypeJunction\Inbox\Exceptions\ActionValidationException;

abstract class Action {

	const CLASSNAME = __CLASS__;
	const ACCESS_PUBLIC = 'public';
	const ACCESS_LOGGED_IN = 'logged_in';
	const ACCESS_ADMIN = 'admin';
	/**
	 * @var ActionResult
	 */
	protected $result;

	/**
	 * Constructor
	 *
	 * @param ActionResult $result Action result
	 */
	public function __construct(ActionResult $result = null) {
		$this->result = ($result) ? : new ActionResult;
	}

	/**
	 * Returns the result object
	 * @return ActionResult
	 */
	public function getResult() {
		$this->result->data = get_object_vars($this);
		return $this->result;
	}

	/**
	 * Returns registered action name
	 * @return string
	 */
	abstract function getName();

	/**
	 * Validates user input
	 * @throws ActionValidationException
	 * @return bool
	 */
	abstract function validate();

	/**
	 * Executes an action
	 * @return void
	 */
	abstract function execute();

}
