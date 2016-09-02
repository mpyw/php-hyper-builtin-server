<?php

namespace mpyw\HyperBuiltinServer;
use mpyw\HyperBuiltinServer\Internal\BuiltinServer;
use React\EventLoop\LoopInterface;
use React\Stream\Stream;
use React\Promise\Deferred;
use React\Promise\RejectedPromise;

class BuiltinServerFactory
{
    protected $loop;
    protected $stderr;
    protected $php = PHP_BINARY;
    protected $retry = 5;

    public function __construct(LoopInterface $loop, $retry_count = 5, $php_command = PHP_BINARY)
    {
        $this->loop = $loop;
        $this->stderr = new Stream(fopen('php://stderr', 'wb'), $this->loop);
        $this->php = $php_command;
        $this->retry = $retry_count;
    }

    protected function createInternalAsync($host, $docroot, $router)
    {
        $deferred = new Deferred;
        $process = new BuiltinServer($host, $docroot, $router, $this->php);

        $process->start($this->loop);
        $process->on('exit', function ($code) use ($deferred) {
            $this->stderr->write("Process exit with code $code\n");
            $deferred->reject();
        });

        $process->stdin->close();
        $process->stdout->close();
        $process->stderr->on('data', function ($output) use ($deferred) {
            $this->stderr->write($output);
            $deferred->reject();
        });

        $timer = new Deferred;
        $this->loop->addTimer(0.05, function () use ($timer, $process) {
            if (DIRECTORY_SEPARATOR === '\\') {
                // Pipes opened by proc_open() can break stream_select() loop in Windows.
                // This fix might do the trick...
                $process->stderr->close();
            }
            $timer->resolve($process);
        });

        return \React\Promise\race([
            $deferred->promise(),
            $timer->promise(),
        ])->then(null, function () use ($process) {
            $process->terminate();
            return new RejectedPromise;
        });
    }

    protected function createInternalWithRetryAsync($host, $docroot, $router, $retry)
    {
        return $this
        ->createInternalAsync($host, $docroot, $router)
        ->then(null, function () use ($host, $docroot, $router, $retry) {
            if ($retry < 1) {
                throw new \RuntimeException('Failed to launch server.');
            }
            return $this->createInternalWithRetryAsync($host, $docroot, $router, $retry - 1);
        });
    }

    public function createAsync($host = '127.0.0.1', $docroot = null, $router = null)
    {
        return $this->createInternalWithRetryAsync($host, $docroot, $router, $this->retry);
    }

    public function createMultipleAsync($n, $host = '127.0.0.1', $docroot = null, $router = null)
    {
        $promises = [];
        for ($i = 0; $i < $n; ++$i) {
            $promises[] = $this->createAsync($host, $docroot, $router);
        }
        return \React\Promise\all($promises);
    }
}
