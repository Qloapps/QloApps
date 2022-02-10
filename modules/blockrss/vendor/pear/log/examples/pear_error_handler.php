<?php

require_once 'Log.php';

function errorHandler($error)
{
    global $logger;

    $message = $error->getMessage();

    if (!empty($error->backtrace[1]['file'])) {
        $message .= ' (' . $error->backtrace[1]['file'];
        if (!empty($error->backtrace[1]['line'])) {
            $message .= ' at line ' . $error->backtrace[1]['line'];
        }
        $message .= ')';
    }

    $logger->log($message, $error->code);
}

$logger = &Log::singleton('console', '', 'ident');

PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'errorHandler');
PEAR::raiseError('This is an information log message.', PEAR_LOG_INFO);
