<?php
namespace QuestDB;

/**
 * Class Client
 *
 * This class is used to manage the connection to the QuestDB server.
 */
final class Client
{
    /**
     * Default host for the QuestDB server.
     */
    private $default_host = 'questdb_server';

    /**
     * Default port for the QuestDB server.
     */
    private $default_port = 9009;

    /**
     * Socket resource.
     */
    protected $socket = null;

    /**
     * Singleton instance of the Client class.
     */
    protected static $instance = null;

    /**
     * Client constructor.
     *
     * Creates a new socket and connects to the default QuestDB server.
     */
    private function __construct()
    {
        $this->connect($this->default_host, $this->default_port);
    }

    /**
     * Connects to the QuestDB server.
     *
     * @param string $host The host of the QuestDB server.
     * @param int $port The port of the QuestDB server.
     */
    private function connect($host, $port = 9009)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        return @socket_connect($this->socket, $host, $port);
    }

    /**
     * Client destructor.
     *
     * Closes the socket connection and unsets the socket resource.
     */
    public function __destruct()
    {
        socket_close($this->socket);
        unset($this->socket);
    }

    /**
     * Returns the singleton instance of the Client class.
     *
     * @return Client The singleton instance of the Client class.
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Sets the QuestDB server.
     *
     * @param string $host The host of the QuestDB server.
     * @param int $port The port of the QuestDB server.
     */
    public static function setServer($host, $port = 9009)
    {
        return self::getInstance()->connect($host, $port);
    }

    /**
     * Sends a ping to the QuestDB server.
     *
     * @param mixed ...$mixed The values to send in the ping.
     */
    public static function ping(...$mixed)
    {
        switch(func_num_args()) {
            case 1:
                $values = func_get_arg(0);
                break;
            default:
                $values = (string) new ILPQueryBuilder(... func_get_args());
                break;
        }

        return socket_write(self::getInstance()->socket, $values."\n");
    }

    /**
     * Retrieves the last socket error message.
     *
     * @return string The last socket error message.
     */
    public static function lastError()
    {
        return socket_strerror(socket_last_error(self::getInstance()->socket));
    }
}