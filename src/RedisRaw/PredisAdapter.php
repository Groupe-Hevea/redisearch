<?php

namespace AllopneusRediSearch\RedisRaw;

use Predis\Client;

/**
 * Class PredisAdapter
 * @package AllopneusRediSearch\RedisRaw
 *
 * This class wraps the NRK client: https://github.com/nrk/predis
 */
class PredisAdapter implements RedisRawClientInterface
{
    /**
     * @var Client
     */
    public $redis;

    /**
     * {@inheritDoc}
     */
    public function connect(string $hostname = '127.0.0.1', int $port = 6379, int $db = 0, ?string $password = null, string $scheme = 'tcp'): RedisRawClientInterface
    {
        $clientArgs = array(
            'database' => $db,
            'password' => $password
        );
        if ($scheme === 'tcp') {
            $clientArgs = array_merge(
                $clientArgs,
                array(
                    'scheme' => 'tcp',
                    'port' => $port,
                    'host' => $hostname
                )
            );
        } else if ($scheme === 'unix') {
            $clientArgs = array_merge(
                $clientArgs,
                array(
                    'scheme' => 'unix',
                    'path' => $hostname
                )
            );
        }
        $this->redis = new Client($clientArgs);
        $this->redis->connect();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function multi()
    {
        return $this->redis->pipeline();
    }

    /**
     * {@inheritDoc}
     */
    public function rawCommand(string $command, array $arguments)
    {
        $preparedArguments = $this->prepareRawCommandArguments($command, $arguments);
        $rawResult = $this->redis->executeRaw($preparedArguments);

        return $this->normalizeRawCommandResult($rawResult);
    }

    /**
     * {@inheritDoc}
     */
    public function flushAll(): void
    {
        $this->redis->flushAll();
    }

    /**
     * {@inheritDoc}
     */
    public function prepareRawCommandArguments(string $command, array $arguments): array
    {
        foreach ($arguments as $index => $argument) {
            if (!is_scalar($arguments[$index])) {
                $arguments[$index] = (string)$argument;
            }
        }

        array_unshift($arguments, $command);

        return $arguments;
    }

    /**
     * {@inheritDoc}
     */
    public function normalizeRawCommandResult($rawResult)
    {
        return $rawResult === 'OK' ? true : $rawResult;
    }
}
