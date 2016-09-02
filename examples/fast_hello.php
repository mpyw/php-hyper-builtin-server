<?php

header('Cache-Control: no-store'); // Very important role for Google Chrome
header('Content-Type: text/event-stream; charset=UTF-8');

while (ob_get_level()) ob_end_clean();

var_dump(compact('_GET', '_POST'));
var_dump($_SERVER['SERVER_PORT']);
flush();

usleep(50000);

echo "Hello (1)\n";
flush();

usleep(50000);

echo "Hello (2)\n";
flush();

usleep(50000);

echo "Hello (3)\n";
flush();
