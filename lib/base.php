<?php

$message_types = elgg_get_plugin_setting('message_types', 'hypeInbox');
if ($message_types) {
	$message_types = unserialize($message_types);
	foreach ($message_types as $type => $options) {
		if (isset($options['labels'])) {
			foreach ($options['labels'] as $suffix => $label) {
				add_translation('en', array("item:object:message:$type:$suffix" => $label));
			}
		}
	}
} else {
	$message_types = array();
}
elgg_set_config('inbox_message_types', $message_types);

$user_labels = elgg_get_plugin_user_setting('labels', elgg_get_logged_in_user_guid(), 'hypeInbox');
elgg_set_config('inbox_user_labels', ($user_labels) ? unserialize($user_labels) : array());

elgg_set_config('inbox_user_types', hj_inbox_get_user_types());
elgg_set_config('inbox_user_relationships', hj_inbox_get_user_relationships());
elgg_set_config('inbox_user_group_relationships', hj_inbox_get_user_group_relationships());

/**
 * Extendable configuration array of sender and recipient types
 * These will be used when applying message type rules
 * - 'validation' callback function will be used to identify whether or not a user belongs to that user type group (user entity will be passed to this callback function)
 * - 'getter' callback function will be used to populate userpicker options
 *
 * Use 'config:user_types','framework:inbox' plugin hook to extend this array
 */
function hj_inbox_get_user_types() {

	$config = array(
		'all' => array(),
		'admin' => array(
			'validator' => 'hj_inbox_is_admin_user',
			'getter' => 'hj_inbox_admin_getter_options'
		),
	);

	return elgg_trigger_plugin_hook('config:user_types', 'framework:inbox', null, $config);
}

/**
 * Get a list of existing user - user relationships
 */
function hj_inbox_get_user_relationships() {

	$return = array();

	$dbprefix = elgg_get_config('dbprefix');

	$query = "SELECT DISTINCT(er.relationship)
				FROM {$dbprefix}entity_relationships er
				JOIN {$dbprefix}entities e1 on e1.guid = er.guid_one
				JOIN {$dbprefix}entities e2 on e2.guid = er.guid_two
				WHERE e1.type = 'user' AND e2.type = 'user'";

	$data = get_data($query);

	foreach ($data as $rel) {
		$return[] = $rel->relationship;
	}

	return $return;
}

/**
 * Get a list of existing user-group and group-user relationships
 */
function hj_inbox_get_user_group_relationships() {

	$return = array();

	$dbprefix = elgg_get_config('dbprefix');

	$query = "SELECT DISTINCT(er.relationship)
				FROM {$dbprefix}entity_relationships er
				JOIN {$dbprefix}entities e1 on e1.guid = er.guid_one
				JOIN {$dbprefix}entities e2 on e2.guid = er.guid_two
				WHERE (e1.type = 'user' AND e2.type = 'group')
					OR (e1.type = 'group' AND e2.type = 'user')";

	$data = get_data($query);

	foreach ($data as $rel) {
		$return[] = $rel->relationship;
	}

	return $return;
}

/**
 * Get userpicker options based on the current message type config
 *
 * @param string $message_type Current message type
 * @param ElggUser $user Sender
 * @return array An array of options
 */
