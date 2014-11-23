<?php

namespace hypeJunction\Inbox;

class Ruleset {

	/**
	 * Message type this ruleset applies to
	 * @var string 
	 */
	protected $message_type;

	/**
	 * An array of policies
	 * @var Policy[]
	 */
	protected $policies = array();

	/**
	 * An array of English labels
	 * @var array
	 */
	protected $labels;

	/**
	 * Flag to allow multiple recipients
	 * @var bool
	 */
	protected $multiple;

	/**
	 * Flag to disable delete
	 * @var bool 
	 */
	protected $persistent;

	/**
	 * Flag to allow attachments
	 * @var bool 
	 */
	protected $attachments;

	/**
	 * Flag to disable subject line
	 * @var type 
	 */
	protected $no_subject;

	/**
	 * Constructor
	 *
	 * @param string $mesage_type Message type this set applies to
	 * @param array  $ruleset     A set of rules
	 */
	public function __construct($message_type, array $ruleset = array()) {

		$ruleset = $this->normalizeRuleset($ruleset);
		$this->message_type = $message_type;
		$this->setPolicies($ruleset['policy']);
		$this->labels = $ruleset['labels'];
		$this->multiple = $ruleset['multiple'];
		$this->persistent = $ruleset['persistent'];
		$this->attachments = $ruleset['attachments'];
		$this->no_subject = $ruleset['no_subject'];
	}

	/**
	 * Normalizes rule set
	 * 
	 * @param array $ruleset A set of rules
	 * @return array
	 */
	public function normalizeRuleset(array $ruleset = array()) {
		$defaults = array(
			'policy' => array(),
			'labels' => array(),
			'multiple' => false,
			'persistent' => false,
			'attachments' => false,
			'no_subject' => false,
		);
		return array_merge($defaults, $ruleset);
	}

	/**
	 * Constructs policy objects from arrays
	 * 
	 * @param array $policies An array of policy clauses
	 * @return Ruleset
	 */
	public function setPolicies(array $policies = array()) {
		foreach ($policies as $policy) {
			$this->policies[] = new Policy($policy);
		}
		return $this;
	}
	
	/**
	 * Returns policies
	 * @return Policy[]
	 */
	public function getPolicies() {
		return $this->policies;
	}

	/**
	 * Checks if message type is persistent
	 * @return bool
	 */
	public function isPersistent() {
		return (bool) $this->persistent;
	}
	
	/**
	 * Check if multiple recipients are allowed
	 * @return bool
	 */
	public function allowsMultipleRecipients() {
		return (bool) $this->multiple;
	}
	
	/**
	 * Check if attachments are allowed
	 * @return bool
	 */
	public function allowsAttachments() {
		return (bool) $this->attachments;
	}
	
	/**
	 * Check if subject line is enabled
	 * @return bool
	 */
	public function hasSubject() {
		return !$this->no_subject;
	}
	
	/**
	 * Get a singular label, or raw message key
	 *
	 * @param string|false $language Language code, or false to return raw message key
	 * @return string
	 */
	public function getSingularLabel($language = 'en') {
		$key = "item:object:message:$this->message_type:singular";
		if ($language == 'en') {
			return $this->labels['singular'];
		} else if ($language) {
			return elgg_echo($key, array(), $language);
		}
		return $key;
	}
	
	/**
	 * Get a plural label, or raw message key
	 *
	 * @param string|false $language Language code, or false to return raw message key
	 * @return string
	 */
	public function getPluralLabel($language = 'en') {
		$key = "item:object:message:$this->message_type:plural";
		if ($language == 'en') {
			return $this->labels['plural'];
		} else if ($language) {
			return elgg_echo($key, array(), $language);
		}
		return $key;
	}
}
