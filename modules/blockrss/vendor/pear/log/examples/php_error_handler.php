<?php

require_once 'Log.php';

function errorHandler($code, $message, $file, $line)
{
    global $logger;

    /* Map the PHP error to a Log priority. */
    switch ($code) {
    case E_WARNING:
    case E_USER_WARNING:
        $priority = PEAR_LOG_WARNING;
        break;
    case E_NOTICE:
    case E_USER_NOTICE:
        $priority = PEAR_LOG_NOTICE;
        break;
    case E_ERROR:
    case E_USER_ERROR:
        $priority = PEAR_LOG_ERR;
        break;
    default:
        $priotity = PEAR_LOG_INFO;
    }

    $logger->log($message . ' in ' . $file . ' at line ' . $line,
                 $priority);
}

$logger = &Log::singleton('console', '', 'ident');

set_error_handler('errorHandler');
trigger_error('This is an information log message.', E_USER_NOTICE);
