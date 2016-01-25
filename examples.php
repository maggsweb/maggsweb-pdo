<?php 

// Include db class file
// Instantiate $db as a db connection
include 'index.php';
//-----------------------------------------------------------------

echo "<h2>Query</h2>";

//-----------------------------------------------------------------
echo "<h3>Run a query, any query..</h3>";

//$sql = "CREATE TABLE `names` (
//    `id`          int(5)      NOT NULL AUTO_INCREMENT,
//    `firstname`   varchar(50) DEFAULT NULL,
//    `surname`     varchar(50) DEFAULT NULL,
//    PRIMARY KEY (`id`)
//);";
//
//$sql = "INSERT INTO `names` VALUES 
//    (NULL, 'Chris',  'Maggs'),
//    (NULL, 'Chris2', 'Maggs2'),
//    (NULL, 'Chris3', 'Maggs3');";
//
//$db->query($sql);
//
//if($db->execute() ){
//    echo $db->rowCount() . ' records inserted';
//} else {
//    echo $db->getError();
//}

//-----------------------------------------------------------------
echo "<h3>Run a query and return the results</h3>";

$sql = "SELECT * FROM `names`";

$db->query($sql);

$results = $db->fetchAll(); // Multiple rows
//$result  = $db->fetchOne();  // Single rows
//$results = $db->fetchAll('Array');  // Multiple rows, returned as a multi-dimensional array
//$result  = $db->fetchOne('Array');  // Single row, returned as an array

if($results){
    echo $db->rowCount() . ' rows returned';
} else {
    echo $db->getError();
}














//-----------------------------------------------------------------
echo "<hr><p>3. Run a query  using 'bind' params and return results</p>";
//$sql = "SELECT * FROM twitter WHERE title = :title";
//$db->query($sql);
//$db->bind(':title', 'aaa');
//dumpr($db->fetchAll());
//dumpr($db->rowCount());
//dumpr($db->getError());
//-----------------------------------------------------------------
echo "<h2>Select</h2>";
//-----------------------------------------------------------------
echo "<hr><p>4. Select all columns | named columns and return multiple rows using 'select'</p>";
//$table = 'twitter';
//$columns = 'title, link';
//$results = $db->select($table,$columns);
//dumpr($results);
//dumpr($db->rowCount());
//dumpr($db->getError());
//-----------------------------------------------------------------
echo "<hr><p>5. Select multiple rows using 'select' and optional 'where' string|array</p>";
//$table = 'twitter';
//$columns = '*';
//#$where = false;
//#$where = "link='eee'";
//$where = array();
//$where['title'] = 'aaa';
//$where['link']  = 'ccc';
//dumpr($db->select($table,$columns,$where));
//dumpr($db->getQuery());
//dumpr($db->rowCount());
//dumpr($db->getError());
//-----------------------------------------------------------------
echo "<hr><p>6. Select one row using 'selectRow' and optional 'where' string|array</p>";
//$table = 'twitter';
//$columns = '*';
//#$where = false;
//#$where = "link='eee'";
//$where = array();
//$where['link']  = 'eee';
//dumpr($db->selectRow($table,$columns,$where));
//dumpr($db->getQuery());
//dumpr($db->getError());
//-----------------------------------------------------------------
echo "<hr><p>7. Select multiple rows using 'selectRow' and order the results</p>";
//$table = 'twitter';
//$columns = '*';
//#$where = false;
//#$where = "link='eee'";
//$where = array();
//$where['link']  = 'ddd';
//dumpr($db->selectRow($table,$columns,$where));
//dumpr($db->getQuery());
//dumpr($db->getError());







// TODO    add order by and direction to all selects





