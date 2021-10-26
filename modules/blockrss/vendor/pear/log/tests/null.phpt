--TEST--
Log: Null Handler
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

$logger = Log::singleton('null');
for ($i = 0; $i < 3; $i++) {
	$logger->log("Log entry $i");
}

--EXPECT--