function hj_inbox_get_userpicker_options($message_type = HYPEINBOX_PRIVATE, $user = null) {

	$options = array(
		'types' => 'user'
	);

	$dbprefix = elgg_get_config('dbprefix');

	$message_types = elgg_get_config('inbox_message_types');
	$user_types = elgg_get_config('inbox_user_types');

	$rel = 0;

	$policies = $message_types[$message_type]['policy'];
	if (!$policies) {
		return $options;
	}

	foreach ($policies as $policy) {

		$recipient_type = $policy['recipient'];
		$sender_type = $policy['sender'];
		$relationship_type = $policy['relationship'];
		$inverse_relationship = $policy['inverse_relationship'];

		$validator = $user_types[$sender_type]['validator'];
		$getter = $user_types[$recipient_type]['getter'];

		if ($sender_type != 'all' && $validator && is_callable($validator)) {
			if (!call_user_func($validator, $user, $sender_type)) {
				continue;
			}
		}

		$where = '';
		$recipient_options_wheres = '';

		if ($relationship_type && $relationship_type != 'all') {
			$rel_table = "rel$rel";
			if (!$inverse_relationship) {
				$options['joins'][] = "JOIN {$dbprefix}entity_relationships $rel_table ON e.guid = $rel_table.guid_two";
				$where = "$rel_table.guid_one = $user->guid AND $rel_table.relationship = '$relationship_type'";
			} else {
				$options['joins'][] = "JOIN {$dbprefix}entity_relationships $rel_table ON e.guid = $rel_table.guid_one";
				$where = "$rel_table.guid_two = $user->guid AND $rel_table.relationship = '$relationship_type'";
			}
			$rel++;
		}

		if ($recipient_type != 'all') {
			if ($getter && is_callable($getter)) {
				$recipient_options = call_user_func($getter, $recipient_type);

				if (is_array($recipient_options['wheres'])) {
					$recipient_options_wheres = implode(" AND ", $recipient_options['wheres']);
					unset($recipient_options['wheres']);
				}
				if ($recipient_options_wheres) {
					if ($where) {
						$where = "$where AND $recipient_options_wheres";
					} else {
						$where = $recipient_options_wheres;
					}
				}
				$options = array_merge_recursive($options, $recipient_options);
			}
		}
		if ($where) {
			$wheres[] = "($where)";
		}
	}

	if ($wheres) {
		$wheres = implode(" OR ", $wheres);
		$options['wheres'][] = "($wheres)";
	}

	foreach ($options as $key => $option) {
		if (is_array($option)) {
			$options[$key] = array_unique($option);
		}
	}

	return $options;
}

/**
 * Check if the user is an admin
 * @param ElggUser $user
 * @return boolean
 */
function hj_inbox_is_admin_user($user) {
	if (!elgg_instanceof($user, 'user')) {
		return false;
	}

	return elgg_is_admin_user($user->guid);
}

/**
 * Get admin users getter options
 * @return array
 */
function hj_inbox_admin_getter_options() {

	$dbprefix = elgg_get_config('dbprefix');
	return array(
		'types' => 'user',
		'joins' => array(
			"JOIN {$dbprefix}users_entity ue_admin ON e.guid = ue_admin.guid"
		),
		'wheres' => array(
			"ue_admin.admin = 'yes'"
		)
	);
}

/**
 * Get message types the user can receive
 * @param ElggUser $user
 * @return array An array of message types
 */
function hj_inbox_get_incoming_message_types($user = null) {

	$return = array();

	if (!elgg_instanceof($user)) {
		$user = elgg_get_logged_in_user_entity();
		if (!$user) {
			return $return;
		}
	}

	$message_types = elgg_get_config('inbox_message_types');
	$user_types = elgg_get_config('inbox_user_types');

	foreach ($message_types as $type => $options) {

		$policies = $options['policy'];
		if (!$policies) {
			$return[] = $type;
			continue;
		}

		foreach ($policies as $policy) {

			$recipient_type = $policy['recipient'];

			if ($recipient_type == 'all') {
				$return[] = $type;
				break;
			}

			$validator = $user_types[$recipient_type]['validator'];
			if ($validator && is_callable($validator) && call_user_func($validator, $user, $recipient_type)) {
				$return[] = $type;
				break;
			}
		}
	}

	return $return;
}

/**
 * Get message types the user can send
 * @param ElggUser $user
 * @return array An array of message types
 */
