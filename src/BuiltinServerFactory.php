<?php

namespace mpyw\HyperBuiltinServer;
use mpyw\HyperBuiltinServer\Internal\BuiltinServer;
use React\EventLoop\LoopInterface;
use React\Stream\Stream;
use React\Promise\Deferred;
use React\Promise\Promise;

class BuiltinServerFactory
{
    protected $loop;
    protected $stderr;
    protected $php = 'php';
    protected $retry = 5;

    public function __construct(LoopInterface $loop, $retry_count = 5, $php_command = 'php')
    {
        $this->loop = $loop;
        $this->stderr = new Stream(fopen('php://stderr', 'wb'), $this->loop);
        $this->php = $php_command;
        $this->retry = $retry_count;
    }

    protected function createInternalAsync($host, $root)
    {
        $deferred = new Deferred;
        $process = new BuiltinServer($host, $root, $this->php);
        $process->start($this->loop);

        $process->stdout->on('data', function ($output) use ($deferred, $process) {
            $this->stderr->write($output);
            $deferred->resolve($process);
        });
        $process->stderr->on('data', function ($output) use ($deferred) {
            $this->stderr->write($output);
            $deferred->reject();
        });
        $process->on('exit', function ($code) use ($deferred) {
            $this->stderr->write("Process exit with code $code\n");
            $deferred->reject();
        });

        return $deferred->promise();
    }

    protected function createInternalWithRetryAsync($host, $root, $retry)
    {
        return $this
        ->createInternalAsync($host, $root)
        ->then(null, function ($e) use ($retry, $host, $root) {
            if ($retry < 1) {
                throw new \RuntimeException('Failed to launch server.');
            }
            return $this->createInternalWithRetryAsync($host, $root, $retry - 1);
        });
    }

    public function createAsync($host = '127.0.0.1', $root = null)
    {
        return $this->createInternalWithRetryAsync($host, $root, $this->retry);
    }

    public function createMultipleAsync($n, $host = '127.0.0.1', $root = null)
    {
        $promises = [];
        for ($i = 0; $i < $n; ++$i) {
            $promises[] = $this->createAsync($host, $root);
        }
        return \React\Promise\all($promises);
    }
}
