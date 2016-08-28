<?php

namespace mpyw\HyperBuiltinServer;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server;
use React\Socket\ServerInterface;
use React\Stream\Stream;

class Master
{
    private $loop;
    private $children = [];
    private $using = [];

    public function __construct(LoopInterface $loop, $directory = '.', $number_of_children = 10)
    {
        $this->loop = $loop;
        $this->launchChildren($directory, $number_of_children);
    }

    public function addListener($host = '127.0.0.1', $port = 8080, $use_ssl = false)
    {
        $proxy = $use_ssl ? new SecureServer($this->loop) : new Server($this->loop);
        $this->launchProxy($proxy);
        $proxy->listen($port, $host);
    }

    private function launchChildren($directory, $number_of_children)
    {
        $failed_counts = array_fill(0, $number_of_children, 0);
        $max_fails = 5;

        do {

            for ($i = 0; $i < $number_of_children; ++$i) {
                if (!empty($this->children[$i])) {
                    continue;
                }
                $this->children[$i] = new BuiltinServer($directory);
                $this->using[$i] = false;
            }

            usleep(50000);

            for ($i = 0; $i < $number_of_children; ++$i) {
                if ($this->children[$i]->isRunning()) {
                    continue;
                }
                $this->children[$i] = false;
                if ($failed_counts[$i] < $max_fails) {
                    ++$failed_counts[$i];
                    continue;
                }
                throw new \RuntimeException('Failed to launch BuiltinServer');
            }

        } while (false !== array_search(false, $this->children, true));
    }

    private function launchProxy(ServerInterface $proxy)
    {
        $proxy->on('connection', function (ConnectionInterface $conn) {

            $try = function () use ($conn) {
                if (false === $i = array_search(false, $this->using, true)) {
                    return false;
                }
                try {
                    $child = new Stream($this->children[$i]->getSocketClient(), $this->loop);
                    $conn->pipe($child);
                    $child->pipe($conn);
                    $child->on('close', function () use ($i) {
                        $this->using[$i] = false;
                    });
                    $this->using[$i] = true;
                } catch (\RuntimeException $e) {
                    $conn->write("HTTP/1.0 502 Bad Gateway\r\n");
                    $conn->end("\r\n");
                }
                return true;
            };

            if ($try()) return;
            $this->loop->addTimer(1, $retry = function () use ($try, &$retry) {
                if ($try()) return;
                $this->loop->addTimer(1, $retry);
            });

        });
    }
}
