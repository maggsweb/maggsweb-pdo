[![StyleCI](https://github.styleci.io/repos/50177395/shield?branch=master)](https://github.styleci.io/repos/50177395)

# Maggsweb PDO

An easy-to-use PDO Database wrapper for PHP & MySQL

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
define('DBHOST', '');  //eg: 127.0.0.1
define('DBUSER', '');  //eg: root
define('DBNAME', '');  //eg: admin
define('DBPASS', '');  //eg: password

$db = new Maggsweb\MyPDO((DBHOST, DBUSER, DBNAME, DBPASS);
```

<hr>

## Query

#### Run a query, any query..

To execute a string of SQL, pass the complete SQL string to **->query**

```php
$sql = "INSERT INTO `names` VALUES 
            (NULL, 'Joe',  'Bloggs'),
            (NULL, 'John', 'Bloggs'),
            (NULL, 'Jane', 'Bloggs');";

$db->query($sql);

$result = $db->execute();
```
Call **->execute** to execute the query.  This returns true|false;



#### Run a query and return the results

To return the results from **->query** for use call **->fetchAll()** for multiple rows, **->fetchRow** for a single row or **->fetchOne** for a single value. Results are returned as an Array of Objects an Object or a value.  Optionally, passing 'Array' to the fetch functions will return results as a Multi-dimensional Array.

```php
$sql = "SELECT * FROM `names`";
$db->query($sql);

$results = $db->fetchAll();           // Multiple rows, returned and an Object Array
$results = $db->fetchAll('Array');    // Multiple rows, returned as a multi-dimensional array

$sql = "SELECT * FROM `names` LIMIT 1";
$db->query($sql);

$result  = $db->fetchRow();           // Single row, returned as an Object
$result  = $db->fetchRow('Array');    // Single row, returned as an Array

$sql = "SELECT name FROM `names` LIMIT 1";
$db->query($sql);

$result  = $db->fetchOne();           // Single value, returned as a String
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

// or

$results = $db->query("SELECT * FROM `names` WHERE firstname = :firstname")
              ->bind(':firstname', 'Chris')
              ->fetchAll();

```

### Query Results

On failure, call **->getError** to display the SQL error message

```php
if ($results) {
    foreach ($results as $result) {
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
$columns = ['firstname' => 'Fred', 'surname' => 'Bloggs'];

$result = $db->insert($table, $columns);
```

### Insert Results

```php
if ($result) {
    echo 'Record inserted';
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
$columns = ['firstname' => 'Fred', 'surname' => 'Bloggs'];

$result = $db->update($table, $columns);
```

#### Update records using 'bind' params and 'where' string

```php
$table   = 'names';
$columns = ['firstname' => 'Fred 2', 'surname' => 'Bloggs 2';
$where   = "firstname = 'Fred' AND surname = 'Bloggs'";  //'WHERE' is not needed

$result = $db->update($table, $columns, $where);
```


#### Update specific records using 'bind' params and 'where'
```php
$table   = 'names';
$columns = array('firstname' => 'Fred 2', 'surname' => 'Bloggs 2');
$where   = array('firstname' => 'Fred',   'surname' => 'Bloggs');

$result = $db->update($table,$columns,$where);
```

### Update Results
```php
if ($result) {
    echo $db->numRows() . ' records affected';
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

$result = $db->delete($table, $where);
```

#### Delete records using a 'where' array

```php
$table = 'names';
$where = ['surname' => 'Doe'];

$result = $db->delete($table, $where);
```

### Delete Results
```php
if ($result) {
    echo $db->numRows() . ' records affected';
} else {
    echo $db->getError();
}
```
