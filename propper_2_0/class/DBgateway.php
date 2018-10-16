<?php 

class DBGateway{
	private $_conn;
	private $_dbHost;
  	private $_dbUser;
  	private $_dbPassword;
  	private $_dbType = "mysql"; 

	public function __construct($host, $user, $pwd){
    	$this->_dbHost = $host;
    	$this->_dbUser = $user;
    	$this->_dbPassword = $pwd;
	}

	public function OpenConnection(){
		if ((!isset($this->_dbHost)) ||  (!isset($this->_dbUser)) || (!isset($this->_dbPassword))){
			throw new Exception("connection string is undefined!");
		}
		else{
      		if (!is_resource($this->_conn)){
        		$this->_conn = mysql_connect($this->_dbHost, $this->_dbUser, $this->_dbPassword);
        		if (!$this->_conn) 
        			throw new Exception ("unable to connect to database");
      		}
		}
	}

  	public function GetConnection(){
    	return $this->_conn;
  	}

  	public function CloseConnection(){
    	if (is_resource($this->_conn)){
      		mysql_close($this->_conn);
      		unset($this->_conn);
    	}
  	}

  	public function ExecuteQuery($qry, $dbused){
    	if (is_resource($this->_conn)){
    		mysql_select_db($dbused);
      		$retval = mysql_query($qry, $this->_conn);
      		if (!$retval) 
      			throw new Exception ("failed to execute given query .");
      		return $retval;
    	}
  }

  	public function ExecuteTransaction($qry, $dbused){
    	if (is_resource($this->_conn)){
      	try{
        	$retval = 1;
        	mysql_select_db($dbused);
        	mysql_query("START TRANSACTION", $this->_conn);
        	mysql_query("BEGIN", $this->_conn);
        	if (is_array($qry)){
          		foreach($qry as $sinqry){
            		//echo $sinqry;
            		$result = mysql_query($sinqry, $this->_conn);
            		if (!$result){
              			$retval = 0;
              			break;
            		}
          		}
        	}
        	else{
          		$result = mysql_query($qry, $this->_conn);
          		if (!$result){
            		$retval = 0;
          		}
        	}
      
        	if ($retval == 0){
          		mysql_query("ROLLBACK", $this->_conn);
          		return false;
        	}
        	else{
          		mysql_query("COMMIT", $this->_conn);
          		return true;
        	}
      	}
      	catch (Exception $e){
        	//mysql_query("ROLLBACK", $this->_conn);
        	throw new Exception ("failed to execute transaction : ".$e->getMessage());
      	}
    }
    else{
        return false;
    }    
  }
}
?>
