<?php
/**
 * File: tests/RawClient.php
 * Created Date: 2017-06-03 00:05
 */
declare (strict_types = 1);

require  __DIR__ . '/../vendor/autoload.php';

function displayData($v)
{
    echo json_encode($v), PHP_EOL;
}

$client = \RedisProto\RawClient::connect([
    'host' => '127.0.0.1',
    'port' => 6379
]);

displayData($client->sendCommand('FLUSHDB') === "OK");

displayData($client->sendCommand('SET', 'abc', 123) === "OK");

displayData($client->sendCommand('GET', 'abc') === "123");

displayData($client->sendCommand('DEL', 'abc') === 1);

displayData($client->sendCommand('HSET', 'ht', 'name', 'yubo') === 1);

displayData($client->sendCommand('HSET', 'ht', 'age', 27) === 1);

displayData($client->sendCommand('HGET', 'ht', 'name'));

displayData($client->sendCommand('HGET', 'ht', 'age'));

displayData($client->sendCommand('HGETALL', 'ht'));
