<?php

namespace hypeJunction\Inbox;

use ElggEntity;
use ElggObject;
use ElggSite;

class Message extends ElggObject {

	const CLASSNAME = __CLASS__;
	
	const TYPE = 'object';
	const SUBTYPE = 'messages';
	const TYPE_NOTIFICATION = Config::TYPE_NOTIFICATION;
	const TYPE_PRIVATE = Config::TYPE_PRIVATE;

	/**
	 * Initialize object attributes
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * Create a message from options
	 * @param array $options An array of options
	 *   'sender'       => Sender guid or entity
	 *   'recipients'   => Recipient guid or entity, or an array of guids or entities
	 *   'subject'      => Message subject
	 *   'body'         => Message body
	 *   'hash'         => Message hash
	 *   'message_type' => Message type
	 *   'attachments'  => Entities to attach, or their guids
	 * @return Message
	 */
	public static function factory(array $options = array()) {

		$defaults = array(
			'sender' => 0,
			'recipients' => array(),
			'subject' => '',
			'body' => '',
			'hash' => '',
			'message_type' => Message::TYPE_PRIVATE,
			'attachments' => array(),
		);
		$options = array_merge($defaults, $options);

		$message = new Message;
		$message->setSubject(elgg_extract('subject', $options))
				->setBody(elgg_extract('body', $options))
				->setSender(elgg_extract('sender', $options))
				->setRecipients(elgg_extract('recipients', $options))
				->setMessageType(elgg_extract('message_type', $options))
				->setHash(elgg_extract('hash', $options))
				->setAttachments(elgg_extract('attachments', $options));

		return $message;
	}

	/**
	 * Returns a sorted array of user guids that participate in this message thread
	 * @return integer[]
	 */
	public function getParticipantGuids() {
		$group = $this->group()->add($this->toId)->add($this->fromId);
		return array_unique($group->guids());
	}

	/**
	 * Returns an array of all message participants
	 * @return ElggEntity[]
	 */
	public function getParticipants() {
		$group = $this->group()->add($this->getParticipantGuids());
		return $group->entities();
	}

	/**
	 * Set message recipients
	 * 
	 * @param mixed $recipients A guid or entity, or an array of guids or entities
	 * @return Message
	 */
	public function setRecipients($recipients = null) {
		if (isset($this->toId)) {
			return $this;
		}
		$this->toId = $this->group()->add($recipients)->guids();
		return $this;
	}

	/**
	 * Returns an array of message recipients
	 *
	 * @param bool $as_guid Return an array of guids
	 * @return ElggEntity[]|int[]
	 */
	public function getRecipients($as_guid = false) {
		$group = $this->group()->add($this->toId);
		return ($as_guid) ? $group->guids() : $group->entities();
	}

	/**
	 * Set sender of the message
	 * 
	 * @param ElggEntity|int $sender Sender guid or entity
	 * @return Message
	 */
	public function setSender($sender = null) {
		if ($this->fromId) {
			return $this;
		}
		if (!$sender) {
			$sender = $this->getDefaultSender();
		}
		$this->fromId = $this->group()->add($sender)->guids();
		return $this;
	}

	/**
	 * Returns a sender of the message
	 * @return ElggEntity
	 */
	public function getSender() {
		$sender = $this->group()->add($this->fromId)->entities();
		return (count($sender)) ? $sender[0] : $this->getDefaultSender();
	}

	/**
	 * Sets message subject
	 * 
	 * @param string $subject Subject
	 * @return Message
	 */
	public function setSubject($subject = '') {
		$this->title = $subject;
		return $this;
	}

	/**
	 * Returns message subject
	 * @return string
	 */
	public function getSubject() {
		return (isset($this->title)) ? $this->title : '';
	}

	/**
	 * Returns prefixed reply subject
	 * @return string
	 */
	public function getReplySubject() {
		$prefix = elgg_echo('inbox:reply:prefix');
		return $prefix . ' ' . trim(str_replace(strtolower($prefix), '', strtolower($this->getSubject())));
	}

