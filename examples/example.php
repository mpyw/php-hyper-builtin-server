<?php

declare(ticks=1);
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
$ps = new mpyw\HyperBuiltinServer\MasterProcess(__DIR__);
$ps->listen();
