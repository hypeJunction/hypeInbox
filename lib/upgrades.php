<?php

run_function_once('inbox_upgrade_20141123');

/**
 * Run upgraded activate.php
 */
function inbox_upgrade_20141123() {
	require_once dirname(dirname(__FILE__)) . '/activate.php';
}