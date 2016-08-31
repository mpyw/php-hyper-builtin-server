<?php

namespace mpyw\HyperBuiltinServer;
use mpyw\HyperBuiltinServer\Internal\BuiltinServer;
use mpyw\HyperBuiltinServer\Internal\ConnectionHandler;
use React\EventLoop\LoopInterface;
use React\Socket\Server;

class Master
{
    public $loop;
    public $children = [];
    public $using = [];

    public function __construct(LoopInterface $loop, array $processes)
    {
        if (!$processes) {
            throw new \LengthException('At least 1 process required.');
        }
        array_map(function (BuiltinServer $_) {}, $processes);
        $this->loop = $loop;
        $this->children = array_values($processes);
        $this->using = array_fill(0, count($processes), false);
    }

    public function addListener($host = '127.0.0.1', $port = 8080, $use_ssl = false, $cert = null)
    {
        $proxy = new Server($this->loop);
        $proxy->on('connection', new ConnectionHandler($this, $use_ssl));
        $context = !$use_ssl ? [] : [
            'ssl' => [
                'local_cert' => $cert === null ? (__DIR__ . '/../certificate.pem') : $cert,
                'allow_self_signed' => true,
                'verify_peer' => false,
            ],
        ];
        $proxy->listen($port, $host, $context);
    }
}
