<?php

namespace hypeJunction\Inbox;

use ElggBatch;
use ElggEntity;
use InvalidArgumentException;

class Thread {

	protected $message;

	const LIMIT = 10;
	
	/**
	 * Construct a magic thread
	 * @param Message $message Message entity
	 */
	public function __construct(Message $message) {
		if (!$message instanceof Message) {
			throw new InvalidArgumentException(get_class() . ' expects an instance of ' . get_class(new Message));
		}
		$this->message = $message;
	}

	/**
	 * Get options for {@link elgg_get_entities()}
	 * 
	 * @param array $options Default options array
	 * @return array
	 */
	public function getFilterOptions(array $options = array()) {
		$options['types'] = Message::TYPE;
		$options['subtypes'] = Message::SUBTYPE;
		$options['owner_guids'] = $this->message->owner_guid;
		$options['metadata_name_value_pairs'][] = array(
			'name' => 'msgHash',
			'value' => $this->message->getHash(),
		);
		if (!isset($options['order_by'])) {
			$options['order_by'] = 'e.guid ASC';
		}
		return $options;
	}

	/**
	 * Calculate a page offset to the given message
	 *
	 * @param int $limit Items per page
	 * @return int
	 */
	public function getOffset($limit = self::LIMIT) {
		if ($limit === 0) {
			return 0;
		}
		$before = $this->getMessagesBefore(array('count' => true, 'offset' => 0));
		return floor($before / $limit) * $limit;
	}

	/**
	 * Get messages in a thread
	 * 
	 * @param array $options Default options array
	 * @return Message[]|false
	 */
	public function getMessages(array $options = array()) {
		return elgg_get_entities_from_metadata($this->getFilterOptions($options));
	}

	/**
	 * Get unread messages in a thread
	 * 
	 * @param array $options Default options array
	 * @return Message[]
	 */
	public function getUnreadMessages(array $options = array()) {
		$options['metadata_name_value_pairs'][] = array(
			'name' => 'readYet',
			'value' => false,
		);
		return $this->getMessages($options);
	}

	/**
	 * Get count of messages in a thread
	 * 
	 * @param array $options Default options array
	 * @return int
	 */
	public function getCount(array $options = array()) {
		$options['count'] = true;
		return $this->getMessages($options);
	}

	/**
	 * Get count of unread messages in a thread
	 * 
	 * @param array $options Default options array
	 * @return int
	 */
	public function getUnreadCount(array $options = array()) {
		$options['count'] = true;
		return $this->getUnreadMessages($options);
	}

	/**
	 * Check if thread contains unread messages
	 * @return boolean
	 */
	public function isRead() {
		return (!$this->getUnreadCount());
	}

	/**
	 * Mark all messages in a thread as read
	 * @return void
	 */
	public function markRead() {
		$messages = $this->getAll();
		foreach ($messages as $message) {
			$message->readYet = true;
		}
	}

	/**
	 * Mark all messages in a thread as unread
	 * @return void
	 */
	public function markUnread() {
		$messages = $this->getAll();
		foreach ($messages as $message) {
			$message->readYet = false;
		}
	}

	/**
	 * Delete all messages in a thread
	 * 
	 * @param bool $recursive Delete recursively
	 * @return bool
	 */
	public function delete($recursive = true) {
		$success = 0;
		$count = $this->getCount();
		$messages = $this->getAll();
		$messages->setIncrementOffset(false);
		foreach ($messages as $message) {
			if ($message->delete($recursive)) {
				$success++;
			}
		}
		return ($success == $count);
	}

	/**
	 * Get all messages as batch
	 * 
	 * @param string $getter  Callable getter
	 * @param array  $options Getter options
	 * @return ElggBatch
	 */
	public function getAll($getter = 'elgg_get_entities_from_metadata', $options = array()) {
		$options['limit'] = 0;
		$options = $this->getFilterOptions($options);
		return new ElggBatch($getter, $options);
	}

	/**
	 * Get preceding messages
	 *
	 * @param array $options Additional options
	 * @return mixed
	 */
	public function getMessagesBefore(array $options = array()) {
		$options['wheres'][] = "e.guid < {$this->message->guid}";
		$options['order_by'] = 'e.guid DESC';
		$messages = elgg_get_entities_from_metadata($this->getFilterOptions($options));
		if (is_array($messages)) {
			return array_reverse($messages);
		}
		return $messages;
	}

	/**
	 * Get succeeding messages
	 *
	 * @param array $options Additional options
	 * @return mixed
	 */
	public function getMessagesAfter(array $options = array()) {
		$options['wheres'][] = "e.guid > {$this->message->guid}";
		return elgg_get_entities_from_metadata($this->getFilterOptions($options));
	}

	/**
	 * Returns an array of getter options for retrieving attachments in the thread
	 *
	 * @param array $options Additional options
	 * @return array
	 */
	public function getAttachmentsFilterOptions(array $options = array()) {

		$dbprefix = elgg_get_config('dbprefix');

		$msn = elgg_get_metastring_id('msgHash');
		$msv = elgg_get_metastring_id($this->message->getHash());

		$options['joins'][] = "JOIN {$dbprefix}entity_relationships er ON er.guid_two = e.guid";
		$options['joins'][] = "JOIN {$dbprefix}metadata md ON er.guid_one = md.entity_guid";
		$options['wheres'][] = "er.relationship = 'attached'";
		$options['wheres'][] = "md.name_id = $msn AND md.value_id = $msv";

		return $options;
	}

	/**
	 * Returns an array of attachments in the thread
	 *
	 * @param array $options Additional options
	 * @return ElggEntity[]|false
	 */
	public function getAttachments(array $options = array()) {
		$options = $this->getAttachmentsFilterOptions($options);
		return elgg_get_entities($options);
	}

	/**
	 * Returns a count of attachments in the thread
	 * 
	 * @param array $options Additional options
	 * @return int
	 */
	public function hasAttachments(array $options = array()) {
		$options['count'] = true;
		return $this->getAttachments($options);
	}

}
