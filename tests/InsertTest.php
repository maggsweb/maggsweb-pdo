<?php

declare(strict_types=1);

include_once 'BaseTestCase.php';

class InsertTest extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->db->query('DELETE FROM address')->execute();
    }

    /**
     * @test
     */
    public function insertSucceeds()
    {
        $insert = $this->db->insert('address', [
            'user'    => 1,
            'address' => 'Some valid address string',
        ]);
        $this->assertTrue($insert);
        $this->assertEquals(1, $this->countAddresses());
    }

    /**
     * @test
     */
    public function insertReturnsId()
    {
        $insert = $this->db->insert('address', [
            'user'    => 1,
            'address' => 'Another valid address string',
        ]);
        $this->assertTrue($insert);
        $this->assertIsInt($this->db->insertID());
        $this->assertEquals(1, $this->db->insertID());
    }

    /**
     * @test
     */
    public function insertFailsGracefully__IncorrectTable()
    {
        $this->expectException(PDOException::class);
        $this->db->insert('wrong_table', [
            'address' => 'A valid address string',
        ]);
    }

    /**
     * @test
     */
    public function insertFailsGracefully__ColumnValidation()
    {
        $this->expectException(PDOException::class);
        $this->db->insert('address', [
            'wrong_column' => 'A valid address string',
        ]);
    }

    private function countAddresses(): int
    {
        $result = $this->db->query('SELECT * FROM address')->fetchAll();

        return count($result);
    }
}
