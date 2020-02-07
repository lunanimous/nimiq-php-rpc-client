<?php

require 'vendor/autoload.php';

include_once 'src/Rpc/Client.php';
include_once 'src/Rpc/NimiqResponse.php';

$client = new \Lunanimous\Rpc\Client();
$response = $client->blockNumber();

$result = $response->getResult();
var_dump($result);
