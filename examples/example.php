<?php

if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=UTF-8', true, 400);
    exit("This script only works on php-cli.\n");
}
if (DIRECTORY_SEPARATOR !== '/') {
    fwrite(STDERR, "Windows is not supported.\n");
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$master = new mpyw\HyperBuiltinServer\Master($loop, __DIR__);
$master->addListener('127.0.0.1', 8080, false);
$master->addListener('127.0.0.1', 8081, true);
$loop->run();
