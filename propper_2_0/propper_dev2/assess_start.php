<?php
    session_start();

    $hak = $_SESSION['aCCeSSRiGHT'];
    $employeeid = $_SESSION["uSeRiD"];
    
    if (isset($_REQUEST["btnSubmit"])){
        $_SESSION["assessStatus"] = "start";
        $_SESSION["NoTe"] = "";
        $_SESSION["aSSeSSTYPe"] = $_SESSION["aSSeSSiD"];

		try{
            require_once ("class/DBgateway.php");
    		require_once ("class/ConfigReader.php");
    		
    		$conf = new ConfigReader();

			$gate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
        	$gate->OpenConnection();
        	
        	$nbdonesql =   "SELECT 
								nb_done 
							FROM 
								pnp.dev_cc_score 
							WHERE 
								employee_id = '".$_SESSION["uSeRiD"]."' and 
								period = ".date('Ym')." and 
								assess_id = '".$_SESSION["aSSeSSiD"]."'";

            $infores = $gate->ExecuteQuery($nbdonesql,"pnp");
            $arrOfInfo = mysql_fetch_array($infores);

            if ($arrOfInfo[0] == ""){
                $nbquest = 1;
            }
            else{
                $nbquest = $arrOfInfo[0]+1;
            }

            $transdate = date("d/m/Y");
            $transid = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$transdate.$nbquest),0,10));
            $_SESSION["transdate"] = $transdate;

            echo "<script language=\"javascript\">location.href=\"assessment.php?id=$transid&qnum=$nbquest\"</script>";
            echo "<script language=\"javascript\">location.href=\"main.php\"</script>";
        }
        catch(Exception $e){
            //echo "<script language=\"javascript\">location.href=\"main.php?err=461003\"</script>";
        }
    }
    else{
        echo "<script language=\"javascript\">location.href=\"main.php?err=461002\"</script>";
    }
?>