--TEST--
Log: Masks
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

/* Levels */
for ($level = PEAR_LOG_EMERG; $level <= PEAR_LOG_DEBUG; $level++) {

	printf("Level %d: 0x%08x, 0x%08x, 0x%08x\n",
		$level,
		Log::MASK($level),
		Log::MIN($level),
		Log::MAX($level));
}
echo "\n";

/* Mask */
$conf = array('lineFormat' => '%2$s [%3$s] %4$s');
$logger = Log::singleton('console', '', 'ident', $conf);

$logger->setMask(Log::MAX(PEAR_LOG_INFO));
$logger->info('Info 1');
$logger->setMask(Log::MAX(PEAR_LOG_ERR));
$logger->info('Info 2');

--EXPECT--
Level 0: 0x00000001, 0xffffffff, 0x00000001
Level 1: 0x00000002, 0xfffffffe, 0x00000003
Level 2: 0x00000004, 0xfffffffc, 0x00000007
Level 3: 0x00000008, 0xfffffff8, 0x0000000f
Level 4: 0x00000010, 0xfffffff0, 0x0000001f
Level 5: 0x00000020, 0xffffffe0, 0x0000003f
Level 6: 0x00000040, 0xffffffc0, 0x0000007f
Level 7: 0x00000080, 0xffffff80, 0x000000ff

ident [info] Info 1
