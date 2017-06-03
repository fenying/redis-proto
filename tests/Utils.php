<?php
/**
 * File: tests/Utils.php
 * Created Date: 2017-06-03 00:05
 */
declare (strict_types = 1);

require  __DIR__ . '/../vendor/autoload.php';

function displayData($v)
{
    echo json_encode($v), PHP_EOL;
}

$utils = new \RedisProto\Utils();

displayData($utils->encode([
    'AUTH',
    'hello world!'
]));

displayData($utils->encode([
    'SET',
    'abc',
    '123'
]));

displayData($utils->encode([
    'GET',
    'abc'
]));

displayData($utils->encode([
    'QUIT'
]));

function fetchSingleResult(\RedisProto\Utils $utils)
{
    $ret = array_shift($utils->results);

    if (is_array($ret)) {

        $ret = array_splice($utils->results, 0, $ret[0]);
    }

    return $ret;
}

$utils->decode(<<<PROTO
+Hello world\r

PROTO
);

displayData(fetchSingleResult($utils));

$utils->decode(<<<PROTO
$12\r
Hello world!\r

PROTO
);

displayData(fetchSingleResult($utils));

try {
    
    $utils->decode(<<<PROTO
-ERR this is an error\r

PROTO
    );
}
catch (\Exception $e) {

    echo $e->getMessage(), PHP_EOL;
}

$utils->decode(<<<PROTO
:123\r
$3\r
abc\r

PROTO
);

displayData(fetchSingleResult($utils));
displayData(fetchSingleResult($utils));

$utils->decode(<<<PROTO
*3\r
\$3\r
1
3\r
+HEIHEIHEI\r
:123\r

PROTO
);

displayData(fetchSingleResult($utils));
