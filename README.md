# MyPDO
A 'Work in progress' Database wrapper for PHP using MySQL and PDO
<hr>

### Table of Contents
**[Initialization](#initialization)**  
**[Raw Query](#raw-query)**  
**[SQL Select](#sql-select)**  





### Initialization
To use this class,  set your database connection constants, download and include 'MyPDO.php' into your project and instantiate a database connection.

```php
define('DB_HOST', '');  //eg: 127.0.0.1
define('DB_USER', '');  //eg: root
define('DB_NAME', '');  //eg: admin
define('DB_PASS', '');  //eg: password

require_once ('MyPDO.php');

$db = new MyPDO();
```



### Raw Query
To execute a raw string of SQL, pass the complete SQL string to **->query**

```php
$sql = "INSERT INTO `names` VALUES 
    (NULL, 'Chris',  'Maggs'),
    (NULL, 'Chris2', 'Maggs2'),
    (NULL, 'Chris3', 'Maggs3');";

$db->query($sql);
```
Call **->execute** to execute the query.  This returns true|false;

On success, call **->rowCount** to return the number of rows updated (if applicable)

On failure, call **->getError** to display the SQL error message

```php
if($db->execute() ){
    echo $db->rowCount() . ' records inserted';
} else {
    echo $db->getError();
}
```




### Raw Query, returning results
To return the results from **->query** for use, call **->fetchAll()** for multiple rows or **->fetchOne** for a single row. Results are returned as an Array of Objects.  Optionally, pass 'Array' to the fetch functions to return results as an Array of Arrays.

```php
$sql = "SELECT * FROM `names`";

$db->query($sql);

$results = $db->fetchAll();     // Multiple rows
//$result  = $db->fetchOne();   // Single row

//$results = $db->fetchAll('Array');  // Multiple rows, returned as a multi-dimensional array
//$result  = $db->fetchOne('Array');  // Single row, returned as an array
```

On success, **$result** will be an Object Array (fetchAll) or an Object (fetchOne)

On failure, call **->getError** to display the SQL error message

```php
if($results){
    echo $db->rowCount() . ' rows returned';
} else {
    echo $db->getError();
}
```




### Raw Query, using 'bound' parameters
To bind parameters to a query, pass the column identifier and value to **->bind()**.  Repeat this for each bound parameter in order.

```php
$sql = "SELECT * FROM `names` WHERE firstname = :firstname";

$db->query($sql);

$db->bind(':firstname', 'Chris');
```

Execute the query as above.



###SQL Select