	/**
	 * Returns title, or a generic string if not set
	 * @return string
	 */
	public function getDisplayName() {
		$subject = $this->getSubject();
		if (!$subject) {
			$recipients = $this->getRecipients();
			if (count($recipients) == 1) {
				return elgg_echo('inbox:conversation:user', array($recipients[0]->name));
			} else {
				return elgg_echo('inbox:conversation:group');
			}
		}
		return $subject;
	}

	/**
	 * Sets message body
	 * 
	 * @param string $body Message body
	 * @return Message
	 */
	public function setBody($body = '') {
		$this->description = $body;
		return $this;
	}

	/**
	 * Returns message body
	 * @return string
	 */
	public function getBody() {
		return (isset($this->description)) ? $this->description : '';
	}

	/**
	 * Returns a message hash calculated based on sender, recipients and message subject
	 * @return string
	 */
	public function calcHash() {
		$user_guids = $this->getParticipantGuids();
		$prefix = elgg_echo('inbox:reply:prefix');
		$subject = trim(str_replace(strtolower($prefix), '', strtolower($this->getSubject())));
		return sha1(implode(':', $user_guids) . $subject);
	}

	/**
	 * Sets message hash
	 * 
	 * @param string $hash Hash
	 * @return Message
	 */
	public function setHash($hash = '') {
		if (!isset($this->msgHash) && $hash) {
			$this->msgHash = $hash;
		}
		return $this;
	}

	/**
	 * Get thread hash
	 * @return string
	 */
	public function getHash() {
		$hash = $this->msgHash;
		if (!$hash) {
			$hash = $this->calcHash();
		}
		return $hash;
	}

	/**
	 * Sets message type
	 * Defaults to private
	 * 
	 * @param string $message_type Message type
	 * @return Message
	 */
	public function setMessageType($message_type = '') {
		if (!$message_type) {
			$message_type = Message::TYPE_PRIVATE;
		}
		$this->msgType = $message_type;
		return $this;
	}

	/**
	 * Returns message type
	 * @return string
	 */
	public function getMessageType() {
		return (isset($this->msgType)) ? $this->msgType : Message::TYPE_PRIVATE;
	}

	/**
	 * Checks if the message has been already read
	 * 
	 * @param bool $threaded Threaded display
	 * @return boolean
	 */
	public function isRead($threaded = false) {
		if (!$threaded) {
			return (bool) $this->readYet;
		}
		return $this->thread()->isRead();
	}

	/**
	 * Mark message as read
	 * 
	 * @param bool $threaded Mark all messages in a thread
	 * @return Message
	 */
	public function markRead($threaded = false) {
		if ($threaded) {
			$this->thread()->markRead();
		} else {
			$this->readYet = true;
		}
		return $this;
	}

	/**
	 * Mark message as unread
	 * 
	 * @param bool $threaded Mark all messages in a thread
	 * @return Message
	 */
	public function markUnread($threaded = false) {
		if ($threaded) {
			$this->thread()->markUnread();
		} else {
			$this->readYet = false;
		}
		return $this;
	}

	/**
	 * Checks if the message is persistent (i.e. can not be deleted)
	 * @return bool
	 */
	public function isPersistent() {
		return hypeInbox()->config->getRuleset($this->msgType)->isPersistent();
	}

	/**
	 * Adds attachments to volatile data and creates attached relationships
	 * once the message is successfully sent
	 *
	 * @param mixed $attachments An array of guids or entities
	 * @return Message
	 */
	public function setAttachments($attachments = array()) {
		$this->setVolatileData('attachments', $attachments);
		return $this;
	}

	/**
	 * Attaches an entity to a message
	 *
	 * @param mixed $attachments An array of guids or entities
	 * @return int Number of successful attachments
	 */
	public function attach() {
		if (!$this->guid) {
			return false;
		}
		$success = 0;
		$guids = $this->group()->add($this->getVolatileData('attachments'))->guids();
		foreach ($guids as $guid) {
			if ($this->addRelationship($guid, 'attached')) {
				$success++;
			}
		}
		return $success;
	}

	/**
	 * Returns getter options for message attachments
	 * 
	 * @param array $options Additional options
	 * @return array
	 */
	public function getAttachmentsFilterOptions(array $options = array()) {
		$defaults = array(
			'relationship' => 'attached',
			'relationship_guid' => $this->guid,
			'inverse_relationship' => false,
		);
		return array_merge($defaults, $options);
	}

