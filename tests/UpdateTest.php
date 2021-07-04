<?php

declare(strict_types=1);

include_once 'BaseTestCase.php';

class UpdateTest extends BaseTestCase
{

    public function testSetupUpdate()
    {
        $this->db->query('DELETE FROM address')->execute();
        $this->assertEquals(0, $this->countAddresses());
        $this->db->query('INSERT INTO address VALUES (null, 1, "Clare address", "CF10");')->execute();
        $this->db->query('INSERT INTO address VALUES (null, 1, "Clare additional address", "CF20");')->execute();
        $this->db->query('INSERT INTO address VALUES (null, 2, "Chris main address", "CF10");')->execute();
        $this->db->query('INSERT INTO address VALUES (null, 2, "Chris additional address", "CF20");')->execute();
        $this->assertEquals(4, $this->countAddresses());
    }

    /**
     * @test
     */
    public function updateUsingAWhereArray()
    {
        $update = $this->db->update('address', [
           'postCode' => 'CF20'
        ], [
            'user' => 2
        ]);
        $this->assertTrue($update);
        $this->assertEquals(2, $this->db->numRows());
        $this->assertEquals(4,$this->countAddresses());
        $this->assertEquals(2,$this->countAddressesWhere(['user' => 1]));
        $this->assertEquals(3,$this->countAddressesWhere(['postCode' => 'CF20']));
        $this->assertEquals(1,$this->countAddressesWhere(['postCode' => 'CF20', 'user' => 1]));
    }

    /**
     * @test
     */
    public function updateUsingAWhereString()
    {
        $update = $this->db->update('address', [
            'postCode' => 'CF30'
        ], "WHERE address LIKE '%additional%'");
        $this->assertTrue($update);
        $this->assertEquals(2, $this->db->numRows());
        $this->assertEquals(4,$this->countAddresses());
        $this->assertEquals(2,$this->countAddressesWhere(['postCode' => 'CF30']));
        $this->assertEquals(1,$this->countAddressesWhere(['postCode' => 'CF30', 'user' => 1]));
    }

    /**
     * @test
     */
    public function updateFailsGracefully__NoColumns()
    {
        $this->expectException(PDOException::class);
        $this->db->update('address', []);
    }

    /**
     * @test
     */
    public function updateFailsGracefully__IncorrectColumns()
    {
        $this->expectException(PDOException::class);
        $this->db->update('address', [
            'wrong_column_name' => true
        ]);
    }

    /**
     * @test
     */
    public function updateFailsGracefully__IncorrectTable()
    {
        $this->expectException(PDOException::class);
        $this->db->update('wrong_table', [
            'wrong_column_name' => true
        ]);
    }

    private function countAddresses(): int
    {
        $result = $this->db->query('SELECT * FROM address')->fetchAll();
        return count($result);
    }

    private function countAddressesWhere(array $where): int
    {
        $where_sql = [];
        foreach(array_keys($where) as $key) {
            $where_sql[] = "$key = :$key";
        }
        $sql = 'SELECT * FROM address WHERE '.implode(' AND ', $where_sql);

        $this->db->query($sql);
        foreach($where as $key => $value) {
            $this->db->bind($key, $value);
        }
        $result = $this->db->fetchAll();
        return count($result);
    }



}