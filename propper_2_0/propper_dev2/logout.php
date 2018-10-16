<?php

  	session_start();

   	require_once ("class/DBgateway.php");
   	require_once ("class/ConfigReader.php");

  	$delsession = "DELETE 
				FROM 
					  pnp.session 
		  		WHERE 
				  	userid = '".$_SESSION["uSeRiD"]."'";
				  	
  	$conf = new ConfigReader();

	try{
    	$sessGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
        $sessGate->OpenConnection();

    	$sessGate->ExecuteTransaction($delsession,"pnp");

    	$sessGate->CloseConnection();
  	}
  	catch(Exception $e){
    	echo "<script type=\"text/javascript\">location.href=\"index.php?err=500\"</script>";
  	}

  	session_destroy();

  	$cause = $_REQUEST["cause"];

	if ($cause != ""){
		echo "<script type=\"text/javascript\">location.href=\"index.php?err=5$cause\"</script>";
	}
	else{
		echo "<script type=\"text/javascript\">location.href=\"index.php\"</script>";
	}
?>