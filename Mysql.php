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
                'password' => 'some_root_password_should_be_here',
                'charset'  => 'utf8'
            ]
        );
    }    
}