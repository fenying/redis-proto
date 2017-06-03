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

try {

    displayData($client->sendCommand('AUTH', 'ssss'));
}
catch (\Exception $e) {

    displayData($e->getMessage());
}

displayData($client->sendCommand('SELECT', 1) === "OK");

displayData($client->sendCommand('FLUSHDB') === "OK");

displayData($client->sendCommand('RANDOMKEY'));

displayData($client->sendCommand('SET', 'abc', 123) === "OK");

displayData($client->sendCommand('SETNX', 'abcx', 123) === 1);

displayData($client->sendCommand('SETNX', 'abcx', 123) === 0);

displayData($client->sendCommand('EXPIRE', 'abcx', 123) === 1);

displayData($client->sendCommand('EXPIREAT', 'abcx', time() + 123) === 1);

displayData($client->sendCommand('PERSIST', 'abcx'));

displayData($client->sendCommand('PTTL', 'abcx'));

displayData($client->sendCommand('EXISTS', 'abc') === 1);

displayData($client->sendCommand('GET', 'abc') === "123");

displayData($client->sendCommand('GET', 'abcd') === null);

displayData($client->sendCommand('DEL', 'abc') === 1);

displayData($client->sendCommand('HSET', 'ht', 'name', 'yubo') === 1);

displayData($client->sendCommand('HSET', 'ht', 'age', 27) === 1);

displayData($client->sendCommand('HGET', 'ht', 'name') === "yubo");

displayData($client->sendCommand('HGET', 'ht', 'age') === "27");

displayData($client->sendCommand('HGETALL', 'ht'));

displayData($client->sendCommand('RENAME', 'ht', 'htt'));

displayData($client->sendCommand('RENAMENX', 'htt', 'abcx'));

displayData($client->sendCommand('RANDOMKEY'));
