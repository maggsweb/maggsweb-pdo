<?php

declare(strict_types=1);

use Maggsweb\MyPDO;
use Maggsweb\MyTestPDO;

include_once 'BaseTestCase.php';

class ConnectionTest extends BaseTestCase
{
    public function tearDown(): void
    {
        MyPDO::setConnection(null);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function connectionThrowsBeforeInitialise()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('call MyPDO::initialise() first');
        MyPDO::connection();
    }

    /**
     * @test
     */
    public function connectionReturnsTheInstanceSet()
    {
        MyPDO::setConnection($this->db);

        $this->assertSame($this->db, MyPDO::connection());
        $this->assertSame($this->db, MyPDO::connection());
    }

    /**
     * @test
     */
    public function initialiseKeepsAnExistingInstance()
    {
        MyPDO::setConnection($this->db);

        $this->assertSame($this->db, MyPDO::initialise('unused', 'unused', 'unused', 'unused'));
        $this->assertSame($this->db, MyPDO::connection());
    }

    /**
     * @test
     */
    public function setConnectionNullForgetsTheInstance()
    {
        MyPDO::setConnection(new MyTestPDO());
        MyPDO::setConnection(null);

        $this->expectException(RuntimeException::class);
        MyPDO::connection();
    }
}
