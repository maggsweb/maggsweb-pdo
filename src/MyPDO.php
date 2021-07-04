<?php
/**
 * MyPDO Class.
 *
 * @category  Database Access
 *
 * @author    Chris Maggs <git@maggsweb.co.uk>
 * @copyright Copyright (c)2016
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 *
 * @version   1.2
 **/

namespace Maggsweb;

use PDO;
use PDOException;
use PDOStatement;

class MyPDO
{
    /**
     * Host name.
     *
     * @var string
     */
    private $host;

    /**
     * Username.
     *
     * @var string
     */
    private $user;

    /**
     * Password.
     *
     * @var string
     */
    private $pass;

    /**
     * DB Name.
     *
     * @var string
     */
    private $dbname;

    /**
     * Database handle.
     *
     * @var object
     */
    protected $dbh;

    /**
     * Error message.
     *
     * @var string
     */
    protected $error;

    /**
     * Query statement.
     *
     * @var PDOStatement
     */
    protected $stmt;

    /**
     * MyPDO constructor.
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $dbname
     */
    public function __construct(string $host, string $user, string $pass, string $dbname)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        $this->error = false;
        $this->dbh = null;

        $options = [
            // This option sets the connection type to the database to be persistent.
            // Persistent database connections can increase performance by checking to see
            // if there is already an established connection to the database.
            PDO::ATTR_PERSISTENT => true,
            // Using ERRMODE_EXCEPTION will throw an exception if an error occurs.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Force UTF8 encoding throughout
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ];

        try {
            $this->dbh = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    /**
     * Prepare query statement.
     *
     * @param $query
     *
     * @return $this
     */
    public function query($query): MyPDO
    {
        $this->stmt = $this->dbh->prepare($query);

        return $this;
    }

    /**
     * Bind a specific column/value.
     *
     * @param $param
     * @param $value
     * @param null $type
     *
     * @return $this
     */
    public function bind($param, $value, $type = null): MyPDO
    {
        $paramType = $type ? $this->getType($value) : null;

        $this->stmt->bindValue($param, $value, $paramType);

        return $this;
    }

    /**
     * Run Query!  This is called automatically when fetching results.
     *
     * @return bool
     */
    public function execute(): bool
    {
        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }

        return $this->stmt->errorCode() === '00000';
    }