function hj_inbox_get_outgoing_message_types($user = null) {

	$return = array();

	if (!elgg_instanceof($user)) {
		$user = elgg_get_logged_in_user_entity();
		if (!$user) {
			return $return;
		}
	}

	$message_types = elgg_get_config('inbox_message_types');
	$user_types = elgg_get_config('inbox_user_types');

	foreach ($message_types as $type => $options) {

		$policies = $options['policy'];

		if (!$policies) {
			if ($type != HYPEINBOX_NOTIFICATION) {
				$return[] = $type;
			}
			continue;
		}

		foreach ($policies as $policy) {

			$sender_type = $policy['sender'];

			if ($sender_type == 'all') {
				$return[] = $type;
				break;
			}

			$validator = $user_types[$sender_type]['validator'];
			if ($validator && is_callable($validator) && call_user_func($validator, $user, $sender_type)) {
				$return[] = $type;
				break;
			}
		}
	}

	return $return;
}

/**
 * Count unread messages of a given type received by a given user
 * @param string $message_type Message type
 * @param ElggUser $user
 * @return int Count of unread messages
 */
function hj_inbox_count_unread_messages($message_type = null, $user = null) {

	if (!$user) {
		$user = elgg_get_logged_in_user_entity();
		if (!$user)
			return 0;
	}

	$dbprefix = elgg_get_config('dbprefix');

	$strings = array('toId', $user->guid, 'readYet', 0, 'msg', 1);
	if ($message_type) {
		$strings[] = 'msgType';
		$strings[] = $message_type;
	}

	$map = array();
	foreach ($strings as $string) {
		$id = get_metastring_id($string);
		$map[$string] = $id;
	}

	$options = array(
		'joins' => array(
			"JOIN {$dbprefix}metadata msg_toId on e.guid = msg_toId.entity_guid",
			"JOIN {$dbprefix}metadata msg_readYet on e.guid = msg_readYet.entity_guid",
		//"JOIN {$dbprefix}metadata msg_msg on e.guid = msg_msg.entity_guid",
		),
		'wheres' => array(
			"msg_toId.name_id='{$map['toId']}' AND msg_toId.value_id='{$map[$user->guid]}'",
			"msg_readYet.name_id='{$map['readYet']}' AND msg_readYet.value_id='{$map[0]}'",
		//"msg_msg.name_id='{$map['msg']}' AND msg_msg.value_id='{$map[1]}'",
		),
		'owner_guid' => $user->guid,
		'count' => true,
	);

	if ($message_type) {
		$options['joins'][] = "JOIN {$dbprefix}metadata msg_type on e.guid = msg_type.entity_guid";
		$options['wheres'][] = "msg_type.name_id='{$map['msgType']}' AND msg_type.value_id='{$map[$message_type]}'";
	}

	return elgg_get_entities_from_metadata($options);
}

/**
 * Prepare compose form variables
 *
 * @param integer $recipient_guids GUIDs of recipients if any
 * @param string $message_type Type of the message being composed
 * @param ElggObject $entity Message to which the reply is to be sent
 * @return array An array of form variables
 */
function hj_inbox_prepare_form_vars($recipient_guids = null, $message_type = HYPEINBOX_PRIVATE, $entity = null) {

	if ($recipient_guids && !is_array($recipient_guids)) {
		$recipient_guids = array($recipient_guids);
	}

	$values = array(
		'subject' => ($entity) ? "Re: $entity->title" : '',
		'body' => '',
		'recipient_guids' => $recipient_guids,
		'message_type' => $message_type,
		'reply_to' => $entity->guid
	);

	if (elgg_is_sticky_form('messages')) {
		foreach (array_keys($values) as $field) {
			$values[$field] = elgg_get_sticky_value('messages', $field);
		}
	}

	elgg_clear_sticky_form('messages');

	return $values;
}

/**
 * Send a message to specified recipients
 *
 * @param int $sender_guid GUID of the sender entity
 * @param array $recipient_guids An array of recipient GUIDs
 * @param str $subject Subject of the message
 * @param str $message Body of the message
 * @param str $message_type Type of the message
 * @param array $params Additional parameters, e.g. 'message_hash', 'attachments'
 * @return boolean
 */
