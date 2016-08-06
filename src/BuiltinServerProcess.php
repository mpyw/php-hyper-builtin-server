<?php

namespace mpyw\HyperBuiltinServer;

declare(ticks=1);

class BuiltinServerProcess
{
    use LoggerTrait, CommandTrait;

    private $logger;
    private $pid;
    private $port;
    private $directory;

    public function __construct($directory = '.')
    {
        SignalHandler::enable();
        $this->registerLogger('BuiltinServerProcess');
        do {
            $port = mt_rand(49152, 65535);
            $cmd = "lsof -i4TCP:$port | perl -ane '{if(\$F[8]=~/^[^:]+:(\d+)/){print \$1}}'";
            $outputs = $this->execCommand($cmd);
        } while (!empty($outputs));
        $pid = $this->fork($port);
        if (!empty($pid)) {
            $this->pid = $pid;
            $this->port = $port;
            usleep(50000);
            return;
        }
        $this->info("Listening on localhost:$port");
        $escdir = escapeshellarg($directory);
        $this->execCommand("cd $escdir; php -S localhost:$port >/dev/null 2>&1", false);
        exit;
    }

    public function __destruct()
    {
        if (empty($this->port)) {
            return;
        }
        $search = "php -S localhost:$this->port";
        $outputs = $this->execCommand("ps | grep '$search'");
        foreach ($outputs as $item) {
            if (
                strpos($item, 'grep') === false &&
                strpos($item, 'sh') === false &&
                strpos($item, $search) !== false
            ) {
                $pid = (int)$item;
                $this->execCommand("kill $pid 2>/dev/null");
                $this->info("Killed localhost:$this->port");
                return;
            }
        }
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getHostPort()
    {
        return "localhost:{$this->getPort()}";
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
            $errstr
        );
        if (!$socket) {
            $this->error("$errstr ($errno)");
        }
        return $socket;
    }
}
