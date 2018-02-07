<?php 

// Include db class file
// Instantiate $db as a db connection
// include 'index.php';

// Query
// ===========================================================

// Run a query, any query..
//-------------------------

$sql = "CREATE TABLE `names` (
    `id`          int(5)      NOT NULL AUTO_INCREMENT,
    `firstname`   varchar(50) DEFAULT NULL,
    `surname`     varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`)
);";

$db->query($sql)->execute();

$sql = "INSERT INTO `names` VALUES 
    (NULL, 'Joe',  'Bloggs'),
    (NULL, 'John', 'Doe'),
    (NULL, 'Jane', 'Doe');";

$db->query($sql)->execute();

// Run a query and return the results
//------------------------------------

$sql = "SELECT * FROM `names`";

$db->query($sql);

$result = $db->fetchAll();              // Multiple rows
$result  = $db->fetchRow();             // Single row
$result  = $db->fetchAll('Array');      // Multiple rows, returned as a multi-dimensional array
$result  = $db->fetchRow('Array');      // Single row, returned as an array
$result  = $db->fetchOne();             // Single value

// Run a query  using 'bound' params and return results
//-----------------------------------------------------

$sql = "SELECT * FROM `names` WHERE firstname = :firstname";

$db->query($sql);
$db->bind(':firstname', 'John');

$result = $db->fetchAll(); 

// or

$result = $db->query($sql)->bind(':firstname', 'John')->fetchAll(); 


// QUERY RESULTS
//-----------------------------------------------------
// All results can be tested and outputted using $result
if($result){
    foreach($results as $result){
        echo $result->$column;
    }
} else {
    echo $db->getError();
}


// INSERT RECORDS
// ==============================================

// Insert a record using 'bind' params
//------------------------------------

$table   = 'names';
$columns = array('firstname' => 'Fred', 'surname' => 'Bloggs');

$result = $db->insert($table,$columns);

// INSERT STATUS
// -------------
// Success can be tested using $result
if($result){
    echo $db->numRows() . ' records affected';
} else {
    echo $db->getError();
}


// UPDATE RECORDS
// ==============================================

// Update (all) records using 'bind' params 
// ----------------------------------------

$table   = 'names';
$columns = array('firstname' => 'Fred', 'surname' => 'Bloggs');

$result = $db->update($table,$columns);


// Update records using 'bind' params and 'where' string
// ------------------------------------------------------

$table   = 'names';
$columns = array('firstname' => 'Fred 2', 'surname' => 'Bloggs 2');
$where   = "firstname = 'Fred' AND surname = 'Bloggs'";  //'WHERE' is not needed, or spaces

$result = $db->update($table,$columns,$where);


// Update specific records using 'bind' params and 'where' 
//--------------------------------------------------------------

$table   = 'names';
$columns = array('firstname' => 'Fred 2', 'surname' => 'Bloggs 2');
$where   = array('firstname' => 'Fred',   'surname' => 'Bloggs');

$result = $db->update($table,$columns,$where);


// UPDATE STATUS
// -------------
// Success can be tested using $result
if($result){
    echo $db->numRows() . ' records affected';
} else {
    echo $db->getError();
}



// DELETE RECORDS
// ==============================================

// Delete records using a 'where' string
//--------------------------------------

$table  = 'names';
$where  = "surname = 'Doe'";

$result = $db->delete($table,$where);


// Delete records using a 'where' array
//-------------------------------------

$table = 'names';
$where = array('surname' => 'Doe');

$result = $db->delete($table,$where);


// DELETE STATUS
// -------------
// Success can be tested using $result
if($result){
    echo $db->numRows() . ' records affected';
} else {
    echo $db->getError();
}













