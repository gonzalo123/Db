<?php

namespace G;

use Doctrine\DBAL\Connection;
use G\Db\SelectOneIface;
use G\Db\Iface;

class Db implements Iface
{
    private $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param Sql|string $sql
     * @param \Closure   $callback
     * @return array
     */
    public function select($sql, \Closure $callback = null)
    {
        if ($sql instanceof Sql) {
            $sqlString  = $sql->getString();
            $parameters = $sql->getParameters();
            $types      = $sql->getTypes();
        } else {
            $sqlString  = $sql;
            $parameters = [];
            $types      = [];
        }

        $statement = $this->conn->executeQuery($sqlString, $parameters, $types);
        $data      = $statement->fetchAll();
        if (!is_null($callback) && count($data) > 0) {
            $out = [];
            foreach ($data as $row) {
                if (call_user_func_array($callback, [&$row]) !== false) {
                    $out[] = $row;
                }
            }
            $data = $out;
        }

        return $data;
    }

    /**
     * @param Sql|string $sql
     * @param \Closure   $callback
     * @return array
     */
    public function selectOne($sql, \Closure $callback = null)
    {
        $data = $this->select($sql, $callback);
        return count($data) > 0 ? array_values($data[0])[0] : null;
    }

    public function insert($tableName, array $values = [], array $types = [])
    {
        $this->conn->insert($tableName, $values, $types);
    }

    public function delete($tableName, array $where = [], array $types = [])
    {
        $this->conn->delete($tableName, $where, $types);
    }

    public function update($tableName, array $data, array $where = [], array $types = [])
    {
        $this->conn->update($tableName, $data, $where, $types);
    }

    public function transactional(\Closure $callback)
    {
        $out = null;
        $this->conn->beginTransaction();
        try {
            $out = $callback($this);
            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }

        return $out;
    }

    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    public function rollback()
    {
        $this->conn->rollBack();
    }
}