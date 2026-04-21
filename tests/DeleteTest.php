<?php

declare(strict_types=1);

include_once 'BaseTestCase.php';

class DeleteTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->db->query('DELETE FROM address')->execute();
        $this->db->query('INSERT INTO address VALUES (null, 1, "Clare address", "CF10");')->execute();
        $this->db->query('INSERT INTO address VALUES (null, 1, "Clare additional address", "CF20");')->execute();
        $this->db->query('INSERT INTO address VALUES (null, 2, "Chris main address", "CF10");')->execute();
        $this->db->query('INSERT INTO address VALUES (null, 2, "Chris additional address", "CF20");')->execute();
    }

    /**
     * @test
     */
    public function deleteUsingAWhereArray()
    {
        $delete = $this->db->delete('address', [
            'postCode' => 'CF20',
        ]);
        $this->assertTrue($delete);
        $this->assertEquals(2, $this->db->numRows());
        $this->assertEquals(2, $this->countAddresses());
    }

    /**
     * @test
     */
    public function deleteUsingAWhereString()
    {
        $delete = $this->db->delete('address', "WHERE address LIKE '%Chris%'");
        $this->assertTrue($delete);
        $this->assertEquals(2, $this->db->numRows());
        $this->assertEquals(2, $this->countAddresses());
    }

    /**
     * @test
     */
    public function deleteFailsGracefully__IncorrectTable()
    {
        $this->expectException(PDOException::class);
        $this->db->delete('wrong_table', [
            'wrong_column_name' => true,
        ]);
    }

    /**
     * @test
     */
    public function deleteMatchingNoRowsReportsZero()
    {
        $delete = $this->db->delete('address', ['user' => 999]);
        $this->assertTrue($delete);
        $this->assertEquals(0, $this->db->numRows());
    }

    private function countAddresses(): int
    {
        $result = $this->db->query('SELECT * FROM address')->fetchAll();

        return count($result);
    }
}
