<?php

// Replace page handler defined by messages plugin
elgg_unregister_page_handler('messages', 'messages_page_handler');
elgg_register_page_handler('messages', 'hj_inbox_page_handler');

/**
 * Page handler for 'messages'
 * @param array $page An array of URL segments
 * @return boolean Outputs a page or returns false on failure
 */
function hj_inbox_page_handler($page) {

	gatekeeper();

	elgg_load_css('inbox.base.css');

	// Due to varying segmentation in earlier version of messages plugin, let's iterate through segments and find the page owner
	foreach ($page as $segment) {
		$current_user = get_user_by_username($segment);
		if (elgg_instanceof($current_user, 'user')) {
			break;
		}
	}

	if (elgg_instanceof($current_user, 'user') && !$current_user->canEdit()) {
		forward("messages/$current_user->username");
	} else {
		$current_user = elgg_get_logged_in_user_entity();
	}

	elgg_set_page_owner_guid($current_user->guid);

	elgg_push_breadcrumb(elgg_echo("hj:inbox"), "messages/$current_user->username");

	switch ($page[0]) {

		default :
		case 'inbox' :
		case 'incoming' :

			hj_inbox_title_menu_setup();

			elgg_load_js('inbox.user.js');

			$message_type = get_input('message_type', HYPEINBOX_PRIVATE);

			elgg_push_breadcrumb(elgg_echo("hj:inbox"), "messages/inbox/$current_user->username");
			elgg_push_breadcrumb(elgg_echo("item:object:message:$message_type:plural"));

			$params = array(
				'message_type' => $message_type
			);
			$title = elgg_echo("hj:inbox:inbox");
			$content = elgg_view("framework/inbox/inbox", $params);
			$filter = elgg_view("framework/inbox/filters/inbox", $params);
			$sidebar = elgg_view('framework/inbox/sidebar', $params);
			break;

		case 'outbox' :
		case 'outgoing' :
		case 'sent' :

			hj_inbox_title_menu_setup();

			elgg_load_js('inbox.user.js');

			$message_type = get_input('message_type', HYPEINBOX_PRIVATE);

			elgg_push_breadcrumb(elgg_echo("hj:inbox:sent"), "messages/sent/$current_user->username");
			elgg_push_breadcrumb(elgg_echo("item:object:message:$message_type:plural"));

			$params = array(
				'message_type' => $message_type
			);
			$title = elgg_echo("hj:inbox:sent");
			$content = elgg_view("framework/inbox/sent", $params);
			$filter = elgg_view("framework/inbox/filters/sent", $params);
			$sidebar = elgg_view('framework/inbox/sidebar', $params);
			break;

		case 'read' :
		case 'view' :
		case 'reply' :

			$entity = get_entity($page[1]);
			
			hj_inbox_title_menu_setup($entity);

			if (!elgg_instanceof($entity, 'object', 'messages')) {
				$title = elgg_echo('hj:inbox:message_not_found');
				$content = elgg_view('framework/inbox/notfound');
			} else {
				elgg_push_breadcrumb(elgg_echo("hj:inbox:inbox"), "messages/inbox/$current_user->username");
				elgg_push_breadcrumb(elgg_echo("item:object:message:$entity->msgType:plural"), "messages/inbox/$current_user->username?message_type=$entity->msgType");
				elgg_push_breadcrumb($entity->title);

				$title = elgg_echo('hj:inbox:message', array($entity->title));
				$content = elgg_view('framework/inbox/thread', array(
					'entity' => $entity,
				));
			}

			$filter = false;
			$sidebar = elgg_view('framework/inbox/sidebar', array(
				'entity' => $entity,
				'message_type' => $entity->msgType
			));
			break;

		case 'thread' :

			$hash = elgg_extract(1, $page, false);
			if (!$hash) {
				return false;
			}

			$entities = elgg_get_entities_from_metadata(array(
				'owner_guid' => $current_user->guid,
				'metadata_name_value_pairs' => array(
					'name' => 'msgHash', 'value' => $hash
				),
				'order_by' => 'e.time_created ASC',
				'limit' => 1
			));

			$entity = $entities[0];

			hj_inbox_title_menu_setup($entity);

			if (!elgg_instanceof($entity, 'object', 'messages')) {
				$title = elgg_echo('hj:inbox:message_not_found');
				$content = elgg_view('framework/inbox/notfound');
			} else {
				elgg_push_breadcrumb(elgg_echo("hj:inbox:inbox"), "messages/inbox/$current_user->username");
				elgg_push_breadcrumb(elgg_echo("item:object:message:$entity->msgType:plural"), "messages/inbox/$current_user->username?message_type=$entity->msgType");
				elgg_push_breadcrumb($entity->title);

				$title = elgg_echo('hj:inbox:message', array($entity->title));
				$content = elgg_view('framework/inbox/thread', array(
					'entity' => $entity,
					'message_hash' => $hash
				));
			}

			$filter = false;
			$sidebar = elgg_view('framework/inbox/sidebar', array(
				'entity' => $entity,
				'message_type' => $entity->msgType
			));
			break;

		case 'compose' :
		case 'add' :

			$message_type = get_input('message_type', HYPEINBOX_PRIVATE);
			$entity = get_entity($page[1]);

			$title = elgg_echo("hj:inbox:compose", array("item:object:message:$message_type:singular"));

			elgg_push_breadcrumb(elgg_echo("item:object:message:$message_type:plural"));
			elgg_push_breadcrumb($title);

			$params = hj_inbox_prepare_form_vars((int) get_input('send_to'), $message_type, $entity);

			$content = elgg_view("framework/inbox/compose", $params);
			$filter = false;
			$sidebar = elgg_view('framework/inbox/sidebar', $params);

			break;

		case 'userpicker' :

			$sender = elgg_get_logged_in_user_entity();
			$message_type = get_input('message_type', HYPEINBOX_PRIVATE);

			$letter = get_input('letter', false);

			$output = array(
				'counters' => array(),
				'items' => array()
			);

			$getter_options = hj_inbox_get_userpicker_options($message_type, $sender);

			$dbprefix = elgg_get_config('dbprefix');

			if ($letter) {

				$options = $getter_options;
				$options['limit'] = 0;
				$options['joins'][] = "JOIN {$dbprefix}users_entity ue_query ON e.guid = ue_query.guid";
				$options['wheres'][] = "LOWER(ue_query.name) LIKE '$letter%'";
				$options['order_by'] = "ue_query.name ASC";

				$items = new ElggBatch('elgg_get_entities', $options);

				if ($items) {
					foreach ($items as $item) {
						$output['items'][] = elgg_view('input/messages/userpicker/item', array(
							'entity' => $item,
							'name' => get_input('name'),
							'multiple' => get_input('multiple', false),
							'checked' => in_array($item->guid, get_input('value', array()))
								), false, false, 'default');
					}
				}
			} else {

				foreach (range('a', 'z') as $letter) {
					$letters[] = "'$letter'";
					$letter = sanitize_string($letter);

					$options = $getter_options;
					$options['joins'][] = "JOIN {$dbprefix}users_entity ue_query ON e.guid = ue_query.guid";
					$options['wheres'][] = "LOWER(ue_query.name) LIKE '$letter%'";
					$options['order_by'] = "ue_query.name ASC";
					$options['count'] = true;

					$count = elgg_get_entities($options);

					$output['counters'][$letter] = (int) $count;
				}

				$letters_in = implode(',', $letters);

				$options = $getter_options;
				$options['joins'][] = "JOIN {$dbprefix}users_entity ue_query ON e.guid = ue_query.guid";
				$options['wheres'][] = "LOWER(LEFT(ue_query.name,1)) NOT IN ($letters_in)";
				$options['order_by'] = "ue_query.name ASC";
				$options['count'] = true;

				$count = elgg_get_entities($options);

				$output['counters']['*'] = (int) $count;
			}

			header("Content-type: application/json");
			echo json_encode($output);
			exit;
			break;
	}

	$params = array(
		'title' => $title,
		'filter' => $filter,
		'content' => $content,
		'sidebar' => $sidebar,
		'class' => 'inbox-layout'
	);

	if (elgg_is_xhr()) {
		print json_encode($params);
		forward();
	} else {
		$layout = elgg_view_layout('content', $params);
		echo elgg_view_page($title, $layout);
	}

	return true;
}