<?php

namespace mpyw\HyperBuiltinServer\Internal;
use mpyw\HyperBuiltinServer\Master;
use React\Socket\ConnectionInterface;
use React\Stream\Stream;

class ConnectionHandler
{
    protected $master;
    protected $use_ssl = false;
    protected $handshake_completed = false;

    public function __construct(Master $master, $use_ssl = false)
    {
        $this->master = $master;
        $this->use_ssl = $use_ssl;
    }

    public function __invoke(ConnectionInterface $conn)
    {
        $this->handle($conn);
    }

    protected function handle(ConnectionInterface $conn)
    {
        if ($this->tryToHandle($conn)) {
            return;
        }
        $this->loop->addTimer(0.1, $retry = function () use ($conn, &$retry) {
            if ($this->tryToHandle($conn)) {
                return;
            }
            $this->loop->addTimer(0.1, $retry);
        });
    }

    protected function tryToHandle(ConnectionInterface $conn)
    {
        if (false === $i = array_search(false, $this->master->using, true)) {
            return false;
        }
        try {
            $child = new Stream($this->master->children[$i]->getSocketClient(), $this->master->loop);
            $conn->pipe($child);
            $child->pipe($conn);
            $child->on('close', function () use ($i) {
                $this->master->using[$i] = false;
            });
            $this->master->using[$i] = true;
        } catch (\RuntimeException $e) {
            $conn->write("HTTP/1.0 502 Bad Gateway\r\n");
            $conn->end("\r\n");
        }
        return true;
    }
}
