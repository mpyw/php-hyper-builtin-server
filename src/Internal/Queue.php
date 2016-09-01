<?php

namespace mpyw\HyperBuiltinServer\Internal;

class Queue
{
    protected $processes = [];
    protected $promisors = [];

    public function __construct(array $processes)
    {
        if (!$processes) {
            throw new \LengthException('At least 1 process required.');
        }
        array_walk($processes, function (BuiltinServer $process) {
            $this->processes[spl_object_hash($process)] = $process;
        });
    }

    public function executeAsync(\Closure $promisor)
    {
        $this->processes
        ? $this->executeImmediate($promisor)
        : $this->executeReserved($promisor);
    }

    protected function executeImmediate(\Closure $promisor)
    {
        $process = current($this->processes);
        unset($this->processes[spl_object_hash($process)]);
        $promisor($process)->always(function () use ($process) {
            $this->processes[spl_object_hash($process)] = $process;
            $this->dequeue();
        });
    }

    protected function executeReserved(\Closure $promisor)
    {
        $this->promisors[] = $promisor;
    }

    protected function dequeue()
    {
        if ($promisor = array_shift($this->promisors)) {
            $this->executeImmediate($promisor);
        }
    }
}
