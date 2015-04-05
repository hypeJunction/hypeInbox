<?php

$ha = access_get_show_hidden_status();
access_show_hidden_entities(true);

$messages = hypeInbox()->model->getUnhashedMessages(array('count' => true));

access_show_hidden_entities($ha);

if ($messages) {
	echo elgg_view('framework/inbox/admin/import', array(
		'count' => $messages
	));
} else {
	forward('admin/inbox/message_types');
}