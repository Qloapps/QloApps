<?php

require_once 'Log.php';

$conf = array('mode' => 0600, 'timeFormat' => '%X %x');
$logger = &Log::singleton('file', 'out.log', 'ident', $conf);
for ($i = 0; $i < 10; $i++) {
    $logger->log("Log entry $i");
}
