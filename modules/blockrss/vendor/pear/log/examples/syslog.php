<?php

require_once 'Log.php';

$logger = &Log::singleton('syslog', LOG_LOCAL0, 'ident');
for ($i = 0; $i < 10; $i++) {
    $logger->log("Log entry $i");
}
