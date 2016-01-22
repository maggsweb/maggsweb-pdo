<?php 

class MyPDO {
 
    private $host   = DB_HOST;
    private $user   = DB_USER;
    private $pass   = DB_PASS;
    private $dbname = DB_NAME;
    
    private $dbh;
    private $error;
    private $stmt;
    
    public function __construct()
    {
        
        $dsn = "mysql:host=$this->host;dbname=$this->dbname";
        
        $options = array(
            // This option sets the connection type to the database to be persistent. 
            // Persistent database connections can increase performance by checking to see 
            // if there is already an established connection to the database.
            PDO::ATTR_PERSISTENT => true, 
            // Using ERRMODE_EXCEPTION will throw an exception if an error occurs.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        $this->error = false;
        
    }
    
    
    // Set Query
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }
    
    // Bind any db values
    // This step is optional if the query does not need any values, 
    // or if all the values are in the query (legacy)
    public function bind($param, $value, $type=NULL)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }
    
    // Run query
    // This is called automatically when fetching results
    public function execute()
    {
        try {
            $this->stmt->execute();
        }   
        catch (PDOException $e) {
            $this->error = $e->getMessage();
        }  
        return $this->stmt->errorCode() === '00000';
    }
    
    // Multiple Rows
    public function fetchAll($type='Object')
    {
        $this->execute();
        if($type == 'Array'){
            // Return an Array of Arrays
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Return an array of Objects
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        }
    }
    
    // Single Row
    public function fetchRow($type='Object')
    {
        $this->execute();
        if($type == 'Array'){
            // Return an Array
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Return an Object
            return $this->stmt->fetchObject();
        }
    }
    
    // Single Value
    public function fetchOne() 
    {
        $this->execute();
        $resultArray = $this->stmt->fetch(PDO::FETCH_ASSOC);
        return array_shift($resultArray);
    }
    
    /**
     * 
     * @param type $table
     * @param type $columns
     * @return boolean
     */
    public function insert($table, $columns=false)
    {
        if(is_array($columns)){
            // Build COLUMNS
            $columns = array();
            $binders = array();
            foreach ($columns as $key => $value){
                $columns[] = $key;
                $binders[] = ':'.$key;
            }
            $columnsArrayString = implode(', ',$columns);
            $bindersArrayString = implode(', ',$binders);
            // Build QUERY
            $query = "INSERT INTO $table ($columnsArrayString) VALUES ($bindersArrayString);";
            // Prepare Query
            $this->stmt = $this->dbh->prepare($query);
            // Bind Column Params
            foreach ($columns as $key => $value){
                $this->bind(":$key", $value);
            }
            // Run!
            return $this->execute();
        }
        $this->error = 'PDO Insert error: Column Array missing';
        return false;
    }
    
    /**
     * 
     * @param type $table
     * @param type $columns
     * @param type $where       This can be passed as a String, or asd an Array
     *                          The Array type is only for use when all WHERE operators are '='
     * @param type $limit
     * @return boolean
     */
    public function update($table, $columns=false, $where=false, $limit=false)
    {
        if(is_array($columns)){
            // Build COLUMNS
            $binders = array();
            foreach ($columns as $key => $value){
                $binders[] = "$key = :column_$key";
            }
            $bindersString = implode(', ',$binders);
            // Build the QUERY
            $query = "UPDATE $table SET $bindersString";
            // Build the WHERE
            if(is_array($where)){
                // If $where is an array, all WHERE operators will be '='
                $clauses = array();
                foreach ($where as $key => $value){
                    $clauses[] = "$key = :where_$key";
                }
                $query .= " WHERE ".implode(' AND ',$clauses);
            } else {
                // $where is treated as a string
                // replace all case versions of 'where'
                $query .= " WHERE ".preg_replace("/where/i", "", $where);
            }
            if($limit){
                $limitInt = (int)$limit;
                $query .= " LIMIT $limitInt";
            }
            // Prepare Query
            $this->stmt = $this->dbh->prepare($query);
            // Bind Column Params
            foreach ($columns as $key => $value){
                $this->bind(":column_$key", $value);
            }
            // Bind Where Params if $where is_array
            if(is_array($where)){
                foreach($where as $key => $value){
                    $this->bind(":where_$key", $value);
                }
            }
            // Run!
            return $this->execute();
        }
        $this->error = 'PDO Update error: Column Array missing';
        return false;
    }
    
    
    /**
     * 
     * @param type $table
     * @param type $whereString
     * @param type $whereParams
     * @param type $limit
     * @return type
     */
    public function delete($table, $whereString=false, $whereParams=false, $limit=false)
    {
        // Build the Query
        $query = "DELETE FROM $table";
        if($whereString){
            $query .= " WHERE $whereString";
        }
        if($limit){
            $query .= " LIMIT $limit";
        }
        // Prepare Query
        $this->stmt = $this->dbh->prepare($query);
        // Bind Where Params
        if($whereParams){
            foreach($whereParams as $key => $value){
                $this->bind(":$key", $value);
            }
        }
        // Run!
        return $this->execute();
    }
    
    
    // Num-affected-rows for INSERT/UPDATE/DELETE
    // Not supposed to work for SELECT..  ..but does
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
    
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }
    
    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }
    
    public function getError()
    {
        return $this->error;
    }
    
    public function getQuery()
    {
        return $this->stmt->queryString;
    }
    

    
    

    
    
    //return preg_replace_callback('/:([0-9a-z_]+)/i', array($this, '_debugReplace'), $q);
    
    
}
