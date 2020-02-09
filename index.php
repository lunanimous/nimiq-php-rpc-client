<?php

require 'vendor/autoload.php';

$client = new \Lunanimous\Rpc\NimiqClient();
$result = $client->getPeerCount();

var_dump($result);
