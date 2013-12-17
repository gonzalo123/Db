<?php

use Doctrine\DBAL\DriverManager;
use G\Db\Iface;
use G\Sql;
use G\Db;

class TransactionDbalTest extends \PHPUnit_Framework_TestCase
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

        $this->conn->exec("CREATE TABLE users (
            userid VARCHAR PRIMARY KEY  NOT NULL ,
            password VARCHAR NOT NULL ,
            name VARCHAR,
            surname VARCHAR
            );");

        $this->db = new Db($this->conn);
    }

    public function tearDown()
    {
        $this->conn->exec("DROP TABLE users");
    }

    public function test_insert_update_delete()
    {
        $this->db->transactional(function (Iface $db) {
            $this->assertEquals(0, $db->selectOne("select count(1) from users"));

            $db->insert('users', [
                'userid'   => 'uid1',
                'password' => 'password',
                'name'     => 'name',
                'surname'  => 'surname'
            ]);

            $this->assertEquals(1, $db->selectOne("select count(1) from users"));

            $this->assertEquals('name', $db->select(Sql::createFromTable('users', ['userid' => 'uid1']))[0]['name']);

            $db->update('users', ['name' => 'updatedName'], ['userid' => 'uid1']);

            $this->assertEquals('updatedName', $db->select(Sql::createFromTable('users', ['userid' => 'uid1']))[0]['name']);

            $db->delete('users', ['userid'   => 'uid1']);

            $this->assertEquals(0, $db->selectOne("select count(1) from users"));
        });
    }
}