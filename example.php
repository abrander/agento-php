<?php

require('src/Message.php');
require('src/Metric.php');
require('src/Client.php');

// Register a new client
$a = new Agento\Client('127.0.0.1', 12345);

// Add a few tags describing this client
$a->addTag('hostname', 'webserver07');
$a->addTag('httphost', 'shopapi');

// We executed in 12.4 ms
$a->metric('executionTime', 1.0, 12.4);

?>
