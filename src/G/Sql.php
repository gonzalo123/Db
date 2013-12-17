<?php

namespace G;

class Sql
{
    private $string;
    private $parameters;
    private $types;

    public function __construct($string, array $parameters = [], array $types = [])
    {
        $this->string     = $string;
        $this->parameters = $parameters;
        $this->types      = $types;
    }

    public static function createFromTable($tableName, array $parameters = [], array $types = [])
    {
        $sqlString = "SELECT * FROM {$tableName}";

        if (count($parameters) > 0) {
            $where = [];
            foreach ($parameters as $key => $value) {
                $where[] = "{$key} = :{$key}";
            }

            $sqlString .= " WHERE " . implode($where, " AND ");
        }
        return new self($sqlString, $parameters, $types);
    }

    public function createFromTablePgPlSql($storedProcedure, array $parameters = [], array $types = [])
    {
        $sqlString = "SELECT * FROM {$storedProcedure}";

        if (count($parameters) > 0) {
            $where = [];
            foreach ($parameters as $key => $value) {
                $where[] = ":{$key}";
            }

            $sqlString .= "(" . implode($where, ", ") . ")";
        }
        return new self($sqlString, $parameters, $types);
    }

    public static function createFromString($string, array $parameters = [], array $types = [])
    {
        return new self($string, $parameters, $types);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getString()
    {
        return $this->string;
    }

    public function getTypes()
    {
        return $this->types;
    }
}