<?php

namespace hypeJunction\Inbox;

class Ajax {

	/**
	 * Add unread notifications count to the ajax responses
	 *
	 * @param string $hook   "output"
	 * @param string $type   "ajax"
	 * @param array  $return Ajax output
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function setUnreadMessagesCount($hook, $type, $return, $params) {
		$return['inbox']['unread'] = (int) hypeInbox()->model->countUnreadMessages();
		return $return;
	}
}
