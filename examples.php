<?php 

// Include db class file
// Instantiate $db as a db connection
// include 'index.php';

//$sql = "CREATE TABLE `names` (
//    `id`          int(5)      NOT NULL AUTO_INCREMENT,
//    `firstname`   varchar(50) DEFAULT NULL,
//    `surname`     varchar(50) DEFAULT NULL,
//    PRIMARY KEY (`id`)
//);";


// Query
// ===========================================================

// Run a query, any query..
//-------------------------

$sql = "INSERT INTO `names` VALUES 
    (NULL, 'Joe',  'Bloggs'),
    (NULL, 'John', 'Doe'),
    (NULL, 'Jane', 'Doe');";

$db->query($sql);

$results = $db->execute();


// Run a query and return the results
//------------------------------------

$sql = "SELECT * FROM `names`";

$db->query($sql);

$results = $db->fetchAll();     // Multiple rows
//$result  = $db->fetchOne();   // Single row
//$results = $db->fetchAll('Array');  // Multiple rows, returned as a multi-dimensional array
//$result  = $db->fetchOne('Array');  // Single row, returned as an array


// Run a query  using 'bound' params and return results
//-----------------------------------------------------

$sql = "SELECT * FROM `names` WHERE firstname = :firstname";

$db->query($sql);
$db->bind(':firstname', 'John');

$results = $db->fetchAll(); 


// QUERY RESULTS
//-----------------------------------------------------
// All results can be tested and outputted using $result
if($results||$result){
    echo $db->rowCount() . ' records affected';
    //foreach($results as $result){
    //    echo $result->{$column};
    //}
} else {
    echo $db->getError();
}



// SELECT
// =================================================================

// Select all columns and return multiple rows
//--------------------------------------------

$table   = 'names';

$results = $db->selectAll($table);


// Select specific columns and return multiple rows
//-------------------------------------------------

$table   = 'names';
$columns = 'firstname, surname';

$results = $db->selectAll($table,$columns);


// Select specific columns and return multiple rows using a 'where' string
//------------------------------------------------------------------------

$table   = 'names';
$columns = 'firstname';
$where   = "surname LIKE 'D%'";

$results = $db->selectAll($table,$columns,$where);


// Select all columns and return multiple rows using a 'where' array
//------------------------------------------------------------------

$table   = 'names';
$where   = array('surname' => 'Doe');

$results = $db->selectAll($table,false,$where);


// Select one row using a 'where' string
//--------------------------------------

$table  = 'names';
$where  = array('firstname' => 'John', 'surname' => 'Doe');

$result = $db->selectRow($table,false,$where);


// Select one row using a 'where' array
//-------------------------------------

$table   = 'names';
$columns = 'id';
$where   = array('firstname' => 'John', 'surname' => 'Doe');

$result  = $db->selectRow($table,$columns,$where);


// Select one value using a 'where' array
//-------------------------------------

$table   = 'names';
$columns = 'id';
$where   = array('firstname' => 'John', 'surname' => 'Doe');

$result  = $db->selectOne($table,$columns,$where);


// Select multiple rows and order the results
//-------------------------------------------

$table = 'names';
$extra = 'ORDER BY surname ASC';

$result = $db->selectAll($table,false,false,$extra);


// SELECT RESULTS
// ---------------
// All results can be tested and outputted using $result
if($results||$result){
    echo $db->rowCount() . ' records affected';
    //foreach($results as $result){
    //    echo $result->{$column};
    //}
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
// Success can be tested using $insert
if($result){
    echo $db->rowCount() . ' records affected';
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
// Success can be tested using $update
if($result){
    echo $db->rowCount() . ' records affected';
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
    echo $db->rowCount() . ' records affected';
} else {
    echo $db->getError();
}













