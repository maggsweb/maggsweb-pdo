<?php

declare(strict_types=1);

use Maggsweb\MyPDO;

include_once 'BaseTestCase.php';

class SelectTest extends DatabaseTest
{

    public function testBasicSelect()
    {
        $this->assertTrue(true);
        $users = $this->db->query('SELECT * FROM users')->fetchAll();
        $this->assertCount(2,$users);
    }





}