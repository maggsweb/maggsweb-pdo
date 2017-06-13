# MyPDO

A simple PHP database wrapper for MySQL using PDO

<hr>

### Table of Contents
**[Initialization](#initialization)**  
**[Query](#query)**  
**[Insert Records](#insert-records)**  
**[Update Records](#update-records)**  
**[Delete Records](#delete-records)**  

<hr>

## Initialization

To use this class,  set your database connection constants, download and include 'MyPDO.php' into your project and instantiate a database connection.

```php
define('DB_HOST', '');  //eg: 127.0.0.1
define('DB_USER', '');  //eg: root
define('DB_NAME', '');  //eg: admin
define('DB_PASS', '');  //eg: password

require_once ('MyPDO.php');

$db = new MyPDO();
```


<hr>

## Query

#### Run a query, any query..

To execute a string of SQL, pass the complete SQL string to **->query**

```php
$sql = "INSERT INTO `names` VALUES 
    (NULL, 'Chris',  'Maggs'),
    (NULL, 'Chris2', 'Maggs2'),
    (NULL, 'Chris3', 'Maggs3');";

$db->query($sql);

$result = $db->execute();
```
Call **->execute** to execute the query.  This returns true|false;



#### Run a query and return the results

To return the results from **->query** for use call **->fetchAll()** for multiple rows, **->fetchRow** for a single row or **->fetchOne** for a single value. Results are returned as an Array of Objects an Object or a value.  Optionally, passing 'Array' to the fetch functions will return results as a Multi-dimensional Array.

```php
$sql = "SELECT * FROM `names`";

$db->query($sql);

$results = $db->fetchAll();             // Multiple rows
//$result  = $db->fetchRow();           // Single row
//$result  = $db->fetchAll('Array');    // Multiple rows, returned as a multi-dimensional array
//$result  = $db->fetchRow('Array');    // Single row, returned as an array
//$result  = $db->fetchOne();           // Single value
```

On success, **$result** will be an Object Array (fetchAll) or an Object (fetchRow) or a value (fetchOne)

On failure, call **->getError** to display the SQL error message


#### Run a query  using 'bound' params and return results

To bind parameters to a query, pass the column identifier and value to **->bind()**.  Repeat this for each bound parameter in order.

```php
$sql = "SELECT * FROM `names` WHERE firstname = :firstname";

$db->query($sql);

$db->bind(':firstname', 'Chris');

$results = $db->fetchAll(); 

or

$results = $db->query($sql)->bind(':firstname', 'Chris')->fetchAll();

```

### Query Results

On failure, call **->getError** to display the SQL error message

```php
if($results){
    foreach($results as $result){
        echo $result->{$column};
    }
} else {
    echo $db->getError();
}
```


<hr>

## Insert Records

#### Insert a record using 'bind' params

```php
$table   = 'names';
$columns = array('firstname' => 'Fred', 'surname' => 'Bloggs');

$result = $db->insert($table,$columns);
```

### Insert Results

```php
if($result){
    echo $db->rowCount() . ' records affected';
} else {
    echo $db->getError();
}
```

### Last Insert ID

```php

$id = $db->insertID();

```


<hr>

## Update Records

#### Update (all) records using 'bind' params

```php
$table   = 'names';
$columns = array('firstname' => 'Fred', 'surname' => 'Bloggs');

$result = $db->update($table,$columns);
```

#### Update records using 'bind' params and 'where' string

````php
$table   = 'names';
$columns = array('firstname' => 'Fred 2', 'surname' => 'Bloggs 2');
$where   = "firstname = 'Fred' AND surname = 'Bloggs'";  //'WHERE' is not needed, or spaces

$result = $db->update($table,$columns,$where);
```


#### Update specific records using 'bind' params and 'where'
```php
$table   = 'names';
$columns = array('firstname' => 'Fred 2', 'surname' => 'Bloggs 2');
$where   = array('firstname' => 'Fred',   'surname' => 'Bloggs');

$result = $db->update($table,$columns,$where);
```

### Update Results
````php
if($result){
    echo $db->rowCount() . ' records affected';
} else {
    echo $db->getError();
}
```


<hr>

## Delete Records

#### Delete records using a 'where' string

```php
$table  = 'names';
$where  = "surname = 'Doe'";

$result = $db->delete($table,$where);
```

#### Delete records using a 'where' array

```php
$table = 'names';
$where = array('surname' => 'Doe');

$result = $db->delete($table,$where);
```

### Delete Results
```php
if($result){
    echo $db->rowCount() . ' records affected';
} else {
    echo $db->getError();
}
```














