<?php

namespace Fortuna;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class Mysql
{
    private Connection $db_conn;

    function __construct()
    {
        $this->db_conn = DriverManager::getConnection(
            [
                'driver'   => 'pdo_mysql',
                'host'     => 'mysql_db_service',
                'dbname'   => 'db_fortuna',
                'user'     => 'root',
                'password' => 'some_root_password_should_be_here',
                'charset'  => 'utf8'
            ]
        );
    }

    public function getDb():Connection
    {
        return $this->db_conn;
    }
}