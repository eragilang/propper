<?php
	session_start();
	
	if($_SESSION['uSeRiD'] == ""){
    	$homelink = "index.php";
    }
   	else{
    	$homelink = "main.php";
    }
    
    $hak = $_SESSION['aCCeSSRiGHT'];
    $employeeid = $_SESSION["uSeRiD"];
    
    $trueid = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$_SESSION["transdate"]),0,10));
    $getid = $_REQUEST["id"];
    $transdate = date("d/m/Y");
    $_SESSION["transdate"] = $transdate;

    /*Validate transaction id*/
    if ($trueid != $getid){
        echo "<script language=\"javascript\">location.href=\"main.php\"</script>";
        return;
    }
    
    require_once ("class/User.php");
   	require_once ("class/DBgateway.php");
    require_once ("class/ConfigReader.php");
    require_once ("class/Logger.php");
    
    $conf = new ConfigReader();
    
    try{
    	
    	$dbGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
	$dbGate->OpenConnection();
	    
	$query = "SELECT * 
		FROM 
			pnp.page_access_right
		WHERE
		 	file_name = 'assessment.php' AND
			access_right = '$hak' AND
			enable = '1' ";
			 		
	$accessres = $dbGate->ExecuteQuery($query,"pnp"); 
	    
	if (mysql_num_rows($accessres) <= 0){
		session_destroy();
	        #echo "<script language=\"javascript\">location.href=\"index.php?err=75\"</script>";
	}


?>
<?php
	}
    catch (Exception $e){
    	echo "<p style='font-size:15px;font-family:Garamond;color:red;padding:5px;border:1px solid red;'>".$e->getMessage()."</p>";	
    }
?>
