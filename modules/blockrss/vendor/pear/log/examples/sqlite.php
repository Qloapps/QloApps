<?php

require_once 'Log.php';

/* Creating a new database connection. */
$conf = array('filename' => 'log.db', 'mode' => 0666, 'persistent' => true);
$logger =& Log::factory('sqlite', 'log_table', 'ident', $conf);
$logger->log('logging an event', PEAR_LOG_WARNING);

/* Using an existing database connection. */
$db = sqlite_open('log.db', 0666, $error);
$logger =& Log::factory('sqlite', 'log_table', 'ident', $db);
$logger->log('logging an event', PEAR_LOG_WARNING);
sqlite_close($db);
