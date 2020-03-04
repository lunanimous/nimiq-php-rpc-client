<?php

require 'vendor/autoload.php';

$client = new \Lunanimous\Rpc\NimiqClient();
$result = $client->getBlockByNumber(12345678);

var_dump($result);
