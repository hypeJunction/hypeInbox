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

require_once "$engine/load.php";
require_once dirname(dirname(__DIR__)) . '/hypeApps/lib/autoloader.php';
require_once dirname(__DIR__) . "/autoloader.php";