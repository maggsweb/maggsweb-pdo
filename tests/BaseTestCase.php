<?php

declare(strict_types=1);

/**
 * Overwrite MyPDO class with MyTestPDO
 * in order to change the __constructor()
 * to use sqlite:memory
 *
 */
include 'MyTestPDO.php';

use Maggsweb\MyTestPDO;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    /**
     * Static Instance of MyPDO
     * @var MyTestPDO $dbh
     */
    protected static $dbh;

    /**
     * @var MyTestPDO $db
     */
    protected $db;

    public static function setUpBeforeClass():void
    {
        self::$dbh = new MyTestPDO();
        self::$dbh->query('CREATE TABLE users (id INT PRIMARY KEY, name VARCHAR(50), age INTEGER)')->execute();
        $users = [
            ['id' => 1, 'name' => 'Chris', 'age' => 40],
            ['id' => 2, 'name' => 'Clare', 'age' => 50],
        ];
        foreach ($users as $user) {
            self::$dbh->insert('users', [
                'id' => $user['id'],
                'name' => $user['name'],
                'age' => $user['age']
            ]);
        }
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        $this->db = self::$dbh;
        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->db = null;
        parent::tearDown();
    }

    public static function tearDownAfterClass(): void
    {
        self::$dbh = null;
        parent::tearDownAfterClass();
    }
}