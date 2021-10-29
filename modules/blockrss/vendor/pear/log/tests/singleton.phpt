--TEST--
Log: Singleton
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

$console1 = Log::singleton('console');
$console2 = Log::singleton('console');

if (is_a($console1, 'Log_console') && is_a($console2, 'Log_console'))
{
	echo "Two Log_console objects.\n";
}

if ($console1->_id == $console2->_id) {
	echo "The objects have the same ID.\n";
}

--EXPECT--
Two Log_console objects.
The objects have the same ID.