function hj_inbox_send_message($sender_guid, $recipient_guids, $subject = '', $message = '', $message_type = '', array $params = array()) {

	$ia = elgg_set_ignore_access();
	
	if (!is_array($recipient_guids)) {
		$recipient_guids = array($recipient_guids);
	}

	$message_hash = elgg_extract('message_hash', $params);
	$attachments = elgg_extract('attachments', $params);

	$user_guids = $recipient_guids;
	$user_guids[] = $sender_guid;
	sort($user_guids);

	if (!$message_hash) {
		$title = strtolower($subject);
		$title = trim(str_replace('re:', '', $title));
		$message_hash = sha1(implode(':', $user_guids) . $title);
	}

	$acl_hash = sha1(implode(':', $user_guids));

	$dbprefix = elgg_get_config('dbprefix');
	$query = "SELECT * FROM {$dbprefix}access_collections WHERE name = '$acl_hash'";
	$collection = get_data_row($query);
	error_log(print_r($collection, true));
	$acl_id = $collection->id;
	if (!$acl_id) {
		$site = elgg_get_site_entity();
		$acl_id = create_access_collection($acl_hash, $site->guid);
		update_access_collection($acl_id, $user_guids);
	}
	error_log($acl_id);
	$message_sent = new ElggObject();
	$message_sent->subtype = "messages";
	$message_sent->owner_guid = $sender_guid;
	$message_sent->container_guid = $sender_guid;
	$message_sent->access_id = ACCESS_PRIVATE;
	$message_sent->title = $subject;
	$message_sent->description = $message;
	$message_sent->toId = $recipient_guids; // the users receiving the message
	$message_sent->fromId = $sender_guid; // the user sending the message
	$message_sent->readYet = 1; // this is a toggle between 0 / 1 (1 = read)
	$message_sent->hiddenFrom = 0; // this is used when a user deletes a message in their sentbox, it is a flag
	$message_sent->hiddenTo = 0; // this is used when a user deletes a message in their inbox

	$message_sent->msg = 1;

	$message_sent->msgType = $message_type;
	$message_sent->msgHash = $message_hash;

	$message_sent->save();
	
	if ($attachments) {
		$count = count($attachments['name']);
		for ($i = 0; $i < $count; $i++) {
			if ($attachments['error'][$i] || !$attachments['name'][$i]) {
				continue;
			}

			$name = $attachments['name'][$i];

			$file = new ElggFile();
			$file->container_guid = $message_sent->guid;
			$file->title = $name;
			$file->access_id = (int)$acl_id;

			$prefix = "file/";
			$filestorename = elgg_strtolower(time() . $name);
			$file->setFilename($prefix . $filestorename);


			$file->open("write");
			$file->close();
			move_uploaded_file($attachments['tmp_name'][$i], $file->getFilenameOnFilestore());

			$saved = $file->save();

			if ($saved) {
				$mime_type = ElggFile::detectMimeType($attachments['tmp_name'][$i], $attachments['type'][$i]);
				$info = pathinfo($name);
				$office_formats = array('docx', 'xlsx', 'pptx');
				if ($mime_type == "application/zip" && in_array($info['extension'], $office_formats)) {
					switch ($info['extension']) {
						case 'docx':
							$mime_type = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
							break;
						case 'xlsx':
							$mime_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
							break;
						case 'pptx':
							$mime_type = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
							break;
					}
				}


				// check for bad ppt detection
				if ($mime_type == "application/vnd.ms-office" && $info['extension'] == "ppt") {
					$mime_type = "application/vnd.ms-powerpoint";
				}

				$file->msgHash = $message_hash;
				$file->toId = $recipient_guids;
				$file->fromId = $sender_guid;
				$file->setMimeType($mime_type);
				$file->originalfilename = $name;
				if (elgg_is_active_plugin('file')) {
					$file->simpletype = file_get_simple_type($mime_type);
				}

				$file->save();
				$guid = $file->getGUID();
				$uploaded_attachments[] = $guid;
				$attachment_urls .= '<div class="inbox-attachment">' . elgg_view('output/url', array(
							'href' => "messages/download/$guid",
							'text' => $file->title,
							'is_trusted' => true
						)) . '</div>';

				if ($file->simpletype == "image") {
					$file->icontime = time();

					$thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 60, 60, true);
					if ($thumbnail) {
						$thumb = new ElggFile();
						$thumb->setMimeType($attachments['type'][$i]);

						$thumb->setFilename($prefix . "thumb" . $filestorename);
						$thumb->open("write");
						$thumb->write($thumbnail);
						$thumb->close();

						$file->thumbnail = $prefix . "thumb" . $filestorename;
						unset($thumbnail);
					}

					$thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 153, 153, true);
					if ($thumbsmall) {
						$thumb->setFilename($prefix . "smallthumb" . $filestorename);
						$thumb->open("write");
						$thumb->write($thumbsmall);
						$thumb->close();
						$file->smallthumb = $prefix . "smallthumb" . $filestorename;
						unset($thumbsmall);
					}

					$thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 600, 600, false);
					if ($thumblarge) {
						$thumb->setFilename($prefix . "largethumb" . $filestorename);
						$thumb->open("write");
						$thumb->write($thumblarge);
						$thumb->close();
						$file->largethumb = $prefix . "largethumb" . $filestorename;
						unset($thumblarge);
					}
				}
			}
		}
	}

	$success = $error = 0;

	foreach ($recipient_guids as $recipient_guid) {
		$message_to = new ElggObject();
		$message_to->subtype = "messages";
		$message_to->owner_guid = $recipient_guid;
		$message_to->container_guid = $recipient_guid;
		$message_to->access_id = ACCESS_PRIVATE;
		$message_to->title = $subject;
		$message_to->description = $message;

		$message_to->toId = $recipient_guids; // the users receiving the message
		$message_to->fromId = $sender_guid; // the user sending the message
		$message_to->readYet = 0; // this is a toggle between 0 / 1 (1 = read)
		$message_to->hiddenFrom = 0; // this is used when a user deletes a message in their sentbox, it is a flag
		$message_to->hiddenTo = 0; // this is used when a user deletes a message in their inbox

		$message_to->msg = 1;

		$message_to->msgType = $message_type;
		$message_to->msgHash = $message_hash;
		
		if ($message_to->save()) {
			$success++;

			// Make attachments
			if ($uploaded_attachments) {
				foreach ($uploaded_attachments as $attachment_guid) {
					make_attachment($message_to->guid, $attachment_guid);
				}
			}

			// Send out notifications skipping 'site' notification handler
			if ($recipient_guid != $sender_guid) {
				$methods = (array) get_user_notification_settings($recipient_guid);
				unset($methods['site']);
				if (count($methods)) {
					$recipient = get_user($recipient_guid);
					$sender = get_user($sender_guid);

					$notification_subject = elgg_echo('messages:email:subject');
					$notification_message = strip_tags($message);
					if ($uploaded_attachments) {
						$notification_message .= elgg_view_module('inbox-attachments', elgg_echo('messages:attachments'), $attachment_urls);
					}
					$notification_body = elgg_echo('messages:email:body', array(
						$sender->name,
						$notification_message,
						elgg_get_site_url() . "messages/inbox/$recipient->username?message_type=$message_type",
						$sender->name,
						elgg_get_site_url() . "messages/thread/$message_hash"
					));
					notify_user($recipient_guid, $sender_guid, $notification_subject, $notification_body, null, $methods);
				}
			}
		} else {
			$error++;
		}
	}

	if ($success > 0) {
		// Make attachments
		if ($uploaded_attachments) {
			foreach ($uploaded_attachments as $attachment_guid) {
				make_attachment($message_sent->guid, $attachment_guid);
			}
		}
		$return = true;
	} else {
		$message_sent->delete();
		$return = false;
	}

	elgg_set_ignore_access($ia);

	return $return;
}