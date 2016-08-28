<?php

namespace mpyw\HyperBuiltinServer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BuiltinServer extends Process
{
    private $host;
    private $port;
    private $directory;

    public function __construct($directory = '.', $host = '127.0.0.1')
    {
        $port = mt_rand(49152, 65535);
        $eschostport = escapeshellarg("$host:$port");
        $escdir = escapeshellarg($directory);
        parent::__construct("cd $escdir && php -S $eschostport");
        $this->start(function ($type, $buffer) {
            fwrite(STDERR, $buffer);
        });
        $this->directory = $directory;
        $this->host = $host;
        $this->port = $port;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getHostPort()
    {
        return "{$this->getHost()}:{$this->getPort()}";
    }

    public function getSocketSpec()
    {
        return "tcp://{$this->getHostPort()}";
    }

    public function getSocketClient()
    {
        $socket = @stream_socket_client(
            $this->getSocketSpec(),
            $errno,
            $errstr,
            STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT
        );
        if ($socket === false) {
            throw new \RuntimeException(error_get_last()['message']);
        }
        stream_set_blocking($socket, 0);
        return $socket;
    }
}
