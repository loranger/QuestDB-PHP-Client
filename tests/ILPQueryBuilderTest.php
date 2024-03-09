<?php

test('ILPQueryBuilder basic usage', function () {
    $ilp_query = new \QuestDB\ILPQueryBuilder('trades', [
        'symbol' => 'BTC-USD',
        'side' => 'sell'
    ]);

    expect((string)$ilp_query)->toBe('trades,symbol=BTC-USD,side=sell');
});

test('ILPQueryBuilder with timestamp', function () {
    $timestamp = time();
    $ilp_query = new \QuestDB\ILPQueryBuilder('trades', [
        'symbol' => 'BTC-USD',
        'side' => 'sell'
    ], $timestamp);

    expect((string)$ilp_query)->toBe('trades,symbol=BTC-USD,side=sell ' . $timestamp);
});

test('ILPQueryBuilder with specific types', function () {
    $ilp_query = new \QuestDB\ILPQueryBuilder('trips', [
        'cab_type:symbol' => 'yellow',
        'passenger_count:int' => 3,
        'trip_distance:double' => 6.3,
        'payment_type' => 'cash',
    ]);

    expect((string)$ilp_query)->toBe('trips,cab_type=yellow,payment_type=cash passenger_count=3i,trip_distance=6.300000');
});

test('ILPQueryBuilder with all specific types', function () {
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

    expect((string)$ilp_query)->toBe('trades,symbol=BTC-USD,side=sell price=5000.000000,volume=100i,timestamp=1622640000i,id="550e8400-e29b-41d4-a716-446655440000",location="u4pruydqqvj",isActive=true,description="Trade\ description"');
});
