--TEST--
Log: Levels
--INI--
date.timezone=UTC
--FILE--
<?php

require_once 'Log.php';

function verify($exp, $msg)
{
    echo $msg . ': ';
    echo ($exp) ? 'pass' : 'fail';
    echo "\n";
}

function testLevels($mask)
{
    echo "Mask: " . ($mask & 0xffff) . "\n";

    for ($priority = PEAR_LOG_EMERG; $priority <= PEAR_LOG_DEBUG; $priority++) {
        $masked = (Log::MASK($priority) & $mask);
        echo "Priority $priority: ";
        echo($masked) ? "masked\n" : "unmasked\n";
    }

    echo "\n";
}

testLevels(PEAR_LOG_NONE);
testLevels(PEAR_LOG_ALL);
testLevels(Log::MIN(PEAR_LOG_WARNING));
testLevels(Log::MAX(PEAR_LOG_WARNING));

--EXPECT--
Mask: 0
Priority 0: unmasked
Priority 1: unmasked
Priority 2: unmasked
Priority 3: unmasked
Priority 4: unmasked
Priority 5: unmasked
Priority 6: unmasked
Priority 7: unmasked

Mask: 65535
Priority 0: masked
Priority 1: masked
Priority 2: masked
Priority 3: masked
Priority 4: masked
Priority 5: masked
Priority 6: masked
Priority 7: masked

Mask: 65520
Priority 0: unmasked
Priority 1: unmasked
Priority 2: unmasked
Priority 3: unmasked
Priority 4: masked
Priority 5: masked
Priority 6: masked
Priority 7: masked

Mask: 31
Priority 0: masked
Priority 1: masked
Priority 2: masked
Priority 3: masked
Priority 4: masked
Priority 5: unmasked
Priority 6: unmasked
Priority 7: unmasked
