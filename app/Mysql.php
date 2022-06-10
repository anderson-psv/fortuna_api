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
                'host'     => 'db-mysql',
                'dbname'   => 'db_fortuna',
                'user'     => 'root',
                'password' => 'root-fortuna',
                'charset'  => 'utf8'
            ]
        );
    }

    public function getDb():Connection
    {
        return $this->db_conn;
    }
}