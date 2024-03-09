<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/vendor/autoload.php';


// Define server connection
// \QuestDB\Client::setServer('127.0.0.1', 9009);

// Simple ping
\QuestDB\Client::ping('trades,symbol=BTC-USD,side=sell');


// Simple ping using ILP builder
$ilp_query = new \QuestDB\ILPQueryBuilder('trades', [
    'symbol' => 'BTC-USD',
    'side' => 'sell'
]);
printf("<pre>%s</pre>\n", $ilp_query);

$res = \QuestDB\Client::ping($ilp_query);

// Type casted ping using ILP builder
\QuestDB\Client::ping('trades', [
    'symbol' => 'BTC-USD',
    'side' => 'sell',
    'price:double' => 5000.00,
    'volume:int' => 100,
    'timestamp:long' => 1622640000,
    'id:uuid' => '550e8400-e29b-41d4-a716-446655440000',
    'location:geohash' => 'u4pruydqqvj',
    'isActive:boolean' => true,
    'description:string' => 'Trade description'
]);

// Sending 100000 pings
for ($i = 0; $i < 100000; $i++) {
    $ilp_query = new \QuestDB\ILPQueryBuilder('test', [
        'value:string' => ['yeah', 'woot', 'woot woot', 'incredible'][rand(0, 3)],
        'id:int' => "$i",
        'text' => ['awesome', 'hell yeah', 'all your base are belong to us'][rand(0, 2)],
    ]);
    printf("<pre>%s</pre>\n", $ilp_query);
    
    \QuestDB\Client::ping($ilp_query);
}