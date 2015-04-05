<?php

/**
 * hypeInbox
 * Enhanced inbox for Elgg
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyright (c) 2011-2015, Ismayil Khayredinov
 */
require_once __DIR__ . '/lib/autoloader.php';

if (hypeInbox()->config->get('legacy_mode')) {
	hypeInbox()->config->setLegacyConfig();
	require_once __DIR__ . '/lib/deprecated_ns.php';
	require_once __DIR__ . '/lib/deprecated.php';
}

elgg_register_event_handler('init', 'system', function() {

	hypeInbox()->config->registerLabels();

	hypeInbox()->events->init();
	hypeInbox()->hooks->init();
	hypeInbox()->router->init();
	hypeInbox()->actions->init();

	elgg_register_css('inbox.base.css', elgg_get_simplecache_url('css', 'framework/inbox/stylesheet.css'));
});
