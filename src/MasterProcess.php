<?php

namespace mpyw\HyperBuiltinServer;

declare(ticks=1);

class MasterProcess
{
    use LoggerTrait, CommandTrait;

    private $logger;
    private $pid;
    private $port;
    private $directory;

    public function __construct($directory = '.')
    {
        SignalHandler::enable();
        $this->registerLogger('MasterProcess');
        $this->directory = $directory;
    }

    public function listen()
    {
        $pid = $this->fork();
        $process = new ProxyProcess($this->directory, $pid === 0);
        $process->listen();
        if ($pid === 0) {
            exit;
        }
    }
}
