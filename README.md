SQL wrapper
===========

[![Build Status](https://travis-ci.org/gonzalo123/Db.png?branch=master)](https://travis-ci.org/gonzalo123/Db)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c473b8d2-5552-43c5-8aaf-1c83115576e4/big.png)](https://insight.sensiolabs.com/projects/c473b8d2-5552-43c5-8aaf-1c83115576e4)

First we set up de DBAL connection

```php
use Doctrine\DBAL\DriverManager;

// Set up DBAL Connection
$conn = DriverManager::getConnection([
    'driver' => 'pdo_sqlite',
    'memory' => true
]);
```

We also can set up the DBAL connection from a PDO instance
```php
$conn = DriverManager::getConnection(['pdo' => new PDO('sqlite::memory:')]);
```

Then we create the database and populate tables with dummy data

```php
// init database
$conn->exec("CREATE TABLE users (
            userid VARCHAR PRIMARY KEY  NOT NULL ,
            password VARCHAR NOT NULL ,
            name VARCHAR,
            surname VARCHAR
            );");
$conn->exec("INSERT INTO users VALUES('user','pass','Name','Surname');");
$conn->exec("INSERT INTO users VALUES('user2','pass2','Name2','Surname2');");
```

Now we can use the library:

```php
use G\Sql;
use G\Db;

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
```