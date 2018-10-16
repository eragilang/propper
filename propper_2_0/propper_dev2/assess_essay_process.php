<?php

	session_start();

	require_once ("class/User.php");
   	require_once ("class/DBgateway.php");
	require_once ("class/ConfigReader.php");
    	require_once ("class/Logger.php");
    
    	$conf = new ConfigReader();

	try{
    	
    		$dbGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
		$dbGate->OpenConnection();

		for($i=1;$i<=5;$i++){
			$ans = str_replace("'","",$_POST["answer".$i]);
			$qry = "UPDATE pnp.essay_answer
			SET
				employee_answer = '$ans',
				answer_time = now()
			WHERE
				employee_id = '".$_POST["userid"]."' AND
				question_id = '".$_POST["questionid".$i]."'
			";

		$dbGate->ExecuteTransaction($qry,"pnp");		
		}

                $_SESSION["assessStatus"] = "done";
                $_SESSION["uSeRiD"] = $_POST["userid"];
                $_SESSION["aSSeSSTYPe"] = $_POST["txtassessmenttype"];

		echo "<script language=\"javascript\">location.href=\"result.php\"</script>";

	}
    	catch (Exception $e){
    		echo "<p style='font-size:15px;font-family:Garamond;color:red;padding:5px;border:1px solid red;'>".$e->getMessage()."</p>";	
    	}
?>
