<?php

namespace AllopneusRediSearch\RedisRaw;


interface RedisRawClientInterface
{
    /**
     * @param string  $hostname
     * @param int     $port
     * @param int     $db
     * @param ?string $password
     *
     * @return RedisRawClientInterface
     */
    public function connect(string $hostname = '127.0.0.1', int $port = 6379, int $db = 0, ?string $password = null): RedisRawClientInterface;

    /**
     * @return void
     */
    public function flushAll(): void;

    /**
     * @return mixed
     */
    public function multi();

    /**
     * @param string $command
     * @param array $arguments
     *
     * @return mixed
     */
    public function rawCommand(string $command, array $arguments);

    /**
     * @param string $command
     * @param array $arguments
     *
     * @return array
     */
    public function prepareRawCommandArguments(string $command, array $arguments): array;
}
