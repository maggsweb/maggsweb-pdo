<?php 

define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');

require_once 'classes/MyPDO.class.php';

//////////////////////////////////////////////////////////////////////////////////////////

$db = new MyPDO();
dumpr($db);

echo "<h2>Query</h2>";
//-----------------------------------------------------------------
echo "<hr><p>1. Select multiple rows using SQL & 'query'</p>";
//$sql = "SELECT * FROM siteSettings";
//$db->query($sql);
//$results = $db->fetchAll();
//dumpr($results);
//$count = $db->rowCount();
//dumpr($count);
//dumpr($db->getError());
//-----------------------------------------------------------------
echo "<hr><p>2. Select 1 row using SQL & 'query'</p>";
//$sql = "SELECT * FROM siteSettings LIMIT 1";
//$db->query($sql);
//$result = $db->fetchRow();
//dumpr($result);
//$count = $db->rowCount();
//dumpr($count);
//dumpr($db->getError());
//-----------------------------------------------------------------
echo "<hr><p>3. Select multiple rows using a 'bind' param & 'query'</p>";
//$sql = "SELECT * FROM siteSettings WHERE settingsGroup = :settingsGroup";
//$db->query($sql);
//$db->bind(':settingsGroup', 'General');
//$results = $db->fetchAll();
//dumpr($results);
//$count = $db->rowCount();
//dumpr($count);
//dumpr($db->getError());
//-----------------------------------------------------------------
echo "<hr><p>4. Select single value using a 'bind' param & 'query'</p>";
//$sql = "SELECT constantName FROM siteSettings WHERE siteSettingsID=:siteSettingsID";
//$db->query($sql);
//$db->bind(':siteSettingsID',484);
//$resultOne = $db->fetchOne();
//dumpr($resultOne);
//dumpr($db->getError());
//-----------------------------------------------------------------
echo "<hr><p>5. Insert a record using SQL & 'query'</p>";
//$sql = "INSERT INTO twitter (title,link,description) VALUES ('A title', 'A link', 'A Description')";
//$db->query($sql);
//dumpr($db->execute());
//dumpr($db->getError());
//-----------------------------------------------------------------

echo "<h2>Insert</h2>";
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
//$columnArray = array();
//$columnArray['title']       = 'Title (2)';
//$columnArray['link']        = 'Link (2)';
//$columnArray['xxx']        = 'xxx'; // test for error with incorrect column
//$columnArray['description'] = 'Desc (2)';
//$columnArray['pubDate']     = date('Y-m-d h:i:s');
//$insert = $db->insert($table,$columnArray);
//$insert = $db->insert($table); // test for error with missing $columnArray
//dumpr($insert);
//dumpr($db->getError());
//-----------------------------------------------------------------

echo "<h2>Update</h2>";
//-----------------------------------------------------------------
echo "<hr><p>8. pdate a record using 'bind' params and 'query', and show number of records updated</p>";
//$sql = "UPDATE twitter SET title = :title";
//$db->query($sql);
//$db->bind(':title', 'Chris2');
//dumpr($db->execute());
//dumpr($db->getError());
//dumpr($db->rowCount());
//-----------------------------------------------------------------
echo "<hr><p>9. Update all record using 'bind' params and 'update', and show number of records updated.</p>";
//$table = 'twitter';
////$table = 'twitterX'; // test incorrect table name
//$columnArray = array();
//$columnArray['title'] = 'Title (20013)';
//$columnArray['link']  = 'Link  (2001)';
//$update = $db->update($table,$columnArray);
//dumpr($update);
//dumpr($db->getError());
//dumpr($db->rowCount());
//-----------------------------------------------------------------
echo "<hr><p>10. Update specific records using a 'where' string and 'update', and show number of records updated.</p>";
//$table = 'twitter';
////$table = 'twitterX'; // test incorrect table name
//$columnArray = array();
//$columnArray['title'] = 'UPDATED';
//$columnArray['link']  = 'CHANGED';
//$where = "title = 'update' AND link = 'change'";  //'WHERE' is not needed, or spaces
//$update = $db->update($table,$columnArray,$where);
//dumpr($update);
//dumpr($db->getQuery());
//dumpr($db->getError());
//dumpr($db->rowCount());
//-----------------------------------------------------------------
echo "<hr><p>11. Update specific records using a 'where' array and 'update', and show number of records updated.</p>";
//$table = 'twitter';
//$columnArray = array();
//$columnArray['title'] = 'UPDATED WHERE';
//$columnArray['link']  = 'CHANGED WHERE';
//$where = array();
//$where['title'] = 'update';
//$where['link']  = 'change';
//$update = $db->update($table,$columnArray,$where);
//dumpr($update);
//dumpr($db->getQuery());
//dumpr($db->getError());
//dumpr($db->rowCount());
//-----------------------------------------------------------------




echo "<h2>Delete</h2>";
//-----------------------------------------------------------------





echo "<h2>Clean</h2>";
//-----------------------------------------------------------------
// truncate
// empty
// optimise




