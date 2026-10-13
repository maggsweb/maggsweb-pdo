<?php

declare(strict_types=1);

use Maggsweb\MyPDO;
use PHPUnit\Framework\TestCase;

/**
 * Tests that need a real MySQL server: the charset default and runScript().
 * SQLite runs only the first statement of a multi-statement string.
 *
 * Skipped unless MYPDO_TEST_HOST, MYPDO_TEST_USER, MYPDO_TEST_PASS and
 * MYPDO_TEST_NAME are set. Only TEMPORARY tables are created, so nothing is
 * left behind in that database.
 */
class MySqlTest extends TestCase
{
    private MyPDO $db;

    public function setUp(): void
    {
        $host = getenv('MYPDO_TEST_HOST');
        if ($host === false) {
            $this->markTestSkipped('Set MYPDO_TEST_HOST, _USER, _PASS and _NAME to run the MySQL tests.');
        }

        $this->db = new MyPDO(
            $host,
            (string) getenv('MYPDO_TEST_USER'),
            (string) getenv('MYPDO_TEST_PASS'),
            (string) getenv('MYPDO_TEST_NAME'),
            [PDO::MYSQL_ATTR_MULTI_STATEMENTS => true]
        );
        $this->assertSame('', $this->db->getError());

        $this->db->runScript('DROP TEMPORARY TABLE IF EXISTS mypdo_script; CREATE TEMPORARY TABLE mypdo_script (id INT PRIMARY KEY, name VARCHAR(20)) CHARACTER SET utf8mb4;');
    }

    public function tearDown(): void
    {
        MyPDO::setConnection(null);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function defaultCharsetIsUtf8mb4()
    {
        $this->assertSame('utf8mb4', $this->db->query('SELECT @@character_set_client')->fetchOne());
    }

    /**
     * @test
     */
    public function aFourByteCharacterIsStored()
    {
        $this->assertTrue($this->db->insert('mypdo_script', ['id' => 1, 'name' => "Hi \u{1F600}"]));
        $this->assertSame("Hi \u{1F600}", $this->db->query('SELECT name FROM mypdo_script WHERE id = 1')->fetchOne());
    }

    /**
     * @test
     */
    public function runScriptRunsEveryStatement()
    {
        $ok = $this->db->runScript("INSERT INTO mypdo_script VALUES (1, 'one');\nINSERT INTO mypdo_script VALUES (2, 'two');\nUPDATE mypdo_script SET name = 'TWO' WHERE id = 2;");

        $this->assertTrue($ok);
        $this->assertSame(
            [['id' => 1, 'name' => 'one'], ['id' => 2, 'name' => 'TWO']],
            $this->db->query('SELECT id, name FROM mypdo_script ORDER BY id')->fetchAll('Array')
        );
    }

    /**
     * @test
     */
    public function runScriptReportsAnErrorInALaterStatement()
    {
        $ok = $this->db->runScript("INSERT INTO mypdo_script VALUES (1, 'one');\nINSERT INTO mypdo_no_such_table VALUES (2);");

        $this->assertFalse($ok);
        $this->assertStringContainsString("mypdo_no_such_table' doesn't exist", $this->db->getError());
        // The statements before the failing one have run
        $this->assertSame(1, (int) $this->db->query('SELECT COUNT(*) FROM mypdo_script')->fetchOne());
    }

    /**
     * @test
     */
    public function initialiseBuildsTheConnection()
    {
        $connection = MyPDO::initialise(
            (string) getenv('MYPDO_TEST_HOST'),
            (string) getenv('MYPDO_TEST_USER'),
            (string) getenv('MYPDO_TEST_PASS'),
            (string) getenv('MYPDO_TEST_NAME')
        );

        $this->assertSame('', $connection->getError());
        $this->assertSame($connection, MyPDO::connection());
        $this->assertSame('1', (string) MyPDO::connection()->query('SELECT 1')->fetchOne());
    }
}
