[![StyleCI](https://github.styleci.io/repos/50177395/shield?branch=master)](https://github.styleci.io/repos/50177395)

# Maggsweb PDO

An easy-to-use PDO Database wrapper for PHP & MySQL

<hr>

### Table of Contents
**[Initialization](#initialization)**  
**[Query](#query)**  
**[Run a script](#run-a-script-of-several-statements)**  
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

require_once ('MyPDO.php');

$db = new MyPDO(DBHOST, DBUSER, DBPASS, DBNAME);
```

The connection uses `SET NAMES utf8mb4` by default. To change this, or any other PDO option, pass an array of overrides as the fifth argument:

```php
$db = new MyPDO(DBHOST, DBUSER, DBPASS, DBNAME, [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES latin1',
]);
```

#### One shared connection

Each `new MyPDO(...)` opens its own connection. To use one connection for the whole request, call **MyPDO::initialise** once at startup, then **MyPDO::connection** wherever a handle is needed:

```php
MyPDO::initialise(DBHOST, DBUSER, DBPASS, DBNAME);  // once, e.g. in bootstrap.php

$db = MyPDO::connection();                             // anywhere after that
```

`MyPDO::connection()` opens nothing itself. It returns the instance `initialise()` built, and throws a `RuntimeException` if `initialise()` hasn't run. Calling `initialise()` again returns the existing instance. In tests, `MyPDO::setConnection($double)` replaces the instance and `MyPDO::setConnection(null)` clears it.

**One thing to know:** the prepared statement is kept on the instance, so a shared instance means a shared statement. Always chain `query()->bind()->fetch()` as one expression (or as adjacent lines). If you split them so other code can run a query in between, the first caller silently gets the wrong rows. If a query has to stay open across other work, give it its own `new MyPDO(...)`.


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

$results = $db->fetchAll();             // Multiple rows
//$result  = $db->fetchRow();           // Single row
//$result  = $db->fetchRow('Array');    // Single row, returned as an array
//$result  = $db->fetchAll('Array');    // Multiple rows, returned as a multi-dimensional array
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


#### Run a script of several statements

**->query** prepares its SQL, which only reliably runs the first statement of a multi-statement string. To run a whole file of statements (a migration or a seed file), pass it to **->runScript**. It runs every statement and returns true|false. If any statement fails, it returns false and **->getError** has the reason. Statements before the failing one will already have run. Nothing can be bound and no rows are returned. MySQL only.

```php
$sql = file_get_contents('migrations/1.2.0.sql');

if (!$db->runScript($sql)) {
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
    echo $db->numRows() . ' records affected';
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

```php
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
```php
if($result){
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
    echo $db->numRows() . ' records affected';
} else {
    echo $db->getError();
}
```




<hr>
More examples are provided in 'examples.php'









