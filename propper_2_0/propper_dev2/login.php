<?php

    session_start();
    require_once ("class/User.php");
    require_once ("class/Logger.php");
    require_once ("class/DBgateway.php");
    require_once ("class/ConfigReader.php");
    include('config.php');
    $conf = new ConfigReader();
    
    try{
        $username = $_REQUEST["username"];
        $password = $_REQUEST["password"];

        $sessGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
        $sessGate->OpenConnection();
        
        $csdmGate = new DBGateway($conf->Host251,$conf->User251, $conf->Pass251);
        $csdmGate->OpenConnection();

        $check =    "SELECT * ".
                    "FROM ".
                    "  pnp.session ".
                    "WHERE ".
                    "  userid = '$username' AND ".
                    "  session_date > date_sub(now(), interval 15 minute) AND ".
                    "  status = 1";
                    
        $checkres = $sessGate->ExecuteQuery($check,"pnp");
        //echo $flag;
        if (mysql_num_rows($checkres) > 0 ){
            $ipaddress = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else{
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            }

            $logger = new Logger();
            $logger->setDBConnection($sessGate);
            $logger->Logging(HistoryType::HISTORYTYPE_LOGIN,$username,"Unable to login. 
			User has been log in to PnP!",$ipaddress);
			
			
		 
			
            echo "<script type=\"text/javascript\">location.href=\"index.php?err=465\"</script>";
        }
        else{

            $user = new User($username);
            $user->setDBConnectionCSDM($csdmGate);
            $user->setDBConnectionPNP($sessGate);
            $ret = $user->AuthenticateMe($password,&$flag);

	    	echo "FLAG:".$flag;
			//break;

            if ($ret){
                $_SESSION['aCCeSSRiGHT'] = $user->GetUserAccessRight();
                $_SESSION['uSeRiD'] = $user->GetUserID();
                $_SESSION['uSeRNaMe'] = $user->GetUserName();
                $_SESSION['aReaiD'] = $user->GetAreaID();
                $_SESSION['ReGioNiD'] = $user->GetRegionalID();
                $_SESSION['uNiTiD'] = $user->GetUnitID();
                $_SESSION['CHaNNeLTYPe'] = $user->GetChannelType();
                $_SESSION['TiMeZoNe'] = $user->GetTimeZone();
		  		$_SESSION['JoBFuNCTioN'] = $user->GetJobFunction();
				$_SESSION['SeGMeNT'] = $user->GetSegment();
		  		$_SESSION['LeVeL'] = $user->GetLevel();
		  		$_SESSION['aSSeSSiD'] = $user->GetUserAssessID();

                $ipaddress = "";

                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
                else{
                    $ipaddress = $_SERVER['REMOTE_ADDR'];
                }

		  		$logger = new Logger();
                $logger->setDBConnection($sessGate);
				
				
		
			
                

		  		if ($ipaddress != "10.1.1.101"){
					if ($user->GetChannelType() == "GERAI"){
						$query = "SELECT ip_address FROM pnp.gerai_allowed_ip WHERE enable = '1' and unit_id = ".$user->GetUnitID();
						
						$ipres = $sessGate->executeQuery($query,"pnp");
						
						if (mysql_num_rows($ipres) <= 0){
							$logger->Logging(HistoryType::HISTORYTYPE_LOGIN,$username,"Login Gagal. User Gerai hanya bisa digunakan dari geraiHALO masing-masing.",$ipaddress);

							echo "<script type=\"text/javascript\">location.href=\"index.php?err=468\"</script>";
							return;
						}
					
					}
		  		}

                $logger->Logging(HistoryType::HISTORYTYPE_LOGIN,$username,"Successful Login",$ipaddress);
               	$sqlsession = "INSERT INTO session VALUES ('".session_id()."',now(),'".$user->GetUserID()."',1)";
				$sessGate->ExecuteTransaction($sqlsession,"pnp");
				
				

                echo "<script type=\"text/javascript\">location.href=\"main.php\"</script>";
            }
            else{
		        $ipaddress = "";

                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
                else{
                    $ipaddress = $_SERVER['REMOTE_ADDR'];
                }
                $logger = new Logger();
                $logger->setDBConnection($sessGate);
                
                if ($flag == "LOCK"){
                    $logger->Logging(HistoryType::HISTORYTYPE_LOGIN,$username,"Unable to login. Account has been locked. Request new password to 1166!",$ipaddress);

                    echo "<script type=\"text/javascript\">location.href=\"index.php?err=461\"</script>";
					continue;
				}
                else if ($flag == "DISABLED"){
                    $logger->Logging(HistoryType::HISTORYTYPE_LOGIN,$username,"Unable to login. Personal information in CSDM has been disabled!",$ipaddress);

                    echo "<script type=\"text/javascript\">location.href=\"index.php?err=463\"</script>";
                }
                else if ($flag == "TIME-OUT"){
                    $logger->Logging(HistoryType::HISTORYTYPE_LOGIN,$username,"Unable to login. Password has been expired!",$ipaddress);

                    echo "<script type=\"text/javascript\">location.href=\"index.php?err=464\"</script>";
                }
		  else if ($flag == "NO-RIGHT"){
                    $logger->Logging(HistoryType::HISTORYTYPE_LOGIN,$username,"Unable to login. User has no privilege to access application!",$ipaddress);

                    echo "<script type=\"text/javascript\">location.href=\"index.php?err=466\"</script>";
                }
		  else if ($flag == "NO-SEGMENT"){
                    $logger->Logging(HistoryType::HISTORYTYPE_LOGIN,$username,"Unable to login. User has no segment!",$ipaddress);

                    echo "<script type=\"text/javascript\">location.href=\"index.php?err=467\"</script>";
                }
                else{
                    $logger->Logging(HistoryType::HISTORYTYPE_LOGIN,$username,"Unable to login. Username and Password did not match (Username = $username; Password = $password)!",$ipaddress);
                    echo "<script type=\"text/javascript\">location.href=\"index.php?err=462\"</script>";
                }
            }
        }
    }
    catch(Exception $e){
        try{
            $ipaddress = gethostbyname($_SERVER['REMOTE_ADDR']);
            $logger = new Logger();
            $logger->setDBConnection($sessGate);
            $logger->Logging(HistoryType::HISTORYTYPE_LOGIN,$username,"Unable to login. System failure (".$e->getMessage().")!",$ipaddress);

            echo "<script type=\"text/javascript\">location.href=\"index.php?err=82\"</script>";
        }
        catch(Exception $e){
            echo "<script type=\"text/javascript\">location.href=\"index.php?err=83&err=".$e->getMessage()."\"</script>";
        }
    }
	
		
?>
