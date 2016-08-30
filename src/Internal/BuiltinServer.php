<?php

namespace mpyw\HyperBuiltinServer\Internal;
use React\EventLoop\LoopInterface;
use React\ChildProcess\Process;

class BuiltinServer extends Process
{
    protected $host;
    protected $port;

    public function __construct($host = '127.0.0.1', $root = null, $php = 'php')
    {
        $port = mt_rand(49152, 65535);
        $escphp = escapeshellarg($php);
        $eschostport = escapeshellarg("$host:$port");
        $escroot = $root !== null ? escapeshellarg($root) : '';

        parent::__construct("script -q /dev/null $escphp -S $eschostport $root");

        $this->host = $host;
        $this->port = $port;
    }

    public function getSocketClient()
    {
        $socket = @stream_socket_client("tcp://{$this->host}:{$this->port}");
        if ($socket === false) {
            throw new \RuntimeException(error_get_last()['message']);
        }
        return $socket;
    }
}
