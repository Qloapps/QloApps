--TEST--
Log: Priorities
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

$conf = array('lineFormat' => '[%3$s] %4$s');
$logger = Log::singleton('console', '', 'ident', $conf);

/* Log at the default PEAR_LOG_INFO level. */
$logger->log('Log message');

/* Set the default priority to PEAR_LOG_DEBUG. */
$logger->setPriority(PEAR_LOG_DEBUG);
$logger->log('Log message');

/* Verify that the getPriority() method also things we're at PEAR_LOG_DEBUG. */
$priority = $logger->priorityToString($logger->getPriority());
echo "$priority\n";

/* Verify that stringToPriority() can convert back to a constant. */
$priority = $logger->stringToPriority($priority);
echo "$priority\n";

--EXPECT--
[info] Log message
[debug] Log message
debug
7
