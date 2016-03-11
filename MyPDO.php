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
    
    
    /**
     * @desc Prepare the raw SQL
     * @param type $query
     */
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
        return $this; //allows chaining
    }
    
    
    /**
     * @desc Bind a specific column/value
     * @param type $param
     * @param type $value
     * @param type $type
     */
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
        return $this; //allows chaining
    }
    
    
    /**
     * @desc Run Query!  This is called automatically when fetching results
     * @return type
     */
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
    
    
    /**
     * @desc Fetch multiple rows as ObjectArray or MultiDemensional Array
     * @param type $type
     * @return type
     */
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
    
    
    /**
     * @desc Fetch a single row as an Object or an Array
     * @param type $type
     * @return type
     */
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
    
    
    /**
     * Select Multiple Rows
     * 
     * Build 'select' statement
     * Bind 'where' params (if sent via an Array)
     * Execute Query and return multiple rows
     * 
     * @param type $table
     * @param type $columns
     * @param type $where
     * @param type $extra
     * @return type
     */
    public function selectAll($table, $columns=false, $where=false, $extra=false)
    {
        // Prepare query
        $this->buildSelectStatement($table, $columns, $where, $extra);
        
        // Bind Where Params
        if($where) $this->bindWhereParameters($where);
        
        return $this->fetchAll();
    }
    
    
    /**
     * Select Single Row
     * 
     * Build 'select' statement
     * Bind 'where' params (if sent via an Array)
     * Execute Query and return single row
     * 
     * @param type $table
     * @param type $columns
     * @param type $where
     * @param type $extra
     * @return type
     */
    public function selectRow($table, $columns=false, $where=false, $extra=false)
    {
        // Prepare query
        $this->buildSelectStatement($table, $columns, $where, $extra);
        
        // Bind Where Params
        if($where) $this->bindWhereParameters($where);
        
        return $this->fetchRow();
    }

    
    /**
     * 
     * Select Single value
     * 
     * Check that $column has a single value (column name)
     * Build 'select' statement
     * Bind 'where' params (if sent via an Array)
     * Execute Query and return single row
     * Return the required column value
     * 
     * @param type $table
     * @param type $column
     * @param type $where
     * @param type $extra
     * @return type
     */
    public function selectOne($table, $column=false, $where=false, $extra=false)
    {
        // Check only 1 column name
        if(!is_array($column) || count($column) != 1){
            $this->error = 'Error `selectOne` can only pass a single column name';
        }
        
        // Prepare query
        $this->buildSelectStatement($table, $column, $where, $extra);
        
        // Bind Where Params
        if($where) $this->bindWhereParameters($where);
        
        $returnObject = $this->fetchRow();
        
        return $returnObject->$column;
        
    }
    
    
    /**
     * Insert Query
     * 
     * Build 'column string' from $column Array
     * Bind  'column values' from $column Array
     * Build query string
     * Bind column values
     * Execute query
     *  
     * @param type $table
     * @param type $columns
     * @return boolean
     */
    public function insert($table, $columns)
    {
            
        $columnString = $this->buildColumnString($columns);
        $binderString = $this->buildBindString($columns);

        // Build QUERY
        $query = "INSERT INTO $table ($columnString) VALUES ($binderString);";

        // Prepare Query
        $this->stmt = $this->dbh->prepare($query);

        // Bind Column Params
        foreach ($columns as $key => $value){
            $this->bind(":column_$key", $value);
        }
        
        return $this->execute();
    }
    
    /**
     * Update Query
     * 
     * Bind column values
     * Build 'where' clause from String or Array
     * Add aditional SQL
     * Build query string
     * Bind column values
     * Bind 'where' parameters
     * Execute query
     * 
     * @param type $table
     * @param type $columns
     * @param type $where       This can be passed as a String, or asd an Array
     *                          The Array type is only for use when all WHERE operators are '='
     * @param type $limit
     * @return boolean
     */
    public function update($table, $columns, $where=false, $limit=false)
    {
            
        $query = "UPDATE $table SET ";

        // Build column bindings
        $query .= $this->buildColumnBindString($columns);

            // Build the WHERE
        $query .= $this->buildWhereString($where);

        // Build LIMIT
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

        // Bind Where Params
        $this->bindWhereParameters($where);

        return $this->execute();
    }
    
    
    /**
     * Delete Query
     * 
     * Build 'where' clause from String or Array
     * Add aditional SQL
     * Build query string
     * Bind 'where' parameters
     * Execute query
     * 
     * @param type $table
     * @param type $where
     * @param type $limit
     * @return type
     */
    public function delete($table, $where=false, $limit=false)
    {
        // Build the Query
        $query = "DELETE FROM $table";
       
        // Build the WHERE
        $query .= $this->buildWhereString($where);
        
        // Build LIMIT
        if($limit){
            $limitInt = (int)$limit;
            $query .= " LIMIT $limitInt";
        }
        
        // Prepare Query
        $this->stmt = $this->dbh->prepare($query);
        
        // Bind Where Params
        $this->bindWhereParameters($where);
        
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
    

    //  ----------------------------------------------------------------
    //  PRIVATE HELPER FUNCTIONS  --------------------------------------
    //  ----------------------------------------------------------------
    
    
    /**
     * 
     * @param type $table
     * @param string $columns
     * @param type $where
     * @param type $extra
     */
    private function buildSelectStatement($table, $columns, $where, $extra)
    {
        // Generate default column
        if(!$columns) $columns = ' * ';
        // Build QUERY
        $query = "SELECT $columns FROM $table ";
        // Build the WHERE
        if($where) $query .= $this->buildWhereString($where);
        // order by | direction | limit
        if($extra) $query .= " $extra ";
        // Prepare query
        $this->stmt = $this->dbh->prepare($query);
    }

    
    /**
     * 
     * @param type $where
     * @return type
     */
    private function buildWhereString($where)
    {
        $whereString = '';
        if($where){
            $whereString .= " WHERE ";
            if(is_array($where)){
                // If $where is an array, all WHERE operators will be '='
                $clauses = array();
                foreach ($where as $key => $value){
                    $clauses[] = "$key = :where_$key";
                }
                $whereString .= implode(' AND ',$clauses);
            } else {
                // $where is treated as a string
                // replace all case versions of 'where'
                $whereString .= preg_replace("/where/i", "", $where);
            }
        }
        return $whereString;
    }
    

    /**
     * @param type $where
     */
    private function bindWhereParameters($where)
    {
        if($where){
            if(is_array($where)){
                foreach($where as $key => $value){
                    $this->bind(":where_$key", $value);
                }
            }
        }
    }
    
    
    /**
     * @param type $columns
     * @return type
     */
    private function buildColumnBindString($columns)
    {
        if( $columns && is_array($columns)) {
            $binders = array();
            foreach ($columns as $key => $value){
                $binders[] = "$key = :column_$key";
            }
            return (string)implode(', ',$binders);
        }
    }
    
    
    /**
     * @param type $columns
     * @return type
     */
    private function buildColumnString($columns)
    {
        $tmp = array();
        foreach ($columns as $key => $value){
           $tmp[] = $key;
        }
        return (string)implode(', ',$tmp);
    }
    
    /**
     * @param type $columns
     * @return type
     */
    private function buildBindString($columns)
    {
        $tmp = array();
        foreach ($columns as $key => $value){
            $tmp[] = ':column_'.$key;
        }
        return (string)implode(', ',$tmp);
    }
    
    
    //return preg_replace_callback('/:([0-9a-z_]+)/i', array($this, '_debugReplace'), $q);
    
    
}
