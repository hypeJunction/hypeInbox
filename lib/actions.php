<?php

$path = elgg_get_plugins_path() . 'hypeInbox/actions/';

elgg_register_action('hypeInbox/settings/save', $path . 'settings/save.php', 'admin');
elgg_register_action('inbox/admin/import', $path . 'admin/import.php', 'admin');

elgg_register_action("messages/send", $path . "messages/send.php");
elgg_register_action("messages/delete", $path . "messages/delete.php");
elgg_register_action("messages/markread", $path . "messages/markread.php");
elgg_register_action("messages/markunread", $path . "messages/markunread.php");