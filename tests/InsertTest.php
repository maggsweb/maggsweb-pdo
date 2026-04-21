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
        $this->assertIsString($this->db->insertID());
        $this->assertEquals('1', $this->db->insertID());
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

    /**
     * @test
     */
    public function insertFailsGracefully__NotNullConstraint()
    {
        // `user` column has NOT NULL — prepare succeeds, execute fails,
        // exercising the try/catch path inside execute().
        $result = $this->db->insert('address', [
            'address' => 'missing required user column',
        ]);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->db->getError());
    }

    private function countAddresses(): int
    {
        $result = $this->db->query('SELECT * FROM address')->fetchAll();

        return count($result);
    }
}
