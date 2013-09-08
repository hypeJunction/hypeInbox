<?php

register_notification_handler('site', 'hj_inbox_site_notify_handler');

/**
 * Notification handler
 *
 * @param ElggEntity $from
 * @param ElggUser   $to
 * @param string     $subject
 * @param string     $message
 * @param array      $params
 * @return bool
 */
function hj_inbox_site_notify_handler(ElggEntity $from, ElggUser $to, $subject, $message, array $params = array()) {

	if (!$from) {
		throw new NotificationException(elgg_echo('NotificationException:MissingParameter', array('from')));
	}

	if (!$to) {
		throw new NotificationException(elgg_echo('NotificationException:MissingParameter', array('to')));
	}

	return hj_inbox_send_message($from->guid, $to->guid, $subject, $message, HYPEINBOX_NOTIFICATION, $params);
	
}