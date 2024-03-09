<?php

$host = 'questdb_server'; // Docker compose should be up and running

test('Client successful connection', function () use ($host) {
    $connection = \QuestDB\Client::setServer($host, 9009);
    expect($connection)->toBeTrue();
});

test('Client failed connection', function () use ($host) {
    $connection = \QuestDB\Client::setServer('234.234.234.234', 9009);
    expect($connection)->toBeFalse();
    expect((string)\QuestDB\Client::lastError())->toBe('Network unreachable');
});


test('Client basic usage', function () use ($host) {
    \QuestDB\Client::setServer($host, 9009);
    $ilp_query = new \QuestDB\ILPQueryBuilder('trades', [
        'symbol' => 'BTC-USD',
        'side' => 'sell'
    ]);
    $bytes = \QuestDB\Client::ping($ilp_query);
    expect((int)$bytes)->toBeGreaterThan(0);
});

test('Client with specific types', function () use ($host) {
    \QuestDB\Client::setServer($host, 9009);
    $ilp_query = new \QuestDB\ILPQueryBuilder('trips', [
        'cab_type:symbol' => 'yellow',
        'passenger_count:int' => 3,
        'trip_distance:double' => 6.3,
        'payment_type' => 'cash',
    ]);
    $bytes = \QuestDB\Client::ping($ilp_query);
    expect((int)$bytes)->toBeGreaterThan(0);
});

test('Client with all specific types', function () use ($host) {
    \QuestDB\Client::setServer($host, 9009);
    $ilp_query = new \QuestDB\ILPQueryBuilder('trades', [
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
    $bytes = \QuestDB\Client::ping($ilp_query);
    expect((int)$bytes)->toBeGreaterThan(0);
});
