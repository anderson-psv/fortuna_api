<?php

namespace Fortuna;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class Mysql
{
    function __construct()
    {
        return new DriverManager(
            [
                'driver'   => 'pdo_mysql',
                'host'     => 'mysql_db_service',
                'dbname'   => 'db_fortuna',
                'user'     => 'root',
                'password' => '',
                'charset'  => 'utf8'
            ]
        );
    }    
}