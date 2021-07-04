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
}
