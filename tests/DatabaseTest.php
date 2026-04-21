<?php

declare(strict_types=1);

use Maggsweb\MyPDO;
use Maggsweb\MyTestPDO;

include_once 'BaseTestCase.php';

class DatabaseTest extends BaseTestCase
{
    /**
     * @test
     */
    public function DbExists()
    {
        $this->assertInstanceOf(MyTestPDO::class, $this->db);
        $this->assertInstanceOf(MyPDO::class, $this->db);
    }

    /**
     * @test
     */
    public function invalidQueryThrowsException()
    {
        $this->expectException(PDOException::class);
        $this->db->query('SELECT * FROM notable')->fetchAll();
    }

    /**
     * @test
     */
    public function debugDumpParams()
    {
        $this->db->query('SELECT * FROM users WHERE name LIKE :name')
            ->bind(':name', 'Ch%')
            ->fetchAll();

        ob_start();
        $this->db->debugDumpParams();
        $params = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString('Params:  1', $params);
        $this->assertStringContainsString('Key: Name: [5] :name', $params);
        $this->assertStringContainsString('paramno=0', $params);
        $this->assertStringContainsString('name=[5] ":name"', $params);
        $this->assertStringContainsString('is_param=1', $params);
        $this->assertStringContainsString('param_type=2', $params);
    }

    /**
     * @test
     */
    public function getQuery()
    {
        $query_1 = 'SELECT * FROM users';
        $this->db->query($query_1)->fetchAll();
        $query_2 = $this->db->getQuery();

        $this->assertEquals($query_1, $query_2);
    }

    /**
     * @test
     */
    public function getErrorIsEmptyByDefault()
    {
        $fresh = new MyTestPDO();
        $this->assertSame('', $fresh->getError());
    }

    /**
     * @test
     */
    public function bindInfersIntegerType()
    {
        $this->db->query('SELECT * FROM users WHERE age = :age')->bind(':age', 40);

        ob_start();
        $this->db->debugDumpParams();
        $params = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString('param_type=1', $params);
    }

    /**
     * @test
     */
    public function bindInfersBooleanType()
    {
        $this->db->query('SELECT * FROM users WHERE age = :flag')->bind(':flag', true);

        ob_start();
        $this->db->debugDumpParams();
        $params = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString('param_type=5', $params);
    }

    /**
     * @test
     */
    public function bindInfersNullType()
    {
        $this->db->query('SELECT * FROM users WHERE age = :age')->bind(':age', null);

        ob_start();
        $this->db->debugDumpParams();
        $params = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString('param_type=0', $params);
    }

    /**
     * @test
     */
    public function bindAcceptsExplicitType()
    {
        $this->db->query('SELECT * FROM users WHERE age = :age')
            ->bind(':age', 40, PDO::PARAM_STR);

        ob_start();
        $this->db->debugDumpParams();
        $params = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString('param_type=2', $params);
    }
}
