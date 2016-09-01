<?php

namespace mpyw\HyperBuiltinServer\Internal;
use mpyw\HyperBuiltinServer\Master;
use React\Socket\ConnectionInterface;
use React\Stream\Stream;
use React\Promise\Deferred;

class ConnectionHandler
{
    protected $master;

    public function __construct(Master $master)
    {
        $this->master = $master;
    }

    public function __invoke(ConnectionInterface $conn)
    {
        $sink = new Sink($conn);
        $this->master->queue->executeAsync(function (BuiltinServer $process) use ($conn, $sink) {
            $deferred = new Deferred;
            try {
                $child = new Stream($process->getSocketClient(), $this->master->loop);
                $sink->pipe($child);
                $child->pipe($conn);
                $child->on('close', function () use ($deferred) {
                    $deferred->resolve();
                });
            } catch (\RuntimeException $e) {
                $conn->write("HTTP/1.0 502 Bad Gateway\r\n");
                $conn->end("\r\n");
                $deferred->resolve();
            }
            return $deferred->promise();
        });
    }
}
