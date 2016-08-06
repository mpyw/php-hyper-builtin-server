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
        $escdir = escapeshellarg($directory);
        $pid = (int)current($this->execCommand("cd $escdir; php -S localhost:$port 1>&2 & printf \$!"));
        if ($pid === 0) {
            throw new \UnexpectedValueException;
        }
        $this->info("Listening on localhost:$port");
        $this->pid = $pid;
        $this->port = $port;
        usleep(50000);
    }

    public function __destruct()
    {
        if ($this->pid !== 0) {
            posix_kill($this->pid, SIGKILL);
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
