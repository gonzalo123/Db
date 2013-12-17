<?php

use Doctrine\DBAL\DriverManager;
use G\Sql;
use G\Db;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    private $conn;
    /** @var G\Db */
    private $db;

    public function setUp()
    {
        $this->conn = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true
        ]);
        $this->initDatabase();
        $this->db = new Db($this->conn);
    }


    private function initDatabase()
    {
        $this->conn->exec("CREATE TABLE users (
            userid VARCHAR PRIMARY KEY  NOT NULL ,
            password VARCHAR NOT NULL ,
            name VARCHAR,
            surname VARCHAR
            );");
        $this->conn->exec("INSERT INTO users(userid, password, name, surname) VALUES('user','pass','Name','Surname');");
        $this->conn->exec("INSERT INTO users(userid, password, name, surname) VALUES('user2','pass2','Name2','Surname2');");
    }

    public function tearDown()
    {
        $this->conn->exec("DROP TABLE users");
    }

    public function test_simple_select_from_table()
    {
        $data = $this->db->select(Sql::createFromTable('users'));
        $this->assertCount(2, $data);
        $this->assertEquals('user', $data[0]['userid']);
    }

    public function test_simple_select_from_string()
    {
        $data = $this->db->select("select * from users");
        $this->assertCount(2, $data);
        $this->assertEquals('user', $data[0]['userid']);
    }

    public function test_simple_select_from_constructor()
    {
        $data = $this->db->select(Sql::createFromString("select * from users"));
        $this->assertCount(2, $data);
        $this->assertEquals('user', $data[0]['userid']);
    }

    public function test_select_from_table_with_where_clause()
    {
        $data = $this->db->select(Sql::createFromTable('users', ['userid' => 'user2']));
        $this->assertCount(1, $data);
        $this->assertEquals('user2', $data[0]['userid']);
    }

    public function test_select_from_string_with_where_clause()
    {
        $data = $this->db->select(Sql::createFromString('select * from users where userid=:userid', ['userid' => 'user2']));
        $this->assertCount(1, $data);
        $this->assertEquals('user2', $data[0]['userid']);
    }

    public function test_iterate_over_recordset()
    {
        $count = 0;
        $data  = $this->db->select("select * from users", function (&$row) use (&$count) {
            $count++;
            $row['name'] = strtoupper($row['name']);
        });

        $this->assertEquals($count, count($data));
        $this->assertEquals('NAME', $data[0]['name']);
    }

    public function test_select_one()
    {
        $count = $this->db->selectOne("SELECT count(1) from users");
        $this->assertEquals(2, $count);
    }
}