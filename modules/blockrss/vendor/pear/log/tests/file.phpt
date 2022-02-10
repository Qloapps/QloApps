--TEST--
Log: File Handler
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test.log';
if (file_exists($filename)) unlink($filename);

/* Write some entries to the log file. */
$conf = array('lineFormat' => '%2$s [%3$s] %4$s');
$logger = Log::singleton('file', $filename, '', $conf);

for ($i = 0; $i < 3; $i++) {
    $logger->log("Log entry $i");
}
$logger->close();

/* Dump the contents of the log file. */
echo file_get_contents($filename);

/* Clean up. */
if (file_exists($filename)) unlink($filename);

--EXPECT--
 [info] Log entry 0
 [info] Log entry 1
 [info] Log entry 2
