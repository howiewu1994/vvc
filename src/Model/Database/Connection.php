<?php
namespace VVC\Model\Database;

/**
 * Singleton class for creating and holding MySQL connection
 * The purpose is to reuse existing connection
 * instead of creating new one for every query
 */
class Connection
{
    // Holds connection that is shared between all child classes
    // Destroyed when execution of the script ends
    private static $instance;

    // Copy of a connection for every child
    // Destroyed when child is destroyed
    protected $db;

    /**
     * All child classes use this method for instantiation
     * Creates a new connection or reuses existing one
     */
    public function __construct($db = null)
    {
        // DBUnit database double
        // and connection reuse
        if ($db) {
            $this->db = $db;
            self::$instance = $db;
            return;
        }

        if (!Connection::getInstance()) {
            Connection::createConnection();
        }
        $this->db = Connection::getInstance();
    }

    protected static function getInstance()
    {
        return self::$instance;
    }

    protected static function createConnection()
    {
        // test stub
        if (NO_DATABASE) {
            return self::createConnection_stub();
        }

        $dsn = "mysql:host=localhost;dbname=vvc";
        $username = "vvc_admin";
        $password = "123";

        self::$instance = new \PDO($dsn, $username, $password);
        self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    protected static function createConnection_stub()
    {
        self::$instance = 'a connection';
    }
}
