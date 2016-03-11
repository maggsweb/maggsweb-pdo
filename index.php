<?php 

/**
 * Define database connection as constants
 */
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_NAME', 'pdotest');
define('DB_PASS', '');

/**
 * Include MyPDO class file
 */
require_once 'MyPDO.php';


/**
 * Instantiate DB class for use
 */
$db = new MyPDO();


//See examples.php for usage examples

//////////////////////////////////////////////////////////////////////////////////////////

$sql = "DROP TABLE `names`;";

$db->query($sql)->execute();

//---------------------------------------------------------

$sql = "CREATE TABLE `names` (
    `id`          int(5)      NOT NULL AUTO_INCREMENT,
    `firstname`   varchar(50) DEFAULT NULL,
    `surname`     varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`)
);";

$db->query($sql)->execute();

//---------------------------------------------------------

$sql = "INSERT INTO `names` VALUES 
    (NULL, 'Joe',  'Bloggs'),
    (NULL, 'John', 'Doe'),
    (NULL, 'Jane', 'Doe');";

$db->query($sql)->execute();

//---------------------------------------------------------

$sql = "SELECT * FROM `names` WHERE firstname = :firstname";

$result = $db->query($sql)->bind(':firstname', 'John')->fetchAll();

if($result){
    echo $db->rowCount() . ' records returned';
} else {
    echo $db->getError();
}

//---------------------------------------------------------


