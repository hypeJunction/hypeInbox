<?php

/**
 * Enhanced inbox for Elgg
 *
 * @package hypeJunction
 * @subpackage hypeInbox
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 */
try {
	require_once __DIR__ . '/autoloader.php';
	hypeInbox()->boot();
} catch (Exception $ex) {
	register_error($ex->getMessage());
}
