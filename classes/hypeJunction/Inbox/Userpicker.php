<?php

namespace hypeJunction\Inbox;

use ElggEntity;

class Userpicker {

	private $dbprefix;
	protected $message_type;
	protected $sender;
	protected $policies;
	protected $options;

	/**
	 * Constructor
	 * 
	 * @param string     $message_type Message type
	 * @param ElggEntity $sender       Sender
	 */
	public function __construct($message_type = '', $sender = null) {
		$this->setMessageType($message_type);
		$this->setSender($sender);
		$this->dbprefix = elgg_get_config('dbprefix');
	}

	/**
	 * Sets message type
	 * 
	 * @param string $message_type Message type
	 * @return Userpicker
	 */
	public function setMessageType($message_type = '') {
		if (!$message_type) {
			$message_type = Message::TYPE_PRIVATE;
		}
		$this->message_type = $message_type;
		return $this;
	}

	/**
	 * Returns set message type
	 * @return string
	 */
	public function getMessageType() {
		return $this->message_type;
	}

	/**
	 * Sets sender entity
	 * Defaults to logged in user, or site if there isn't one
	 * 
	 * @param ElggEntity $sender Sender entity
	 * @return Userpicker
	 */
	public function setSender($sender = null) {
		if (is_null($sender)) {
			$sender = elgg_get_logged_in_user_entity();
		}
		if (!$sender) {
			$sender = elgg_get_site_entity();
		}
		$this->sender = $sender;
		return $this;
	}

	/**
	 * Returns set sender entity
	 * @return ElggEntity
	 */
	public function getSender() {
		return $this->sender;
	}

	/**
	 * Returns ruleset object
	 * @return Ruleset
	 */
	public function getRuleset() {
		if (!isset($this->ruleset)) {
			$this->ruleset = hypeInbox()->config->getRuleset($this->getMessageType());
		}
		return $this->ruleset;
	}

	/**
	 * Returns getter options to retrieve potential recipients in relation to this sender for the given message type
	 * 
	 * @param array $options Default options
	 * @return array
	 */
	public function getFilterOptions(array $options = array()) {

		$policies = $this->getRuleset()->getPolicies();
		$sender = $this->getSender();

		$options['joins'][] = "JOIN {$this->dbprefix}users_entity ue ON e.guid = ue.guid";
		$options['order_by'] = "ue.name ASC";

		$wheres = array();

		foreach ($policies as $policy) {

			if (!$policy->validateSenderType($sender)) {
				continue;
			}

			$where = array();

			$clauses = $policy->getRelationshipClauses($sender);
			$options['joins'][] = $clauses['join'];
			$where[] = $clauses['where'];


			$clauses = $policy->getGroupRelationshipClauses($sender);
			$options['joins'][] = $clauses['join'];
			$where[] = $clauses['where'];

			$clauses = $policy->getRecipientClauses();
			$options['joins'][] = $clauses['join'];
			$where[] = $clauses['where'];

			$where = array_unique(array_filter($where));
			$where = implode(' AND ', $where);
			if ($where) {
				$wheres[] = "($where)";
			}
		}

		if (count($wheres)) {
			$wheres = implode(' OR ', array_filter($wheres));
			if ($wheres) {
				$options['wheres'][] = "($wheres)";
			}
		}

		foreach ($options as $key => $option) {
			if (is_array($option)) {
				$options[$key] = array_unique($option);
			}
		}

		return $options;
	}

}
