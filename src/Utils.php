<?php

namespace mpyw\HyperBuiltinServer;

declare(ticks=1);

class Utils
{
    public static function exec($cmd)
    {
        return self::execWithStatus($cmd)[0];
    }

    public static function execWithStatus($cmd)
    {
        exec($cmd, $outputs, $status);
        return [$outputs, $status];
    }
}
