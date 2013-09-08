<?php

/* hypeInbox
 *
 * Enhanced inbox for Elgg
 * 
 * @package hypeJunction
 * @subpackage hypeInbox
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyright (c) 2011-2013, Ismayil Khayredinov
 */

define('HYPEINBOX_RELEASE', 1374851653);
define('HYPEINBOX_NOTIFICATION', '__notification');
define('HYPEINBOX_PRIVATE', '__private');

define('HYPEINBOX_USERPICKER_BATCH_SIZE', 21);

elgg_register_event_handler('init', 'system', 'hj_inbox_init');

function hj_inbox_init() {

	$libraries = array(
		'integrations',
		'notifications',
		'base',
		'page_handlers',
		'actions',
		'assets',
		'views',
		'menus',
		'hooks',
		'events',
	);

	foreach ($libraries as $lib) {
		$path = elgg_get_plugins_path() . "hypeInbox/lib/{$lib}.php";
		if (file_exists($path)) {
			elgg_register_library("inbox:library:$lib", $path);
			elgg_load_library("inbox:library:$lib");
		}
	}

	elgg_register_event_handler('upgrade', 'system', 'hj_inbox_check_release');
}

/**
 * Run upgrade scripts
 *
 * @param string $event Equals 'upgrade'
 * @param string $type Equals 'system'
 * @param type $params
 * @return boolean
 */
function hj_inbox_check_release($event, $type, $params) {

	if (!elgg_is_admin_logged_in()) {
		return true;
	}

	$release = HYPECATEGORIES_RELEASE;
	$old_release = elgg_get_plugin_setting('release', 'hypeInbox');

	if ($release > $old_release) {

		elgg_register_library("inbox:library:upgrade", elgg_get_plugins_path() . 'hypeInbox/lib/upgrade.php');
		elgg_load_library("inbox:library:upgrade");

		elgg_set_plugin_setting('release', $release, 'hypeCategories');
	}

	return true;
}