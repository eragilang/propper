<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script><script src="style/ga.js" type="text/javascript"></script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-181114-3");
pageTracker._trackPageview();
</script>

<?php
	
	include('config.php');
    require_once ("class/DBgateway.php");
	
    require_once ("class/ConfigReader.php");
    $conf = new ConfigReader();
	$username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
	$username = mysql_escape_string($username);
	$password = mysql_escape_string($password);	
    $sessGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
    $sessGate->OpenConnection(); 
    $csdmGate = new DBGateway($conf->Host251,$conf->User251, $conf->Pass251);
    $csdmGate->OpenConnection();

        $check = "SELECT unit_type from csdm2.t_appprofile where employee_code = '$username'";
		//echo "flag";
         //echo $username;        
        $checkres = $sessGate->ExecuteQuery($check,"pnp");
		if($username == 'upload_soal'){
			header("location: http://10.251.38.144:9090/propper_2_0/login.php?username=$username&password=$password");
		}
		else if (mysql_num_rows($checkres) > 0 ){
			$rs = mysql_fetch_array($checkres);
			$unitT = $rs[0];
			if ($unitT == 'webservice'){
				header("location: http://172.28.145.39:9090/propper_2_0/login.php?username=$username&password=$password");
			}else if($unitT == 'callcenter'){
				header("location: http://10.251.38.144:9090/propper/login.php?username=$username&password=$password");	
			} 
			else{
				header("location: http://10.251.38.144:9090/propper_2_0/login.php?username=$username&password=$password");
			}
		}
		else{
			//echo "User ". $username ."is not found.";
			header("location: http://10.251.38.144:9090/propper_2_0/login.php?username=$username&password=$password");
		}

?>