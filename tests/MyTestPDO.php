<?php

namespace Maggsweb;

use PDO;
use PDOException;

class MyTestPDO extends MyPDO
{

    public function __construct()
    {
        $this->error = false;
        $this->dbh = null;

        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ];

        try {
            $this->dbh = new PDO("sqlite::memory:", null, null, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }


}
