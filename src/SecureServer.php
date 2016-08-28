<?php

namespace mpyw\HyperBuiltinServer;

use React\Socket\Server;
use React\Socket\ServerInterface;
use React\Socket\ConnectionException;

class SecureServer extends Server implements ServerInterface
{
    protected function getLoop()
    {
        $closure = function () {
            return $this->loop;
        };
        $closure = $closure->bindTo($this, get_parent_class());
        return $closure();
    }

    public function listen($port, $host = '127.0.0.1')
    {
        if (strpos($host, ':') !== false) {
            // enclose IPv6 addresses in square brackets before appending port
            $host = '[' . $host . ']';
        }

        $this->master = @stream_socket_server(
            "ssl://$host:$port",
            $errno,
            $errstr,
            STREAM_SERVER_BIND | STREAM_SERVER_LISTEN,
            stream_context_create([
                'ssl' => [
                    'local_cert' => __DIR__ . '/certificate.pem',
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                ],
            ])
        );
        if (false === $this->master) {
            $message = "Could not bind to ssl://$host:$port: $errstr";
            throw new ConnectionException($message, $errno);
        }
        stream_set_blocking($this->master, 0);

        $this->getLoop()->addReadStream($this->master, function ($master) {
            $newSocket = @stream_socket_accept($master);
            if (false === $newSocket) {
                $this->emit('error', [new \RuntimeException('Error accepting new connection')]);
                return;
            }
            $this->handleConnection($newSocket);
        });
    }
}