	/**
	 * Returns an array of attached entities
	 * 
	 * @param array $options  Additional options
	 * @param bool  $threaded Threaded display
	 * @return ElggEntity[]|false
	 */
	public function getAttachments(array $options = array(), $threaded = false) {
		if ($threaded) {
			return $this->thread()->getAttachments($options);
		} else {
			$options = $this->getAttachmentsFilterOptions($options);
			return elgg_get_entities_from_relationship($options);
		}
	}

	/**
	 * Check if message has attachments
	 * Returns a count of attachments
	 *
	 * @param array $options Additional options
	 * @param bool  $threaded Threaded display
	 * @return int
	 */
	public function hasAttachments(array $options = array(), $threaded = false) {
		if ($threaded) {
			return $this->thread()->hasAttachments($options);
		} else {
			$options['count'] = true;
			return $this->getAttachments($options);
		}
	}

	/**
	 * Returns default sender
	 * @return ElggSite
	 */
	public function getDefaultSender() {
		return elgg_get_site_entity();
	}

	/**
	 * Validate that a message has a sender, at least one recipient and body
	 * @return boolean
	 */
	public function validate() {
		$sender = $this->getSender();
		if (!$sender) {
			return false;
		}
		$recipients = $this->getRecipients();
		if (!is_array($recipients) || !count($recipients)) {
			return false;
		}
		$body = $this->getBody();
		if (!$body) {
			return false;
		}
		if (elgg_trigger_before_event('send', 'object', $this) === false) {
			return false;
		}
		return true;
	}

	/**
	 * Create copies of the message in each of participants' inboxes
	 * @return int|false GUID of the sent message or false on error
	 */
	public function send() {

		if (!$this->validate()) {
			return false;
		}

		// Create a sender copy first
		$owner = $this->getSender();
		$this->owner_guid = $owner->guid; // A copy of the message is owned by each of the participants
		$this->container_guid = $owner->guid;
		$this->access_id = ACCESS_PRIVATE; // A copy of the message is private to its owner

		$guid = $this->save();
		if (!$guid) {
			return false;
		}

		$this->attach();

		// Create a copy for each of the recipients
		$ia = elgg_set_ignore_access(true);
		$recipients = $this->getRecipients();
		foreach ($recipients as $recipient) {
			if ($recipient->guid == $owner->guid) {
				continue;
			}
			$copy = clone $this;
			$copy->owner_guid = $recipient->guid;
			$copy->container_guid = $recipient->guid;
			if ($copy->save()) {
				$copy->attach();
			}
		}
		elgg_set_ignore_access($ia);

		elgg_trigger_after_event('send', 'object', $this);

		return $guid;
	}

	/**
	 * Saves a message and sets its hash
	 * @return int GUID of the saved message
	 */
	public function save() {

		$defaults = array(
			'msgHash' => $this->calcHash(),
			'msgType' => $this->getMessageType(),
			'readYet' => false,
			'hiddenFrom' => false, // legacy flag
			'hiddenTo' => false, // legacy flag
			'msg' => true, // legacy flag
		);
		foreach ($defaults as $key => $value) {
			if (!isset($this->$key)) {
				$this->$key = $value;
			}
		}

		return parent::save();
	}

	/**
	 * Delete message
	 * 
	 * @param bool $recursive Delete recursively
	 * @param bool $threaded  Delete all messages in a thread
	 * @return bool
	 */
	public function delete($recursive = true, $threaded = false) {
		if ($threaded) {
			return $this->thread()->delete($recursive);
		}
		return parent::delete($recursive);
	}

	/**
	 * Construct a new thread
	 * @return Thread
	 */
	public function thread() {
		return new Thread($this);
	}

	/**
	 * Returns a new entity group
	 * @return Group
	 */
	public function group() {
		return new Group;
	}

	/**
	 * Alias for factory
	 * 
	 * @param array $options Options
	 * @return Message
	 * @deprecated since version 3.1
	 */
	public static function construct(array $options = array()) {
		return self::factory($options);
	}
}
