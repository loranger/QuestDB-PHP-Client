# QuestDB wrapper

Simple PHP QuestDB PHP client using Influx Line Protocol.

Formats [ILP query string](https://docs.influxdata.com/influxdb/cloud/reference/syntax/line-protocol/) regarding [QuestDB datatypes](https://questdb.io/docs/reference/api/ilp/advanced-settings/#syntax) provided

## Requirements

PHP 7+ server with `sockets` extension enabled.

## Install

Clone this repository or use composer

```shell
composer require loranger/questdb-client
```

## Usage

`\QuestDB\Client` wrapper is provided as a singleton class.

### Server config

Call the `setServer` method to define your QuestDB host.

```php
\QuestDB\Client::setServer('questdb_server', 9009);
```

### Sending datas

You can send datas to your QuestDB server using the `ping` method, by providing an [ILP](https://questdb.io/docs/reference/api/ilp/advanced-settings/#syntax) query string or an associative array

#### ILP syntax

Use the [ILP syntax](https://questdb.io/docs/reference/api/ilp/advanced-settings/#syntax) to send your datas

```php
\QuestDB\Client::ping('trades,symbol=BTC-USD,side=sell');
```

#### ILP Query Builder

You can use the ILP Query Builder to generate query string from array values

```php
new \QuestDB\ILPQueryBuilder(string $table, array $values, int $timestamp = null);
```

```php
$ilp_query = new \QuestDB\ILPQueryBuilder('trades', [
    'symbol' => 'BTC-USD',
    'side' => 'sell'
]);

echo $ilp_query;
// trades,symbol=BTC-USD,side=sell

\QuestDB\Client::ping($ilp_query);
```

The `ping` method is compliant with the `ILPQueryBuilder` constructor signature, so you can directly pass the argument to the `ping` method

```php
\QuestDB\Client::ping('trades', [
    'symbol' => 'BTC-USD',
    'side' => 'sell'
], time());
```

The given values are automatically considered as symbol datatypes, but you can specify any [QuestDB type](https://questdb.io/docs/reference/api/ilp/columnset-types/) if you need more precision:

```php
\QuestDB\Client::ping('trips', [
    'cab_type:symbol' => 'yellow',
    'passenger_count:int' => 3,
    'trip_distance:double' => 6.3,
    'payment_type' => 'cash',
]);
```

which will build the same IPL query as

```php
echo (new \QuestDB\ILPQueryBuilder('trips', [
    'cab_type:symbol' => 'yellow',
    'passenger_count:int' => 3,
    'trip_distance:double' => 6.3,
    'payment_type' => 'cash',
]));

// trips,cab_type=yellow,payment_type=cash passenger_count=3i,trip_distance=6.300000
```

## Docker

A docker compose file is provided in order to run a local short demo along with a questdb server.

The container is composed by:
- a php 7 image (locally build with its sockets extensions)
- a composer image inherited from php image
- a pest image inherited from php image
- a [dockerized questdb server](https://questdb.io/docs/deployment/docker/)

The PHP image runs [the builtin server](https://www.php.net/manual/en/features.commandline.webserver.php) and expose the port `80` (but can also be used with traefik).

The composer image should exit as soon as the container starts. It only provides a convenient shortcut to run composer command within the container: `docker-compose run --rm composer show --platform`

The pest image is a shortcut to the composer locally installed pest binary. It provides a shortcut to run tests:  `docker-compose run --rm pest --version`

The questdb image runs the latest official docker image and exposes ports `9000` and `9009` (but can also be used with traefik).

```shell
cp .env.example .env
cp docker-compose.example docker-compose.yml
docker compose up
```