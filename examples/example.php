<?php

set_time_limit(0);

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
$factory = new mpyw\HyperBuiltinServer\BuiltinServerFactory($loop);
$factory
->createMultipleAsync(6)
->then(function ($processes) use ($loop) {
    $master = new mpyw\HyperBuiltinServer\Master($loop, $processes);
    $master->addListener('127.0.0.1', 8080, false);
    $master->addListener('127.0.0.1', 8081, true);
})->done();
$loop->run();
