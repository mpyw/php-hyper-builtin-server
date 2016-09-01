<?php

namespace mpyw\HyperBuiltinServer;
use mpyw\HyperBuiltinServer\Internal\ConnectionHandler;
use mpyw\HyperBuiltinServer\Internal\Queue;
use React\EventLoop\LoopInterface;
use React\Socket\Server;

class Master
{
    public $loop;
    public $queue;

    public function __construct(LoopInterface $loop, array $processes)
    {
        $this->loop = $loop;
        $this->queue = new Queue($processes);
    }

    public function addListener($host = '127.0.0.1', $port = 8080, $use_ssl = false, $cert = null)
    {
        $proxy = new Server($this->loop);
        $proxy->on('connection', new ConnectionHandler($this));
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
