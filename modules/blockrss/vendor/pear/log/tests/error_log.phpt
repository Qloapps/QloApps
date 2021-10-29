--TEST--
Log: Error_Log Handler
--SKIPIF--
<?php if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') die("skip\n"); ?>
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

/* Default Configuration */
$logger1 = Log::singleton('error_log', PEAR_LOG_TYPE_SYSTEM, 'ident');
for ($i = 0; $i < 3; $i++) {
	$logger1->log("Log entry $i");
}

/* Custom line format */
$conf = array('lineFormat' => '%2$s: [%3$s] %4$s');
$logger2 = Log::singleton('error_log', PEAR_LOG_TYPE_SYSTEM, 'ident', $conf);
for ($i = 0; $i < 3; $i++) {
	$logger2->log("Log entry $i");
}

--EXPECT--
ident: Log entry 0
ident: Log entry 1
ident: Log entry 2
ident: [info] Log entry 0
ident: [info] Log entry 1
ident: [info] Log entry 2
