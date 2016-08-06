<?php

namespace mpyw\HyperBuiltinServer;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

trait CommandTrait
{
    private function fork()
    {
        if (-1 === $pid = pcntl_fork()) {
            $this->critical('Failed to fork');
            throw new \UnexpectedValueException;
        }
        return $pid;
    }

    private function execCommand($cmd, $strict = true)
    {
        list($outputs, $status) = Utils::execWithStatus($cmd);
        if ($strict && $status !== 0) {
            $this->critical("Command execution failed: $cmd");
            throw new \UnexpectedValueException;
        }
        return $outputs;
    }
}
