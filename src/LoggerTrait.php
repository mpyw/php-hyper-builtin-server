<?php

namespace mpyw\HyperBuiltinServer;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

trait LoggerTrait
{
    private function registerLogger($name)
    {
        $this->logger = new Logger($name);
        $this->logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    private function debug($message, array $context = [])
    {
        $r = $this->logger->debug($message, $context);
        return $r;
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    private function info($message, array $context = [])
    {
        $r = $this->logger->info($message, $context);
        return $r;
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    private function notice($message, array $context = [])
    {
        $r = $this->logger->notice($message, $context);
        return $r;
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    private function warning($message, array $context = [])
    {
        $r = $this->logger->warning($message, $context);
        return $r;
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    private function error($message, array $context = [])
    {
        $r = $this->logger->error($message, $context);
        return $r;
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    private function critical($message, array $context = [])
    {
        $r = $this->logger->critical($message, $context);
        return $r;
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    private function alert($message, array $context = [])
    {
        $r = $this->logger->alert($message, $context);
        return $r;
    }

    /**
     * Adds a log record at the EMERGENCY level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    private function emergency($message, array $context = [])
    {
        $r = $this->logger->emergency($message, $context);
        return $r;
    }
}
