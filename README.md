# MyPDO
A 'Work in progress' Database wrapper for PHP using MySQL and PDO
<hr>

### Table of Contents
**[Initialization](#initialization)**  
**[Raw Query](#raw-query)**  
**[SQL Select](#sql-select)**  





### Initialization
To use this class, download and include 'MyPDO.php' into your project.

```php
require_once ('MyPDO.php');
```







### Raw Query
To execute a raw string of SQL, pass the complete SQL string to *->query*, call *->execute* (returns true|false)

```php
$sql = "CREATE TABLE `names` (
    `id`          int(5)      NOT NULL AUTO_INCREMENT,
    `firstname`   varchar(50) DEFAULT NULL,
    `surname`     varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`)
);";

$sql = "INSERT INTO `names` VALUES 
    (NULL, 'Chris',  'Maggs'),
    (NULL, 'Chris2', 'Maggs2'),
    (NULL, 'Chris3', 'Maggs3');";

$db->query($sql);
```

On success, call *->rowCount* to return the number of rows updated (if applicable)
On failure, call *->getError* to display the SQL error message

```php
if($db->execute() ){
    echo $db->rowCount() . ' records inserted';
} else {
    echo $db->getError();
}
```








### SQL Select
asd fa sdf as df asdf a sdf asd ff 
asd fa sdf as df asdf a sdf asd ff 
asd fa sdf as df asdf a sdf asd ff 
asd fa sdf as df asdf a sdf asd ff 
asd fa sdf as df asdf a sdf asd ff 














