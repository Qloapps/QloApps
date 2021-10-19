<?php

require_once 'Log.php';

$conf = array('subject' => 'Important Log Events');
$logger = &Log::singleton('mail', 'webmaster@example.com', 'ident', $conf);
for ($i = 0; $i < 10; $i++) {
    $logger->log("Log entry $i");
}
