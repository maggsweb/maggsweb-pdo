<?php

/**
 * Define database connection as constants.
 */
define('DBHOST', '127.0.0.1');
define('DBUSER', 'root');
define('DBNAME', 'pdotest');
define('DBPASS', '');

/**
 * Include MyPDO class file.
 */
require_once 'MyPDO.php';

/**
 * Instantiate DB class for use.
 */
$db = new MyPDO(DBHOST, DBUSER, DBNAME, DBPASS);

//////////////////////////////////////////////////////////////////////////////////////////

$sql = 'DROP TABLE `names`;';
$db->query($sql)->execute();

$sql = 'CREATE TABLE `names` (
    `id`          int(5)      NOT NULL AUTO_INCREMENT,
    `firstname`   varchar(50) DEFAULT NULL,
    `surname`     varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`)
);';
$db->query($sql)->execute();

$sql = "INSERT INTO `names` VALUES 
    (NULL, 'Joe',  'Bloggs'),
    (NULL, 'John', 'Doe'),
    (NULL, 'Jane', 'Doe');";
$db->query($sql)->execute();

$sql = 'SELECT * FROM `names`';
$db->query($sql);
$result = $db->fetchAll();

echo '<pre>';
print_r($result);
echo '</pre>';
