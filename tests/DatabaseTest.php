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
}
