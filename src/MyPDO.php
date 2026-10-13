<?php

/**
 * MyPDO Class.
 *
 * @category  Database Access
 *
 * @author    Chris Maggs <git@maggsweb.co.uk>
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 *
 * One connection per request: call MyPDO::initialise() once at startup, then
 * MyPDO::connection() wherever a handle is needed, rather than `new MyPDO(...)`
 * in every class (each of which opens its own connection).
 *
 * ONE THING TO KNOW: the prepared statement is kept on the instance, so a
 * shared instance means a shared statement. Always chain
 * query()->bind()->fetch() as one expression (or as adjacent lines). Split
 * them so other code can query in between, and the first caller silently
 * gets the wrong rows. If a query has to stay open across other work, give
 * it its own `new MyPDO(...)`.
 **/

namespace Maggsweb;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

class MyPDO
{
    /**
     * Host name.
     *
     * @var string
     */
    private string $host;

    /**
     * Username.
     *
     * @var string
     */
    private string $user;

    /**
     * Password.
     *
     * @var string
     */
    private string $pass;

    /**
     * DB Name.
     *
     * @var string
     */
    private string $dbname;

    /**
     * Database handle.
     *
     * @var PDO|null
     */
    protected ?PDO $dbh;

    /**
     * Error message.
     *
     * @var string|bool
     */
    protected string|bool $error;

    /**
     * Query statement.
     *
     * @var PDOStatement
     */
    protected PDOStatement $stmt;

    /**
     * PDO Options.
     *
     * @var array
     */
    protected array $options;

    /**
     * Shared instance, built by initialise() and returned by connection().
     *
     * @var MyPDO|null
     */
    private static ?MyPDO $connection = null;

    /**
     * Build the shared instance. Call once at startup, before connection().
     *
     * If a shared instance already exists (an earlier initialise(), or a test
     * double from setConnection()) it is returned unchanged and no new
     * connection is made.
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $dbname
     * @param array  $overrides
     *
     * @return MyPDO
     */
    public static function initialise(string $host, string $user, string $pass, string $dbname, array $overrides = []): MyPDO
    {
        return self::$connection ??= new self($host, $user, $pass, $dbname, $overrides);
    }

    /**
     * The shared instance built by initialise(). Opens nothing itself.
     *
     * @throws RuntimeException if initialise() has not been called
     *
     * @return MyPDO
     */
    public static function connection(): MyPDO
    {
        if (self::$connection === null) {
            throw new RuntimeException('No MyPDO connection: call MyPDO::initialise() first.');
        }

        return self::$connection;
    }

    /**
     * Replace the shared instance, or pass null to forget it. For tests: a
     * double set here is what every connection() caller gets.
     *
     * @param MyPDO|null $instance
     */
    public static function setConnection(?MyPDO $instance): void
    {
        self::$connection = $instance;
    }

    /**
     * MyPDO constructor.
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $dbname
     * @param array  $overrides
     */
    public function __construct(string $host, string $user, string $pass, string $dbname, array $overrides = [])
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        $this->options = $this->setOptions($overrides);
        $this->error = '';
        $this->dbh = null;

