<?php

namespace hypeJunction\Inbox;

/**
 * Setup menus on page setup
 * @return void
 */
function pagesetup() {
	elgg_register_menu_item('page', array(
		'name' => 'message_types',
		'text' => elgg_echo('admin:inbox:message_types'),
		'href' => 'admin/inbox/message_types',
		'priority' => 500,
		'contexts' => array('admin'),
		'section' => 'configure'
	));
}
