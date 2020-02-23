<?php

require 'vendor/autoload.php';

$client = new \Lunanimous\Rpc\NimiqClient();
$result = $client->getConsensusState();

var_dump($result);
