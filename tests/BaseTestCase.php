<?php

declare(strict_types=1);

/**
 * Overwrite MyPDO class with MyTestPDO
 * in order to change the __constructor()
 * to use sqlite:memory.
 */
include 'MyTestPDO.php';

use Maggsweb\MyPDO;
use Maggsweb\MyTestPDO;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    /**
     * Static Instance of MyPDO.
     *
     * @var MyTestPDO
     */
    protected static $dbh;

    /**
     * @var MyTestPDO
     */
    protected $db;

    public static function setUpBeforeClass(): void
    {
        if (!self::$dbh instanceof MyPDO) {
            // Create a populate DB if it doesn't exist
            self::$dbh = new MyTestPDO();
            self::$dbh->query('CREATE TABLE users (id INTEGER PRIMARY KEY, name VARCHAR(50), age INTEGER)')->execute();
            self::$dbh->query('INSERT INTO users VALUES (1, "Clare", 50);')->execute();
            self::$dbh->query('INSERT INTO users VALUES (2, "Chris", 40);')->execute();
            self::$dbh->query('INSERT INTO users VALUES (3, "Colin", 30);')->execute();
            self::$dbh->query('INSERT INTO users VALUES (4, "Craig", 20);')->execute();
            self::$dbh->query('CREATE TABLE address (id INTEGER PRIMARY KEY, user INTEGER NOT NULL, address text)')->execute();
        }
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        $this->db = self::$dbh;
//        $this->db->query('TRUNCATE address')->execute();
        parent::setUp();
    }
}
