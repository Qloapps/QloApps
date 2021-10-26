--TEST--
Log: _extractMessage() [Zend Engine 2.2]
--SKIPIF--
<?php if (version_compare(zend_version(), "2.2.0", "<")) die('skip'); ?>
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

$conf = array('lineFormat' => '%2$s [%3$s] %4$s');
$logger = Log::singleton('console', '', 'ident', $conf);

/* Logging a regular string. */
$logger->log('String');

/* Logging a bare object. */
class BareObject {}
$logger->log(new BareObject());

/* Logging an object with a getMessage() method. */
class GetMessageObject { function getMessage() { return "getMessage"; } }
$logger->log(new GetMessageObject());

/* Logging an object with a toString() method. */
class ToStringObject { function toString() { return "toString"; } }
$logger->log(new ToStringObject());

/* Logging an object with a __toString() method using casting. */
class CastableObject { function __toString() { return "__toString"; } }
$logger->log(new CastableObject());

/* Logging a PEAR_Error object. */
require_once 'PEAR.php';
$logger->log(new PEAR_Error('PEAR_Error object', 100));

/* Logging an array. */
$logger->log(array(1, 2, 'three' => 3));

/* Logging an array with scalar 'message' keys. */
$logger->log(array('message' => 'Message Key'));
$logger->log(array('message' => 50));

/* Logging an array with a non-scalar 'message' key. */
$logger->log(array('message' => array(1, 2, 3)));

--EXPECT--
ident [info] String
ident [info] BareObject::__set_state(array(
))
ident [info] getMessage
ident [info] toString
ident [info] __toString
ident [info] PEAR_Error object
ident [info] array (
  0 => 1,
  1 => 2,
  'three' => 3,
)
ident [info] Message Key
ident [info] 50
ident [info] array (
  0 => 1,
  1 => 2,
  2 => 3,
)
