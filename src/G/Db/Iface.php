<?php

namespace G\Db;

interface Iface
{
    public function select($sql, \Closure $callback = null);

    public function selectOne($sql, \Closure $callback = null);

    public function insert($tableName, array $values = [], array $types = []);

    public function delete($tableName, array $where = [], array $types = []);

    public function update($tableName, array $data, array $where = [], array $types = []);

    public function transactional(\Closure $callback);

    public function beginTransaction();

    public function rollback();
}