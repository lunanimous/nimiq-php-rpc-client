<?php

require 'vendor/autoload.php';

$client = new \Lunanimous\Rpc\NimiqClient();
$result = $client->getSyncingState();

var_dump($result);
