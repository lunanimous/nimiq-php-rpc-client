<?php

require 'vendor/autoload.php';

$client = new \Lunanimous\Rpc\Client();
$response = $client->blockNumber();

echo $response->getResponse()->getBody();

$result = $response->getResult();
var_dump($result);

$id = $response->getId();
var_dump($id);
