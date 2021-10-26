<?php

require_once 'Log.php';

$logger = &Log::singleton('firebug', '',
                          'PHP',
                          array('buffering' => true),
                          PEAR_LOG_DEBUG);

for ($i = 0; $i < 10; $i++) {
    $logger->log("Log entry $i");
}
