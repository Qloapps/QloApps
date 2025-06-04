<?php

require __DIR__ . '/../vendor/autoload.php';

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;

srand(0);

$reader = new Reader('GeoIP2-City.mmdb');
$count = 500000;
$startTime = microtime(true);
for ($i = 0; $i < $count; ++$i) {
    $ip = long2ip(rand(0, 2 ** 32 - 1));

    try {
        $t = $reader->city($ip);
    } catch (AddressNotFoundException $e) {
    }
    if ($i % 10000 === 0) {
        echo $i . ' ' . $ip . "\n";
    }
}
$endTime = microtime(true);

$duration = $endTime - $startTime;
echo 'Requests per second: ' . $count / $duration . "\n";
