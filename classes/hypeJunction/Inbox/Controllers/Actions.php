<?php

namespace hypeJunction\Inbox\Controllers;

use hypeJunction\Inbox\Config;
use hypeJunction\Inbox\Controllers\Actions\Action;
use hypeJunction\Inbox\Controllers\Actions\ActionResult;
use hypeJunction\Inbox\Exceptions\ActionValidationException;
use hypeJunction\Inbox\Exceptions\Exception;
use hypeJunction\Inbox\Exceptions\InvalidEntityException;
use hypeJunction\Inbox\Exceptions\PermissionsException;
use hypeJunction\Inbox\Message;
use hypeJunction\Inbox\Models\Model;
use hypeJunction\Inbox\Services\Router;

/**
 * Actions service
 */
class Actions {

	private $config;
	private $router;
	private $model;

	/**
	 * Constructor
	 *
	 * @param Config   $config     Config
	 * @param Router   $router     Router
	 * @param Model    $model      Model
	 */
	public function __construct(Config $config, Router $router, Model $model) {
		$this->config = $config;
		$this->router = $router;
		$this->model = $model;
	}

	/**
	 * Performs tasks on system init
	 * @return void
	 */
	public function init() {

		$path = $this->config->getPath() . 'actions/';

		elgg_register_action("hypeInbox/settings/save", $path . 'settings/save.php', 'admin');
		elgg_register_action('inbox/admin/import', $path . 'admin/import.php', 'admin');

		elgg_register_action('messages/send', $path . 'messages/send.php');
		elgg_register_action('messages/delete', $path . 'messages/delete.php');
		elgg_register_action('messages/markread', $path . 'messages/markread.php');
		elgg_register_action('messages/markunread', $path . 'messages/markunread.php');
	}

	/**
	 * Executes an action
	 * Triggers 'action:after', $name hook that allows you to filter the Result object
	 * 
	 * @param Action $action   Action
	 * @param bool   $feedback Display errors and messages
	 * @return ActionResult
	 */
	public function execute(Action $action, $feedback = true) {

		$name = $action->getName();
		elgg_make_sticky_form($name);

		$result = $action->getResult();

		try {
			if ($action->validate()) {
				$action->execute();
			}
			$result = $action->getResult();
		} catch (ActionValidationException $ex) {
			$result->addError(elgg_echo('categories:validation:error'));
		} catch (PermissionsException $ex) {
			$result->addError(elgg_echo('categories:permissions:error'));
		} catch (InvalidEntityException $ex) {
			$result->addError(elgg_echo('categories:entity:error'));
		} catch (Exception $ex) {
			$result->addError(elgg_echo('categories:action:error'));
		}

		$errors = $result->getErrors();
		$messages = $result->getMessages();
		if (empty($errors)) {
			elgg_clear_sticky_form($name);
		} else {
			$result->setForwardURL(REFERRER);
		}

		if ($feedback) {
			foreach ($errors as $error) {
				register_error($error);
			}
			foreach ($messages as $message) {
				system_message($message);
			}
		}

		return elgg_trigger_plugin_hook('action:after', $name, null, $result);
	}

	/**
	 * Send a message to specified recipients
	 *
	 * @param array $options An array of options
	 *   'sender'       => Sender guid or entity
	 *   'recipients'   => Recipient guid or entity, or an array of guids or entities
	 *   'subject'      => Message subject
	 *   'hash'         => Message hash
	 *   'body'         => Message body
	 *   'message_type' => Message type
	 *   'attachments'  => Entities to attach, or their guids
	 * @return Message|false Sent message or false on error
	 */
	public function sendMessage($options = array()) {
		$message = Message::factory($options);
		$guid = $message->send();
		return ($guid) ? get_entity($guid) : false;
	}

}