        try {
            $this->dbh = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pass, $this->options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    /**
     * Set PDO Options with overrides.
     *
     * @param array $overrides
     *
     * @return array
     */
    private function setOptions(array $overrides = []): array
    {
        // utf8mb4, not utf8: MySQL's utf8 is the 3-byte utf8mb3, so under strict
        // mode any write carrying a 4-byte character (e.g. an emoji) is refused.
        $options = [
            PDO::ATTR_PERSISTENT         => true,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        ];
        // Override option if $override key exists
        foreach ($overrides as $k => $v) {
            $options[$k] = $v;
        }

        return $options;
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
        $this->assertConnected();
        $this->stmt = $this->dbh->prepare($query);

        return $this;
    }

    /**
     * Run a string of one or more SQL statements, e.g. a migration or seed file.
     *
     * query() goes through PDO::prepare(), which only reliably runs the first
     * statement of a multi-statement string. This sends the whole string with
     * PDO::query() and steps through every statement's result, so an error in
     * a later statement is caught rather than silently skipped. Nothing can be
     * bound and no rows are returned.
     *
     * MySQL only. pdo_mysql allows multiple statements by default; to be
     * explicit, pass [PDO::MYSQL_ATTR_MULTI_STATEMENTS => true] as $overrides.
     *
     * @param string $sql
     *
     * @return bool false on failure, with the reason in getError()
     */
    public function runScript(string $sql): bool
    {
        $this->assertConnected();

        try {
            $this->stmt = $this->dbh->query($sql);
            while ($this->stmt->nextRowset()) {
                // no result data needed, just advancing to the next statement
            }
        } catch (PDOException $e) {
            $this->error = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Bind a specific column/value.
     *
     * @param      $param
     * @param      $value
     * @param null $type
     *
     * @return $this
     */
    public function bind($param, $value, $type = null): MyPDO
    {
        $paramType = $type ?? $this->getType($value);

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
    public function fetchRow(string $type = 'Object'): mixed
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
    public function fetchOne(): mixed
    {
        $this->execute();

        $row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? array_values($row)[0] : null;
    }

    /**
     * Insert Query.
     *
     * @param string $table
     * @param array  $columns
     *
     * @return bool
     */
    public function insert(string $table, array $columns): bool
    {
        $this->assertConnected();

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
     * @param string            $table
     * @param array             $columns
     * @param bool|array|string $where
     * @param bool|int          $limit
     *
     * @return bool
     */
    public function update(string $table, array $columns, bool|array|string $where = false, bool|int $limit = false): bool
    {
        $this->assertConnected();

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
        if (is_array($where)) {
            $this->bindWhereParameters($where);
        }

        return $this->execute();
    }

    /**
     * Delete Query.
     *
     * @param string            $table
     * @param bool|array|string $where
     * @param bool|int          $limit
     *
     * @return bool
     */
    public function delete(string $table, bool|array|string $where = false, bool|int $limit = false): bool
    {
        $this->assertConnected();

        // Build the Query
        $query = "DELETE FROM $table";

        // Build the WHERE
        $query .= $this->buildWhereString($where);

        // Build LIMIT
        $query .= $limit ? ' LIMIT '.(int) $limit : '';

        // Prepare Query
        $this->stmt = $this->dbh->prepare($query);

        // Bind Where Params
        if (is_array($where)) {
            $this->bindWhereParameters($where);
        }

        return $this->execute();
    }

    /**
     * Num-affected-rows for UPDATE/DELETE.
     *
     * @return int
     */
    public function numRows(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * @return string
     */
    public function insertID(): string
    {
        $this->assertConnected();

        return $this->dbh->lastInsertId();
    }

    /**
     * @return bool|null
     */
    public function debugDumpParams(): ?bool
    {
        return $this->stmt->debugDumpParams();
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return (string) $this->error;
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

    private function assertConnected(): void
    {
        if ($this->dbh === null) {
            throw new RuntimeException($this->error !== '' ? $this->error : 'Database connection not established.');
        }
    }

    /**
     * @param array|string $where
     *
     * @return string
     */
    private function buildWhereString(array|string $where): string
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
    private function bindWhereParameters(array $where): void
    {
        foreach ($where as $key => $value) {
            $this->bind(":where_$key", $value);
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
     * @param array $columns
     *
     * @return string
     */
    private function buildBindString(array $columns): string
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
        return match (true) {
            is_int($value)    => PDO::PARAM_INT,
            is_bool($value)   => PDO::PARAM_BOOL,
            is_null($value)   => PDO::PARAM_NULL,
            default                 => PDO::PARAM_STR,
        };
    }
}
