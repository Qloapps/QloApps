<?php

require_once 'Log.php';

$conf = array('dsn' => 'pgsql://jon@localhost+unix/logs');
$logger = &Log::singleton('sql', 'log_table', 'ident', $conf);
for ($i = 0; $i < 10; $i++) {
    $logger->log("Log entry $i");
}