//-----------------------------------------------------------------
echo "<h2>Insert</h2>";
//-----------------------------------------------------------------
echo "<hr><p>5. Insert a record using SQL & 'query'</p>";
//$sql = "INSERT INTO twitter (title,link,description) VALUES ('A title', 'A link', 'A Description')";
//$db->query($sql);
//dumpr($db->execute());
//dumpr($db->getError());
//-----------------------------------------------------------------
echo "<hr><p>6. Insert a record using 'bind' params and 'query'</p>";
//$sql = "INSERT INTO twitter (title,link,description) VALUES (:title, :link, :description)";
//$db->query($sql);
//$db->bind(':title', 'ttiittllee');
//$db->bind(':link', 'lliinnkk');
//$db->bind(':description', 'descriptiondescriptiondescription');
//dumpr($db->execute());
//dumpr($db->getError());
//-----------------------------------------------------------------
echo "<hr><p>7. Insert a record using 'bind' params and 'insert'</p>";
//$table = 'twitter';
//$columns = array();
//$columns['title']       = 'Title (2)';
//$columns['link']        = 'Link (2)';
//$columns['xxx']        = 'xxx'; // test for error with incorrect column
//$columns['description'] = 'Desc (2)';
//$columns['pubDate']     = date('Y-m-d h:i:s');
//$insert = $db->insert($table,$columns);
//$insert = $db->insert($table); // test for error with missing $columns
//dumpr($insert);
//dumpr($db->getError());
//-----------------------------------------------------------------

echo "<h2>Update</h2>";
//-----------------------------------------------------------------
echo "<hr><p>8. pdate a record using 'bind' params and 'query', and show number of records updated</p>";
//$sql = "UPDATE twitter SET title = :title";
//$db->query($sql);
//$db->bind(':title', 'Chris3');
//dumpr($db->execute());
//dumpr($db->getQuery());
//dumpr($db->getError());
//dumpr($db->rowCount());
//-----------------------------------------------------------------
echo "<hr><p>9. Update all record using 'bind' params and 'update', and show number of records updated.</p>";
//$table = 'twitter';
////$table = 'twitterX'; // test incorrect table name
//$columns = array();
//$columns['title'] = 'Title (20013)';
//$columns['link']  = 'Link  (2001)';
//dumpr($db->update($table,$columns));
//dumpr($db->getQuery());
//dumpr($db->getError());
//dumpr($db->rowCount());
//-----------------------------------------------------------------
echo "<hr><p>10. Update specific records using a 'where' string and 'update', and show number of records updated.</p>";
//$table = 'twitter';
////$table = 'twitterX'; // test incorrect table name
//$columns = array();
//$columns['title'] = 'UPDATED';
//$columns['link']  = 'CHANGED';
//$where = "title = 'update' AND link = 'change'";  //'WHERE' is not needed, or spaces
//dumpr($db->update($table,$columns,$where));
//dumpr($db->getQuery());
//dumpr($db->getQuery());
//dumpr($db->getError());
//dumpr($db->rowCount());
//-----------------------------------------------------------------
echo "<hr><p>11. Update specific records using a 'where' array and 'update', and show number of records updated.</p>";
//$table = 'twitter';
//$columns = array();
//$columns['title'] = 'UPDATED WHERE';
//$columns['link']  = 'CHANGED WHERE';
//$where = array();
//$where['title'] = 'update';
//$where['link']  = 'change';
//dumpr($db->update($table,$columns,$where));
//dumpr($db->getQuery());
//dumpr($db->getError());
//dumpr($db->rowCount());
//-----------------------------------------------------------------
echo "<h2>Delete</h2>";
//-----------------------------------------------------------------
echo "<hr><p>12. Delete records using a 'where' string, and show number of records deleted.</p>";
//$table = 'twitter';
//$where = "pubDate = '0000-00-00 00:00:00'";
//dumpr($db->delete($table,$where));
//dumpr($db->getQuery());
//dumpr($db->getError());
//dumpr($db->rowCount());
//-----------------------------------------------------------------
echo "<hr><p>13. Delete records using a 'where' array, and show number of records deleted.</p>";
//$table = 'twitter';
//$where = array();
//$where['title'] = 'update';
//$where['link']  = 'delete';
//dumpr($db->delete($table,$where));
//dumpr($db->getQuery());
//dumpr($db->getQuery());
//dumpr($db->getError());
//dumpr($db->rowCount());
//-----------------------------------------------------------------


