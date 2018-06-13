<?php

namespace FC;

/**
 * TODO: Add support for other databases than MySQL and MariaDB
 * NOTE: select, update, delete and execScript, although identical have been split to give room for distintive behavior if needed
 */

/**
 * Libray to communicate with databases
 */
class Db {
/* member variables */
    private $dbname;
    private $charset;
    private $host;
    private $login;
    private $password;
    private $connection;
    private $connected;
    private $arrParams;
    private $statement;
    private $errMessage;
    private $lastId;
    
/* member functions */
    public function setDbname(string $par) { $this->dbname = $par; }
    public function getDbname() { return $this->dbname; }
    public function setCharset(string $par) { $this->charset = $par; }
    public function getCharset() { return $this->charset; }
    public function setHost(string $par) { $this->host = $par; }
    public function getHost() { return $this->host; }
    public function setLogin(string $par) { $this->login = $par; }
    public function getLogin() { return $this->login; }
    public function setPassword(string $par) { $this->password = $par; }
    public function getPassword() { return $this->password; }
    public function isConnected() { return $this->connected; }
    public function getErrMessage() { return $this->errMessage; }
    public function getLastId() { return $this->lastId; }
    // query parameter array functions
    public function emptyParams() { $this->arrParams = array(); }
    
    /**
     * Add parameters to bind to SQL query
     *
     * @param string $parName parameter name
     * @param string $parValue parameter value
     * @return void
     */
    public function addParamToBind(string $parName, string $parValue) {
        $arraySize = sizeof($this->arrParams);
        $this->arrParams[$arraySize][0] = $parName;
        $this->arrParams[$arraySize][1] = $parValue;
    }

    /**
     * Constructor: attempt to connect to database after initialization of member variables
     *
     * @param string $parDbName database name
     * @param string $parHost database hostname
     * @param string $parLogin user login
     * @param string $parPassword user password
     * @param string $parCharset connection character set
     */
    function __construct(string $parDbName, string $parHost, string $parLogin, string $parPassword, string $parCharset='utf8mb4') { 
        $this->dbname = $parDbName;
        $this->host = $parHost;
        $this->login = $parLogin;
        $this->password = $parPassword;
        $this->arrParams = array();
        $this->charset = $parCharset;

        $this->connect();
    }

    /**
     * Desctructor: disconnect connection properly
     */
    function __destruct() {
        $this->disconnect();
    }

/* methods */
    /**
     * Disconnect from database and reset flag
     *
     * @return void
     */
    public function disconnect() {
        $this->connection = null;
        $this->connected = false;
    }
    
    /**
     * Attempt a connection to the database
     *
     * @return void
     */
    public function connect() {
        // reset connection status
        $this->connected = false;
        // reset error message
        $this->errMessage = '';

        try {
            $connString = sprintf('mysql:dbname=%s;mysql:charset=%s;host=%s',$this->dbname, $this->charset, $this->host);
            $this->connection = new \PDO($connString, $this->login, $this->password);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // [OPTIONAL] stop the execution on error occurring during prepare
            // $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

            $this->connected = true;
        }
        catch (\PDOException $e) {
            $this->errMessage = $e->getMessage();
        }
    }

    /**
     * Execute query and catch error message (if any)
     * Return true on success, false on error
     *
     * @return bool 
     */
    private function executeQuery() {
        try {
            $this->statement->execute();
            return true;
        }
        catch (\PDOException $e) {
            $this->errMessage = $e->getMessage();
            return false;
        }
    }

    /**
     * Bind parameters to a query (if any)
     *
     * @return void
     */
    private function bindParameters() {
        if ($this->arrParams != null) { 
            $imax = sizeof($this->arrParams);
            for($i = 0; $i < $imax; $i++) {
                $this->statement->bindParam(':'.$this->arrParams[$i][0], $this->arrParams[$i][1]);
            }
        }
    }

    /**
     * Prepare query before binding parameters (if any)
     *
     * @param string $query
     * @return void
     */
    private function prepareQuery(string $query) {
        $this->statement = $this->connection->prepare($query);
    }

    /**
     * Retrieve id of the last inserted row and save it to member lastId
     *
     * @return void
     */
    private function retrieveLastId() {
        $this->lastId = $this->connection->lastInsertId();
    }

    /**
     * Get the next row of a result set for a select query
     * (FETCH_BOTH: returns an array indexed by both column name and 0-indexed column number as returned in your result set)
     * Return false on failure
     *
     * @return array
     */
    public function getNextRow() { return $this->statement->fetch(); }

    /**
     * Execute a select query
     * Return true on success, false on failure
     *
     * @param string $query
     * @return bool
     */
    public function select(string $query) {
        // reset the result set
        $this->statement = false;
        // reset error message
        $this->errMessage = '';
        // prepare query
        $this->prepareQuery($query);
        // bind parameters
        $this->bindParameters();

        // execute query
        $executed = $this->executeQuery();
        return $executed;
    }

    /**
     * Execute an insert query
     * Return true on success, false on failure
     *
     * @param string $query
     * @return bool
     */
    public function insert(string $query) {
        // reset the result set
        $this->statement = false;
        // reset error message
        $this->errMessage = '';
        // reset the last inserted id
        $this->lastId = null;
        // prepare query
        $this->prepareQuery($query);
        // bind parameters
        $this->bindParameters();

        // execute query
        $executed = $this->executeQuery();
        if ($executed) { $this->retrieveLastId(); }
        return $executed;
    }

    /**
     * Execute an update query
     * Return true on success, false on failure
     *
     * @param string $query
     * @return bool
     */
    public function update(string $query) {
        // reset the result set
        $this->statement = false;
        // reset error message
        $this->errMessage = '';
        // prepare query
        $this->prepareQuery($query);
        // bind parameters
        $this->bindParameters();

        // execute query
        $executed = $this->executeQuery();
        return $executed;
    }

    /**
     * Execute a delete query
     * Return true on success, false on failure
     *
     * @param string $query
     * @return bool
     */
    public function delete(string $query) {
        // reset the result set
        $this->statement = false;
        // reset error message
        $this->errMessage = '';
        // prepare query
        $this->prepareQuery($query);
        // bind parameters
        $this->bindParameters();

        // execute query
        $executed = $this->executeQuery();
        return $executed;
    }

    /**
     * Execute a script
     * Return true on success, false on failure
     *
     * @param string $script
     * @return bool
     */
    public function execScript(string $script) {
        // reset the result set
        $this->statement = false;
        // reset error message
        $this->errMessage = '';
        // prepare script
        $this->prepareQuery($script);
        // bind parameters
        $this->bindParameters();

        // execute script
        $executed = $this->executeQuery();
        return $executed;
    }
}

