<?php

namespace hypeJunction\Inbox;

use ElggUser;
use InvalidArgumentException;

class Inbox {

	/**
	 * User that "owns" the inbox
	 * @var ElggUser 
	 */
	protected $owner;

	/**
	 * Message type
	 * @var string 
	 */
	protected $msgType;

	/**
	 * Read status
	 * @var boolean 
	 */
	protected $readYet;

	/**
	 * Flag to display messages as threads
	 * @var bool 
	 */
	protected $threaded;

	/**
	 * Flag to only display sent or received messages
	 * @var bool
	 */
	protected $direction;

	/**
	 * Database prefix
	 * @var string
	 */
	private $dbprefix;

	/**
	 * Cached metastring ids
	 * @var array
	 */
	private static $metamap;

	const DIRECTION_SENT = 'sent';
	const DIRECTION_RECEIVED = 'received';
	const DIRECTION_ALL = 'all';
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->dbprefix = elgg_get_config('dbprefix');
	}

	/**
	 * Get a count of unread messages
	 * 
	 * @param ElggUser $user    Recipient
	 * @param string   $msgType Message type
	 * @param array    $options Additional options to pass to the getter
	 * @return int
	 */
	public static function countUnread(ElggUser $user, $msgType = '', array $options = array()) {
		$instance = new Inbox();
		$instance->setOwner($user)->setMessageType($msgType)->setReadStatus(false);
		return $instance->getCount($options);
	}

	/**
	 * Set inbox owner
	 * 
	 * @param ElggUser $user Owning user
	 * @return Inbox
	 * @throws InvalidArgumentException
	 */
	public function setOwner(ElggUser $user) {
		if (!$user instanceof ElggUser) {
			throw new InvalidArgumentException(get_class() . '::setOwner() expects an instanceof ElggUser');
		}
		$this->owner = $user;
		return $this;
	}

	/**
	 * Get inbox owner
	 * @return ElgUser
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * Set message type
	 * 
	 * @param string $msgType Message type
	 * @return Inbox
	 */
	public function setMessageType($msgType = '') {
		$this->msgType = $msgType;
		return $this;
	}

	/**
	 * Get message type
	 * @return string
	 */
	public function getMessageType() {
		return $this->msgType;
	}

	/**
	 * Set read / not read status
	 * 
	 * @param boolean $readYet True for read, false for not read
	 * @return Inbox
	 */
	public function setReadStatus($readYet = null) {
		if (!is_null($readYet)) {
			$this->readYet = (bool) $readYet;
		}
		return $this;
	}

	/**
	 * Get read status
	 * @return bool
	 */
	public function getReadStatus() {
		return $this->readYet;
	}

	/**
	 * Sets message display to threaded
	 * 
	 * @param bool $threaded Flag
	 * @return Inbox;
	 */
	public function displayThreaded($threaded = true) {
		$this->threaded = (bool) $threaded;
		return $this;
	}

	/**
	 * Check if display is threaded
	 * @return bool
	 */
	public function isDisplayThreaded() {
		return (bool) $this->threaded;
	}

	/**
	 * Set types of messages to display
	 *
	 * @param string $direction 'sent', 'received' or 'all'
	 * @return Inbox
	 */
	public function setDirection($direction = '') {
		if (in_array($direction, array(self::DIRECTION_ALL, self::DIRECTION_SENT, self::DIRECTION_RECEIVED))) {
			$this->direction = $direction;
		}
		return $this;
	}

	/**
	 * Returns set message direction or 'all'
	 * @return string
	 */
	public function getDirection() {
		return ($this->direction) ?: self::DIRECTION_ALL;
	}

	/**
	 * Get messages
	 * 
	 * @param array $options Additional options to pass to the getter
	 * @return Message[]
	 */
	public function getMessages(array $options = array()) {
		$options = $this->getFilterOptions($options);
		return elgg_get_entities($options);
	}

	/**
	 * Get count of messages
	 * 
	 * @param array $options Additional options to pass to the getter
	 * @return int
	 */
	public function getCount(array $options = array()) {
		if ($this->threaded) {
			$options = $this->getFilterOptions($options);
			$options['selects'][] = 'COUNT(DISTINCT md_msgHash.value_id) AS total';
			unset($options['group_by']);
			$options['limit'] = 1;
			$options['callback'] = array($this, 'getCountCallback');
			$messages = elgg_get_entities($options);
			return $messages[0]->total;
		} else {
			$options['count'] = true;
			return $this->getMessages($options);
		}
	}
	
	public static function getCountCallback($row) {
		return $row;
	}

	/**
	 * Filter getter options
	 * 
	 * @param array $options Default options
	 * @return array
	 */
	public function getFilterOptions($options = array()) {

		if (!is_array($options)) {
			$options = array();
		}

		$options['types'] = 'object';
		$options['subtypes'] = Message::SUBTYPE;
		$options['owner_guids'] = sanitize_int($this->owner->guid);

		$metastrings = array($this->owner->guid, 'readYet', $this->readYet, 'msgType', $this->msgType, 'msgHash', 'toId', 'fromId');
		$map = $this->getMetaMap($metastrings);

		$options['joins']['md_msgHash'] = "JOIN {$this->dbprefix}metadata md_msgHash ON e.guid = md_msgHash.entity_guid";
		$options['wheres'][] = "md_msgHash.name_id = {$map['msgHash']}";

		$direction = $this->getDirection();
		if ($direction == self::DIRECTION_SENT) {
			$options['joins']['md_fromId'] = "JOIN {$this->dbprefix}metadata md_fromId ON e.guid = md_fromId.entity_guid";
			$options['wheres'][] = "md_fromId.name_id = {$map['fromId']} AND md_fromId.value_id = {$map[$this->owner->guid]}";
		} else if ($direction == self::DIRECTION_RECEIVED) {
			$options['joins']['md_toId'] = "JOIN {$this->dbprefix}metadata md_toId ON e.guid = md_toId.entity_guid";
			$options['wheres'][] = "md_toId.name_id = {$map['toId']} AND md_toId.value_id = {$map[$this->owner->guid]}";
		} else if ($this->threaded) {
			$options['selects'][] = 'MAX(e.guid) as lastMsg';
			$options['group_by'] = "md_msgHash.value_id";
			$options['order_by'] = 'MAX(e.guid) DESC';
		}
		
		if ($this->msgType) {
			$options['joins']['md_msgType'] = "JOIN {$this->dbprefix}metadata md_msgType ON e.guid = md_msgType.entity_guid";
			$options['wheres'][] = "md_msgType.name_id = {$map['msgType']} AND md_msgType.value_id = {$map[$this->msgType]}";
		}

		if (!is_null($this->readYet)) {
			$options['joins']['md_readYet'] = "JOIN {$this->dbprefix}metadata md_readYet ON e.guid = md_readYet.entity_guid";
			$options['wheres'][] = "md_readYet.name_id = {$map['readYet']} AND md_readYet.value_id = {$map[$this->readYet]}";
		}

		return $options;
	}

	/**
	 * Metastring ID mapping
	 * 
	 * @param array $metastrings An array of metastrings
	 * @return array
	 */
	private static function getMetaMap($metastrings = array()) {
		$map = array();
		foreach ($metastrings as $metastring) {
			if (isset(self::$matamap) && in_array(self::$metamap[$metastring])) {
				$map[$metastring] = self::$metamap[$metastring];
			} else {
				$id = elgg_get_metastring_id($metastring);
				self::$metamap[$metastring] = $map[$metastring] = $id;
			}
		}
		return $map;
	}

}
