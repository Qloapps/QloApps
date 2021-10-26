<?php

require_once 'Log.php';

$console = &Log::singleton('console', '', 'TEST');
$file = &Log::singleton('file', 'out.log', 'TEST');

$composite = &Log::singleton('composite');
$composite->addChild($console);
$composite->addChild($file);

$composite->log('This event will be logged to both handlers.');
