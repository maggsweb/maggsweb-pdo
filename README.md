# MyPDO

A simple PHP database wrapper for MySQL using PDO

<hr>

### Table of Contents
**[Initialization](#initialization)**  
**[Raw Query](#raw-query)**  
**[SQL Select](#sql-select)**  
**[Insert Records](#insert-records)**  
**[Update Records](#update-records)**  
**[Delete Records](#delete-records)**  


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
## Raw Query

#### Run a query, any query..

To execute a raw string of SQL, pass the complete SQL string to **->query**

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


#### Run a query  using 'bound' params and return results

To bind parameters to a query, pass the column identifier and value to **->bind()**.  Repeat this for each bound parameter in order.

```php
$sql = "SELECT * FROM `names` WHERE firstname = :firstname";

$db->query($sql);

$db->bind(':firstname', 'Chris');

$results = $db->fetchAll(); 
```

### Query Results

On success, call **->rowCount** to return the number of rows updated (if applicable)

On failure, call **->getError** to display the SQL error message

```php
if($results||$result){
    echo $db->rowCount() . ' records affected';
    //foreach($results as $result){
    //    echo $result->{$column};
    //}
} else {
    echo $db->getError();
}
```



<hr>
##SQL Select

#### Select all columns and return multiple rows

```php
$table   = 'names';

$results = $db->select($table);
```

#### Select specific columns and return multiple rows

```php
$table   = 'names';
$columns = 'firstname, surname';

$results = $db->select($table,$columns);
```

#### Select specific columns and return multiple rows using a 'where' string

```php
$table   = 'names';
$columns = 'firstname';
$where   = "surname LIKE '%D'";

$results = $db->selectAll($table,$columns,$where);
```

#### Select all columns and return multiple rows using a 'where' array

```php
$table   = 'names';
$where   = array('surname' => 'Doe');

$results = $db->selectAll($table,false,$where);
```

#### Select one row using a 'where' string
```php
$table  = 'names';
$where  = array('firstname' => 'John', 'surname' => 'Doe');

$result = $db->selectRow($table,false,$where);
```

#### Select one row using a 'where' array
```php
$table   = 'names';
$columns = 'id';
$where   = array('firstname' => 'John', 'surname' => 'Doe');

$result  = $db->selectRow($table,$columns,$where);
```

#### Select one value using a 'where' array

```php
$table   = 'names';
$columns = 'id';
$where   = array('firstname' => 'John', 'surname' => 'Doe');

$result  = $db->selectOne($table,$columns,$where);
```

#### Select multiple rows and order the results

```php
$table = 'names';
$extra = 'ORDER BY surname ASC';

$result = $db->selectAll($table,false,false,$extra);
```

### Select Results
```php
if($results||$result){
    echo $db->rowCount() . ' records affected';
    //foreach($results as $result){
    //    echo $result->{$column};
    //}
} else {
    echo $db->getError();
}
```

<hr>
##Insert Records

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
$where = array('surname'] = 'Doe');

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




















