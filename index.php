<?php

require 'vendor/autoload.php';

include_once 'src/Client.php';

$client = new \Lunanimous\Rpc\Client();
$body = $client->getBlockByNumber('1', 1);

print_r($body['result']);
