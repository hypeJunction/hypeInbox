<?php

namespace hypeJunction\Inbox;

use ElggUser;
use stdClass;

class Policy {

	private $dbprefix;

	/**
	 * Config object
	 * @var Config 
	 */
	protected $config;

	/**
	 * Sender definition
	 * @var stdClass 
	 */
	protected $sender;

	/**
	 * Recipient definition
	 * @var stdClass 
	 */
	protected $recipient;
	
	/**
	 * Relationship name that exists between sender and recipient
	 * @var string 
	 */
	protected $relationship;
	
	/**
	 * Is relationship between sender and recipient inverse
	 * @var bool 
	 */
	protected $inverse_relationship;
	
	/**
	 * Relationship name that must exist between sender and recipient and a shared group
	 * @var string 
	 */
	protected $group_relationship;
	
	/**
	 * Iterate table aliases
	 * @var int 
	 */
	static $iterator;

	/**
	 * Constructor
	 * @param array $policy An array of policy clauses
	 */
	public function __construct(array $policy = array()) {
		$this->dbprefix = elgg_get_config('dbprefix');

		$policy = $this->normalizePolicy($policy);
		$this->setSenderType($policy['sender']);
		$this->setRecipientType($policy['recipient']);
		$this->relationship = sanitize_string($policy['relationship']);
		$this->inverse_relationship = (bool) $policy['inverse_relationship'];
		$this->group_relationship = sanitize_string($policy['group_relationship']);
	}

	/**
	 * Normalize policy clauses
	 * 
	 * @param array $policy Policy clauses
	 * @return array
	 */
	public function normalizePolicy(array $policy = array()) {

		$defaults = array(
			'sender' => 'all',
			'recipient' => 'all',
			'relationship' => false,
			'inverse_relationhship' => false,
			'group_relationship' => false,
		);

		return array_merge($defaults, $policy);
	}

	/**
	 * Sets sender type and callbacks
	 * 
	 * @param string $type
	 * @return Policy
	 */
	public function setSenderType($type) {

		$usertypes = hypeInbox()->config->getUserTypes();

		$this->sender = new stdClass();
		$this->sender->type = $type;
		$this->sender->validator = elgg_extract('validator', $usertypes[$type]);
		$this->sender->getter = elgg_extract('getter', $usertypes[$type]);
		return $this;
	}

	/**
	 * Returns sender type
	 * @return string
	 */
	public function getSenderType() {
		return $this->sender->type;
	}

	/**
	 * Validate that user is of type defined as sender
	 * 
	 * @param ElggUser $user Sender
	 * @return bool
	 */
	public function validateSenderType(ElggUser $user) {

		if ($this->getSenderType() == 'all') {
			return true;
		}

		$validator = $this->sender->validator;
		if (!$validator || !is_callable($validator)) {
			return false;
		}

		return call_user_func($validator, $user, $this->getSenderType());
	}

	/**
	 * Sets recipient type and callbacks
	 * 
	 * @param string $type
	 * @return Policy
	 */
	public function setRecipientType($type) {

		$usertypes = hypeInbox()->config->getUserTypes();

		$this->recipient = new stdClass();
		$this->recipient->type = $type;
		$this->recipient->validator = elgg_extract('validator', $usertypes[$type]);
		$this->recipient->getter = elgg_extract('getter', $usertypes[$type]);
		return $this;
	}

	/**
	 * Returns recipient type
	 * @return string
	 */
	public function getRecipientType() {
		return $this->recipient->type;
	}

	/**
	 * Validate that user is of type defined as recipient
	 * 
	 * @param ElggUser $user Recipient
	 * @return bool
	 */
	public function validateRecipientType(ElggUser $user) {

		if ($this->getRecipientType() == 'all') {
			return true;
		}

		$validator = $this->recipient->validator;
		if (!$validator || !is_callable($validator)) {
			return false;
		}

		return call_user_func($validator, $user, $this->getRecipientType());
	}

	/**
	 * Returns clauses to filter recipient users by type
	 * @return array
	 */
	public function getRecipientClauses() {
		$clauses = array(
			'join' => '',
			'where' => '',
		);

		if ($this->getRecipientType() == 'all') {
			return $clauses;
		}
		
		$getter = $this->recipient->getter;
		if (!$getter || !is_callable($getter)) {
			return $clauses;
		}

		$options = call_user_func($getter, $this->getRecipientType());
		if (isset($options['joins'])) {
			if (is_array($options['joins'])) {
				$clauses['join'] = implode(' AND ', $options['joins']);
			} else {
				$clauses['join'] = $options['joins'];
			}
		}
		if (isset($options['wheres'])) {
			if (is_array($options['wheres'])) {
				$clauses['where'] = implode(' AND ', $options['wheres']);
			} else {
				$clauses['where'] = $options['wheres'];
			}
		}
		return $clauses;
	}

	/**
	 * Returns relationships clauses to validate the relationship to the sender
	 * 
	 * @param ElggUser $sender Sender
	 * @return array
	 */
	public function getRelationshipClauses(ElggUser $sender) {

		$alias = "rel" . self::$iterator++;

		$clauses = array(
			'join' => '',
			'where' => '',
		);
		if (!$this->relationship || $this->relationship == 'all') {
			return $clauses;
		}

		$guid = sanitize_int($sender->guid);

		if (!$this->inverse_relationship) {
			$clauses['join'] = "JOIN {$this->dbprefix}entity_relationships $alias ON e.guid = $alias.guid_two";
			$clauses['where'] = "$alias.guid_one = $guid AND $alias.relationship = '$this->relationship'";
		} else {
			$clauses['join'] = "JOIN {$this->dbprefix}entity_relationships $alias ON e.guid = $alias.guid_one";
			$clauses['where'] = "$alias.guid_two = $guid AND $alias.relationship = '$this->relationship'";
		}

		return $clauses;
	}

	/**
	 * Returns relationships clauses to validate the relationship to the recipient via group
	 * 
	 * @param ElggUser $sender Sender
	 * @return array
	 */
	public function getGroupRelationshipClauses(ElggUser $sender) {

		$alias = "gerel" . self::$iterator++;

		$clauses = array(
			'join' => '',
			'where' => '',
		);
		if (!$this->group_relationship || $this->group_relationship == 'all') {
			return $clauses;
		}

		$guid = sanitize_int($sender->guid);

		$clauses['join'] = "JOIN {$this->dbprefix}entity_relationships $alias ON $alias.guid_one = $guid
			AND $alias.relationship = '$this->group_relationship'";
		$clauses['where'] = "$alias.guid_two IN (SELECT guid_two FROM {$this->dbprefix}entity_relationships WHERE guid_one = e.guid 
			AND relationship = '$this->group_relationship')";

		return $clauses;
	}

}
