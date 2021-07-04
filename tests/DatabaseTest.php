<?php

declare(strict_types=1);

use Maggsweb\MyPDO;

include_once 'BaseTestCase.php';

class DatabaseTest extends BaseTestCase
{

    public function testDbExists()
    {
        $this->assertInstanceOf(MyPDO::class, $this->db );
    }

}