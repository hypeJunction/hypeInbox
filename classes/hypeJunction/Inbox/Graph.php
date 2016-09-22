<?php

namespace hypeJunction\Inbox;

use hypeJunction\Data\Property;

class Graph {

	/**
	 * Set hypeGraph alias for messages
	 *
	 * @param string $hook
	 * @param string $type
	 * @param array  $return
	 * @param array  $params
	 * @return array
	 */
	public static function getGraphAlias($hook, $type, $return, $params) {
		$return['object']['messages'] = ':message';
		return $return;
	}

	/**
	 * Configure export properties
	 *
	 * @param string $hook
	 * @param string $type
	 * @param array  $return
	 * @param array  $params
	 * @return array
	 */
	public static function getMessageProperties($hook, $type, $return, $params) {

		$return[] = new Property('thread_id', array(
			'getter' => '\hypeJunction\Inbox\Message::getThreadIdProp',
			'read_only' => true,
		));

		$return[] = new Property('message_type', array(
			'getter' => '\hypeJunction\Inbox\Message::getMessageTypeProp',
			'read_only' => true,
		));

		$return[] = new Property('attachments', array(
			'getter' => '\hypeJunction\Inbox\Message::getAttachmentsProp',
			'read_only' => true,
		));

		return $return;
	}
}
