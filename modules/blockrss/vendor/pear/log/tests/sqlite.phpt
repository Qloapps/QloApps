--TEST--
Log: Sqlite Handler
--SKIPIF--
<?php if (!function_exists('sqlite_open')) die("skip\n"); ?>
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'sqlite-log.db';
if (file_exists($filename)) unlink($filename);

$db = sqlite_open($filename, 0666, $error);
$ident = time();
$message = 'logging an event';
$logger = Log::factory('sqlite', 'log_table', $ident, $db);
$logger->log($message, PEAR_LOG_WARNING);

$q = "SELECT message FROM log_table WHERE ident='$ident'";
$res = sqlite_query($db, $q);
if (sqlite_fetch_string($res) == $message) {
	echo 'ok';
}

sqlite_close($db);
if (file_exists($filename)) unlink($filename);

--EXPECT--
ok
