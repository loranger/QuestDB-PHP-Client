<?php

namespace QuestDB;

/**
 * Class ILPQueryBuilder
 * 
 * This class is used to build Influx Line Protocol (ILP) queries.
 */
final class ILPQueryBuilder
{
    /**
     * @var string $table The table name.
     */
    private string $table;

    /**
     * @var array $tags The tags for the ILP query.
     */
    private array $tags = [];

    /**
     * @var array $fields The fields for the ILP query.
     */
    private array $fields = [];

    /**
     * @var mixed $timestamp The timestamp for the ILP query.
     */
    private $timestamp;

    /**
     * ILPQueryBuilder constructor.
     *
     * @param string $table The table name.
     * @param array $array The array of data.
     * @param int|bool $timestamp The timestamp.
     */
    public function __construct($table, $array, $timestamp = false)
    {
        $this->table = $table;
        $this->timestamp = $timestamp;

        // Iterate over the array and set the fields and tags based on the data type
        foreach ($array as $key => $value) {

            // Get datatype if exists
            list($name, $type) = array_pad(
                explode(':', $key),
                2,
                'symbol'
            );

            // Apply Influx Line Protocol syntax
            switch (strtolower($type)) {
                case 'boolean':
                    $this->fields[$name] = $value ? 'true' : 'false';
                    break;

                case 'int':
                case 'integer':
                case 'long':
                case 'long256':
                    $this->fields[$name] = sprintf('%di', $value);
                    break;

                case 'float':
                case 'double':
                    $this->fields[$name] = sprintf('%F', $value);
                    break;

                case 'char':
                case 'uuid':
                case 'string':
                case 'geohash':
                    $this->fields[$name] = sprintf('"%s"', addcslashes($value, '" '));
                    break;

                case 'symbol':
                default:
                    $this->tags[$name] = sprintf('%s', addcslashes($value, ' '));
                    break;
            }
        }
    }

    /**
     * Serialize an array into a string.
     *
     * @param array $array The array to serialize.
     * @param string $separator The separator between elements.
     * @param string $assignment The assignment operator.
     * @return string The serialized array.
     */
    private function serializeArray($array, $separator = ',', $assignment = '='): string
    {
        return implode(
            $separator,
            array_map(
                function ($key, $value) use ($assignment) {
                    return sprintf('%s%s%s', $key, $assignment, $value);
                },
                array_keys($array),
                array_values($array)
            )
        );
    }

    /**
     * Build the ILP query.
     *
     * @return string The ILP query.
     */
    public function build(): string
    {
        $parts = [];
        if (count($this->tags)) {
            array_push($parts, $this->serializeArray($this->tags));
        }
        if (count($this->fields)) {
            array_push($parts, $this->serializeArray($this->fields));
        }
        array_push($parts, $this->timestamp);

        return trim(
            sprintf('%s,%s', $this->table, implode(" ", $parts))
        );
    }

    /**
     * Convert the ILPQueryBuilder object to a string.
     *
     * @return string The ILP query.
     */
    public function __toString(): string
    {
        return $this->build();
    }
}