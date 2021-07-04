<?php

declare(strict_types=1);

include_once 'BaseTestCase.php';

class InsertTest extends BaseTestCase
{

    /**
     * @test
     */
    public function insertSucceeds()
    {
        $insert = $this->db->insert('address', [
           'user' => 1,
           'address' => 'Some valid address string'
        ]);
        $this->assertTrue($insert);
        $this->assertEquals(1,$this->countAddresses());
    }

    /**
     * @test
     */
    public function insertReturnsId()
    {
        $insert = $this->db->insert('address', [
            'user' => 1,
            'address' => 'Another valid address string'
        ]);
        $this->assertTrue($insert);
        $this->assertIsInt($this->db->insertID());
        $this->assertEquals(2,$this->db->insertID());
    }

    /**
     * @test
     */
    public function insertFailsGracefully()
    {
        $insert = $this->db->insert('address', [
            'address' => 'A valid address string'
        ]);
        $this->assertFalse($insert);
        $this->assertEquals(2,$this->countAddresses());
    }

    private function countAddresses(): int
    {
        $result = $this->db->query('SELECT * FROM address')->fetchAll();
        return count($result);
    }

}