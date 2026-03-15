<?php

$rootDirectory = dirname(__DIR__, 2);
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$targetPath = $rootDirectory.$requestPath;

if ($requestPath !== '/' && is_file($targetPath)) {
    return false;
}

require $rootDirectory.'/index.php';