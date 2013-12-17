<?php

include __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use G\Sql;
use G\Db;

// Set up DBAL Connection
$conn = DriverManager::getConnection([
    'driver' => 'pdo_sqlite',
    'memory' => true
]);

// Set up DBAL connection from a PDO object
//$conn = DriverManager::getConnection(['pdo' => new PDO('sqlite::memory:')]);

// init database
$conn->exec("CREATE TABLE users (
            userid VARCHAR PRIMARY KEY  NOT NULL ,
            password VARCHAR NOT NULL ,
            name VARCHAR,
            surname VARCHAR
            );");
$conn->exec("INSERT INTO users VALUES('user','pass','Name','Surname');");
$conn->exec("INSERT INTO users VALUES('user2','pass2','Name2','Surname2');");

// setting up G\Db with connection
$db = new Db($conn);

// select from string
$data = $db->select("select * from users");
// select from table
$data = $db->select(SQL::createFromTable("users"));
// select from table with where clause
$data = $db->select(SQL::createFromTable("users", ['userid' => 'user2']));
// iterating select statement changing the recordset
$data = $db->select(SQL::createFromTable("users"), function (&$row) {
    $row['name'] = strtoupper($row['name']);
});
// transactions
$db->transactional(function (Db $db) {
    $userId = 'temporal';

    $db->insert('users', [
        'USERID'   => $userId,
        'PASSWORD' => uniqid(),
        'NAME'     => 'name3',
        'SURNAME'  => 'name3'
    ]);

    $db->update('users', ['NAME' => 'updatedName'], ['USERID' => $userId]);
    $db->delete('users', ['USERID' => $userId]);
});