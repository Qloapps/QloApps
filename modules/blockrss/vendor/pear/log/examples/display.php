<?php

require_once 'Log.php';

$conf = array('error_prepend' => '<font color="#ff0000"><tt>',
              'error_append'  => '</tt></font>');
$logger = &Log::singleton('display', '', '', $conf, PEAR_LOG_DEBUG);
for ($i = 0; $i < 10; $i++) {
    $logger->log("Log entry $i");
}
