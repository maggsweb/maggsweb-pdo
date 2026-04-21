<?php

declare(strict_types=1);

include_once 'BaseTestCase.php';

class SelectTest extends BaseTestCase
{
    /**
     * @test
     */
    public function selectAllRowsAsObjects()
    {
        $result = $this->db->query('SELECT * FROM users')->fetchAll();
        $this->assertCount(4, $result);
        $this->assertIsObject($result[1]);
        $this->assertIsNotArray($result[1]);
    }

    /**
     * @test
     */
    public function selectAllRowsAsArrays()
    {
        $result = $this->db->query('SELECT * FROM users')->fetchAll('Array');
        $this->assertCount(4, $result);
        $this->assertIsNotObject($result[1]);
        $this->assertIsArray($result[1]);
    }

    /**
     * @test
     */
    public function selectOneRowAsObject()
    {
        $result = $this->db->query('SELECT * FROM users WHERE id = 1')->fetchRow();
        $this->assertIsObject($result);
        $this->assertIsNotArray($result);
        $object = new stdClass();
        $object->id = '1';
        $object->name = 'Clare';
        $object->age = '50';
        $this->assertEquals($object, $result);
    }

    /**
     * @test
     */
    public function selectOneRowAsArray()
    {
        $result = $this->db->query('SELECT * FROM users WHERE id = 1')->fetchRow('Array');
        $this->assertIsNotObject($result);
        $this->assertIsArray($result);
        $array = [
            'id'   => '1',
            'name' => 'Clare',
            'age'  => '50',
        ];
        $this->assertEquals($array, $result);
    }

    /**
     * @test
     */
    public function selectOneColumn()
    {
        $result = $this->db->query('SELECT name FROM users WHERE id = 1')->fetchOne();
        $this->assertIsNotObject($result);
        $this->assertIsNotArray($result);
        $this->assertIsString($result);
        $this->assertEquals('Clare', $result);
    }

    /**
     * @test
     */
    public function selectRowUsingBoundParameter()
    {
        $result = $this->db->query('SELECT * FROM users WHERE name = :name')
            ->bind(':name', 'Chris')
            ->fetchAll();

        $this->assertCount(1, $result);
        $this->assertIsArray($result);
        $this->assertIsObject($result[0]);
        $this->assertIsNotArray($result[0]);

        $object = new stdClass();
        $object->id = '2';
        $object->name = 'Chris';
        $object->age = '40';

        $array = [];
        $array[] = $object;

        $this->assertEquals($array, $result);
    }

    /**
     * @test
     */
    public function selectRowUsingBoundParameters()
    {
        $result = $this->db->query('SELECT * FROM users WHERE name = :name1 OR name = :name2')
            ->bind(':name1', 'Clare')
            ->bind(':name2', 'Chris')
            ->fetchAll();

        $this->assertCount(2, $result);
        $this->assertIsArray($result);
        $this->assertIsObject($result[0]);
        $this->assertIsNotArray($result[0]);
        $this->assertIsObject($result[1]);
        $this->assertIsNotArray($result[1]);
    }

    /**
     * @test
     */
    public function fetchOneReturnsNullWhenNoRows()
    {
        $result = $this->db->query('SELECT name FROM users WHERE id = 999')->fetchOne();
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function fetchAllReturnsEmptyArrayWhenNoRows()
    {
        $result = $this->db->query('SELECT * FROM users WHERE id = 999')->fetchAll();
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function fetchAllAsArrayReturnsEmptyArrayWhenNoRows()
    {
        $result = $this->db->query('SELECT * FROM users WHERE id = 999')->fetchAll('Array');
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function fetchRowReturnsFalseWhenNoRows()
    {
        $result = $this->db->query('SELECT * FROM users WHERE id = 999')->fetchRow();
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function fetchRowAsArrayReturnsFalseWhenNoRows()
    {
        $result = $this->db->query('SELECT * FROM users WHERE id = 999')->fetchRow('Array');
        $this->assertFalse($result);
    }
}
