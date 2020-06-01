<?php

namespace mpyw\HyperBuiltinServer\Internal;
use React\Socket\ConnectionInterface;
use React\Stream\WritableStreamInterface;

class Sink
{
    protected $src;
    protected $dst;
    protected $buffer = '';
    protected $end = false;

    public function __construct(ConnectionInterface $src)
    {
        $this->src = $src;
        $src->on('data', function ($data) {
            $this->buffer .= $data;
            if ($this->dst) {
                $this->dst->write($this->buffer);
                $this->buffer = '';
            }
        });
        $src->on('end', function () {
            $this->end = true;
            if ($this->dst) {
                $this->dst->end();
            }
        });
        $src->on('error', function () {
            $this->end = true;
            if ($this->dst) {
                $this->dst->end();
            }
        });
    }

    public function pipe(WritableStreamInterface $dst)
    {
        $this->dst = $dst;
        if ($this->buffer !== '') {
            $this->dst->write($this->buffer);
            $this->buffer = '';
        }
        if ($this->end) {
            $this->dst->end();
        }
    }
}
