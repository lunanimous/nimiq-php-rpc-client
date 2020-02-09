<?php

require 'vendor/autoload.php';

$client = new \Lunanimous\Rpc\Client();
$response = $client->blockNumber();

$result = $response->getResult();
var_dump($result);
