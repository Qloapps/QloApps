--TEST--
Log: Console Handler
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

$conf = array('lineFormat' => '%2$s [%3$s] %4$s');
$logger = Log::singleton('console', '', 'ident', $conf);
for ($i = 0; $i < 3; $i++) {
    $logger->log("Log entry $i");
}

echo "\n[Buffering / Flush Test]\n";
$conf = array('lineFormat' => '%2$s [%3$s] %4$s', 'buffering' => true);
$buffered_logger = Log::singleton('console', '', 'buffered', $conf);
for ($i = 0; $i < 3; $i++) {
    $buffered_logger->log("Pre-flush buffered log entry $i");
}
echo "Pre-flush\n";
$buffered_logger->flush();
echo "Post-flush\n";
for ($i = 0; $i < 3; $i++) {
    $buffered_logger->log("Post-flush buffered log entry $i");
}
echo "Shutdown\n";
$buffered_logger->close();

--EXPECT--
ident [info] Log entry 0
ident [info] Log entry 1
ident [info] Log entry 2

[Buffering / Flush Test]
Pre-flush
buffered [info] Pre-flush buffered log entry 0
buffered [info] Pre-flush buffered log entry 1
buffered [info] Pre-flush buffered log entry 2
Post-flush
Shutdown
buffered [info] Post-flush buffered log entry 0
buffered [info] Post-flush buffered log entry 1
buffered [info] Post-flush buffered log entry 2
