<?php

namespace mpyw\HyperBuiltinServer;

declare(ticks=1);

class SignalHandler
{
    use LoggerTrait;

    private $logger;
    private static $self;

    public static function enable()
    {
        if (!self::$self) {
            self::$self = new self();
        }
    }

    private function __construct()
    {
        $this->registerLogger('SignalHandler');
        pcntl_signal(SIGCHLD, [$this, 'handle']);
    }

    public function handle($sig)
    {
        if ($sig !== SIGCHLD) {
            return;
        }
        $ignore = null;
        while (($rc = pcntl_waitpid(-1, $ignore, WNOHANG)) > 0);
        if ($rc !== -1 || pcntl_get_last_error() === PCNTL_ECHILD) {
            return;
        }
        $this->error('waitpid() failed: ' . pcntl_strerror(pcntl_get_last_error()));
    }
}
