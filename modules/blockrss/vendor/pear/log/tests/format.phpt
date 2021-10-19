--TEST--
Log: Line Format
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

$conf = array('lineFormat' => '%{timestamp} %{ident} %{priority} %{message} %{file} %{line} %{function} %{class}');
$logger = Log::singleton('console', '', 'ident', $conf);
$logger->log('Message');

--EXPECTREGEX--
^\w{3} \d+ \d{2}:\d{2}:\d{2} ident info Message .*format\.php \d+ [\(\)\w]+$
