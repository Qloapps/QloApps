<?php

require_once 'Log.php';

$logger = &Log::singleton('error_log', PEAR_LOG_TYPE_SYSTEM, 'ident');
for ($i = 0; $i < 10; $i++) {
    $logger->log("Log entry $i");
}