    /**
     * Fetch multiple rows as ObjectArray or MultiDimensional Array.
     *
     * @param string $type
     *
     * @return array
     */
    public function fetchAll(string $type = 'Object'): array
    {
        $this->execute();

        return $type == 'Array'
            ? $this->stmt->fetchAll(PDO::FETCH_ASSOC)
            : $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Fetch a single row as an Object or an Array.
     *
     * @param string $type
     *
     * @return mixed
     */
    public function fetchRow(string $type = 'Object')
    {
        $this->execute();

        return $type == 'Array'
            ? $this->stmt->fetch(PDO::FETCH_ASSOC)
            : $this->stmt->fetchObject();
    }

    /**
     * Fetch a single column value.
     *
     * @return mixed
     */
    public function fetchOne()
    {
        $this->execute();

        $row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        return array_values($row)[0];
    }

    /**
     * Insert Query.
     *
     * Build 'column string' from $column Array
     * Bind  'column values' from $column Array
     * Build query string
     * Bind column values
     * Execute query
     *
     * @param $table
     * @param $columns
     *
     * @return bool
     */
    public function insert($table, $columns): bool
    {
        $columnString = $this->buildColumnString($columns);
        $binderString = $this->buildBindString($columns);

        // Build QUERY
        $query = "INSERT INTO $table ($columnString) VALUES ($binderString);";

        // Prepare Query
        $this->stmt = $this->dbh->prepare($query);

        // Bind Column Params
        foreach ($columns as $key => $value) {
            $this->bind(":column_$key", $value);
        }

        return $this->execute();
    }

    /**
     * Update Query.
     *
     * Bind column values
     * Build 'where' clause from String or Array
     * Add additional SQL
     * Build query string
     * Bind column values
     * Bind 'where' parameters
     * Execute query
     *
     * @param $table
     * @param $columns
     * @param bool|string|array $where
     * @param bool              $limit
     *
     * @return bool
     */
    public function update($table, $columns, $where = false, bool $limit = false): bool
    {
        $query = "UPDATE $table SET ";

        // Build column bindings
        $query .= $this->buildColumnBindString($columns);

        // Build the WHERE
        $query .= $this->buildWhereString($where);

        // Build LIMIT
        $query .= $limit ? ' LIMIT '.(int) $limit : '';

        // Prepare Query
        $this->stmt = $this->dbh->prepare($query);

        // Bind Column Params
        foreach ($columns as $key => $value) {
            $this->bind(":column_$key", $value);
        }

        // Bind Where Params
        $this->bindWhereParameters($where);

        return $this->execute();
    }

    /**
     * Delete Query.
     *
     * Build 'where' clause from String or Array
     * Add additional SQL
     * Build query string
     * Bind 'where' parameters
     * Execute query
     *
     * @param $table
     * @param bool|string|array $where
     * @param bool              $limit
     *
     * @return bool
     */
    public function delete($table, $where = false, bool $limit = false): bool
    {
        // Build the Query
        $query = "DELETE FROM $table";

        // Build the WHERE
        $query .= $this->buildWhereString($where);

        // Build LIMIT
        $query .= $limit ? ' LIMIT '.(int) $limit : '';

        // Prepare Query
        $this->stmt = $this->dbh->prepare($query);

        // Bind Where Params
        $this->bindWhereParameters($where);

        return $this->execute();
    }

    /**
     * Num-affected-rows for INSERT/UPDATE/DELETE.
     *
     * @return int
     */
    public function numRows(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * @return int
     */
    public function insertID(): int
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * @return mixed
     */
    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->stmt->queryString;
    }

    //  ----------------------------------------------------------------
    //  PRIVATE HELPER FUNCTIONS  --------------------------------------
    //  ----------------------------------------------------------------

    /**
     * @param $where
     *
     * @return string
     */
    private function buildWhereString($where): string
    {
        $whereString = '';
        if ($where) {
            $whereString .= ' WHERE ';
            if (is_array($where)) {
                // If $where is an array, all WHERE operators will be '='
                $clauses = [];
                foreach ($where as $key => $value) {
                    $clauses[] = "$key = :where_$key";
                }
                $whereString .= implode(' AND ', $clauses);
            } else {
                // $where is treated as a string
                // replace all case versions of 'where'
                $whereString .= preg_replace('/where/i', '', $where);
            }
        }

        return $whereString;
    }

    /**
     * @param array $where
     */
    private function bindWhereParameters(array $where)
    {
        if ($where) {
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    $this->bind(":where_$key", $value);
                }
            }
        }
    }

    /**
     * @param array $columns
     *
     * @return string
     */
    private function buildColumnBindString(array $columns): string
    {
        $binders = [];
        foreach ($columns as $key => $value) {
            $binders[] = "$key = :column_$key";
        }

        return implode(', ', $binders);
    }

    /**
     * @param array $columns
     *
     * @return string
     */
    private function buildColumnString(array $columns): string
    {
        $tmp = [];
        foreach ($columns as $key => $value) {
            $tmp[] = $key;
        }

        return implode(', ', $tmp);
    }

    /**
     * @param $columns
     *
     * @return string
     */
    private function buildBindString($columns): string
    {
        $tmp = [];
        foreach ($columns as $key => $value) {
            $tmp[] = ':column_'.$key;
        }

        return implode(', ', $tmp);
    }

    /**
     * @param $value
     *
     * @return int|null
     */
    private function getType($value): ?int
    {
        switch (true) {
            case is_int($value):
                return PDO::PARAM_INT;
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case is_null($value):
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
    }
}
