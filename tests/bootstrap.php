<?php

date_default_timezone_set('Europe/Berlin');

error_reporting(E_ALL | E_STRICT);

global $CONFIG;
$CONFIG = (object) array(
			'dbprefix' => 'elgg_',
			'boot_complete' => false,
			'wwwroot' => 'http://localhost/',
);

$engine = dirname(dirname(dirname(dirname(__FILE__)))) . '/engine';

require_once "$engine/lib/autoloader.php";
require_once "$engine/lib/elgglib.php";
require_once "$engine/lib/sessions.php";

require_once dirname(__DIR__) . "/lib/autoloader.php";
_elgg_services()->autoloadManager->addClasses(dirname(__DIR__) . "/classes/");

function elgg_get_config($name) {
	global $CONFIG;
	return $CONFIG->$name;
}

function sanitize_string($value) {
	return $value;
}

function get_entity($guid) {
	if (!$guid || !is_int($guid)) {
		return false;
	}
	return new ElggObject();
}
function elgg_entity_exists($guid = null) {
	return ($guid && is_int($guid));
}