--TEST--
Log: Composite Handler
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

function printOpenStates($children) {
    foreach ($children as $child) {
        $state = ($child->_opened) ? 'OPEN' : 'CLOSED';
        echo "$child->_ident : $state\n";
    }
}

function printIdents($children) {
    foreach ($children as $child) {
        echo "$child->_ident\n";
    }
}

function testPriority($composite, $priority) {
    $log = new Log;
    $name = $log->priorityToString($priority);
    $success = $composite->log($name, $priority);
    echo "$name : " . (($success) ? 'GOOD' : 'BAD') . "\n";
}

/* Create three handlers with different priority masks. */
$conf = array('lineFormat' => '%2$s [%3$s] %4$s');
$children = array(
    Log::factory('console', '', 'CONSOLE1', $conf),
    Log::factory('console', '', 'CONSOLE2', $conf),
    Log::factory('console', '', 'CONSOLE3', $conf)
);

$children[0]->setMask(Log::MASK(PEAR_LOG_DEBUG));
$children[1]->setMask(Log::MASK(PEAR_LOG_INFO));
$children[2]->setMask(Log::MASK(PEAR_LOG_ERR));

$composite = Log::singleton('composite');
$composite->addChild($children[0]);
$composite->addChild($children[1]);
$composite->addChild($children[2]);

/* Verify that all of the children are initially closed. */
printOpenStates($children);

/* Verify that the composite handler's open() opens all of the children. */
$composite->open();
printOpenStates($children);

/* Verify that the composite handler's close() closes all of the children. */
$composite->close();
printOpenStates($children);

/* Verify the log results at different priorities. */
testPriority($composite, PEAR_LOG_DEBUG);
printOpenStates($children);
testPriority($composite, PEAR_LOG_INFO);
printOpenStates($children);
testPriority($composite, PEAR_LOG_ERR);
printOpenStates($children);

/* Verify that changing the ident affects all children. */
$composite->setIdent('IDENT');
printIdents($children);

--EXPECT--
CONSOLE1 : CLOSED
CONSOLE2 : CLOSED
CONSOLE3 : CLOSED
CONSOLE1 : OPEN
CONSOLE2 : OPEN
CONSOLE3 : OPEN
CONSOLE1 : CLOSED
CONSOLE2 : CLOSED
CONSOLE3 : CLOSED
CONSOLE1 [debug] debug
debug : GOOD
CONSOLE1 : OPEN
CONSOLE2 : CLOSED
CONSOLE3 : CLOSED
CONSOLE2 [info] info
info : GOOD
CONSOLE1 : OPEN
CONSOLE2 : OPEN
CONSOLE3 : CLOSED
CONSOLE3 [error] error
error : GOOD
CONSOLE1 : OPEN
CONSOLE2 : OPEN
CONSOLE3 : OPEN
IDENT
IDENT
IDENT
