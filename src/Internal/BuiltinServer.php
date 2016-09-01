<?php

namespace mpyw\HyperBuiltinServer\Internal;
use React\ChildProcess\Process;

class BuiltinServer extends Process
{
    protected $host;
    protected $port;

    public function __construct($host = '127.0.0.1', $docroot = null, $router = null, $php = PHP_BINARY)
    {
        $port = mt_rand(49152, 65535);
        $command = implode(' ', array_filter([
            escapeshellarg($php),
            '-S',
            escapeshellarg("$host:$port"),
            $docroot !== null ? ('-t ' . escapeshellarg($docroot)) : null,
            $router !== null ? escapeshellarg($router) : null,
        ], 'is_string'));
        parent::__construct($command, null, null, ['bypass_shell' => true]);
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
