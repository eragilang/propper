<?php
	session_start();
	
	if($_SESSION['uSeRiD'] == ""){
    		$homelink = "index.php";
    	} else{
    		$homelink = "main.php";
    	}
    	
    	try{
    		require_once ("class/User.php");
    		require_once ("class/DBgateway.php");
    		require_once ("class/ConfigReader.php");
    		
    		$conf = new ConfigReader();
    		
    		$dbGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
       	$dbGate->OpenConnection();
    		
				$csdmGate = new DBGateway($conf->Host251,$conf->User251, $conf->Pass251);
       	$csdmGate->OpenConnection();
				
				$hak = $_SESSION['aCCeSSRiGHT'];
	    	
	    	$query = "SELECT * 
				FROM 
				pnp.page_access_right
				WHERE
				file_name = 'main.php' AND
			 	access_right = '$hak' AND
			 	enable = '1' ";
					
			$accessres = $dbGate->ExecuteQuery($query,"pnp"); 
	    
	   	if (mysql_num_rows($accessres) <= 0){
	    		session_destroy();
	        	echo "<script language=\"javascript\">alert(\"Page Access Violation. Silahkan kembali ke halaman utama dan Login kembali\");self.close();</script>";
        		return;
			}
    	} catch (Exception $e){   	
    	}	
  	 		
?>



<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Upload Soal PNP</title>
<script language="javascript">
<!--

function CloseWindow() {

  window.close();

}
//-->
</script>

<style type="text/css">
<!--
.style3 {color: #FFFF00}
-->
</style>
</head>
<style type="text/css">
<!--
.style1 {
	font-size: 16px;
	font-weight: bold;
	color: #FFFFFF;
}
body {
	background-color: #333333;
}
.style2 {
	color: #FFFF00;
	font-weight: bold;
}
.style9 {
	color: #FFFFFF;
	font-weight: bold;
	font-family: "Bookman Old Style";
	font-size: 36px;
}
-->
</style>
<body>
<?php   

	include "connection.php";
	
	$set_id			= $_SESSION['unitSetIdSoal'];
	$regional 	= $_SESSION['unitRegSoal']; //$_POST["mnRegional"];
		
  $unit_type_soal = $_GET["uts"];
  $job_function 	= $_GET["lvl"];
	
	$start_assess = $_POST["start_assess"];
	$end_assess		= $_POST["end_assess"];
	
	/*
	if ($job_function == 3) {
		$unit_type_soal = "('grapari', 'gerai', 'plasa')"; 
		$end_assess = 30;
	} else {
		$unit_type_soal = "('grapari')";
	}
	*/
	
	$flagJml = 0;
	
	$unit_id = "(";
	
	// select unit_id dari t_unit_branch
	$slc_tu_branch = "SELECT unit_id FROM csdm2.t_unit_branch WHERE regional = '".$regional."' and unit_type IN ('".$unit_type_soal."');";
	$res_slc_tu_branch = $csdmGate->ExecuteQuery($slc_tu_branch,"csdm2");
	if ($res_slc_tu_branch) {
		$jml_unit_id = mysql_num_rows($res_slc_tu_branch);
		
		while ($row_slc_tu_branch = mysql_fetch_array($res_slc_tu_branch)) {
			$unit_id = $unit_id.$row_slc_tu_branch[0];
			$flagJml++;
			if ($flagJml != $jml_unit_id) {
				$unit_id = $unit_id.",";
			}
		}
		$unit_id = $unit_id.")";
	}
	
	
	if ($set_id == 1) {
		// Update assess_periode or men-set assess_periode
		$upd_ass_period = "UPDATE assess_period SET start_assess = ".$start_assess.", end_assess = ".$end_assess." WHERE unit_id IN ".$unit_id." AND LEVEL = '".$job_function."';";
	} else if ($set_id == 2) {
		$upd_ass_period = "UPDATE assess_period SET start_assess = ".$start_assess.", end_assess = ".$end_assess." WHERE unit_type = 'callcenter' AND LEVEL = '".$job_function."';";
	} else if ($set_id == 3) {
		$upd_ass_period = "UPDATE assess_period SET start_assess = ".$start_assess.", end_assess = ".$end_assess." WHERE unit_type = 'regional' AND LEVEL = '".$job_function."';";	
	}
	
	$res_upd_ass_period = mysql_query($upd_ass_period);
	
	if($res_upd_ass_period) {
		$pesan = "Update Assess Success";
	} else {
			$pesan = "Errorr : Update Assess";		
	}
	
	
	?>
	
	<table width="361" border="0" align="center" cellpadding="0" cellspacing="0">
  	<tr>
    	<td width="361" height="270" valign="middle">
    		<table width="337" border="0" align="center">
      		<tr>
        		<td width="331" height="33" valign="top" bgcolor="#FFFFFF">
        			<table width="320" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#810704">
          			<tr>
            			<td width="310"><div align="center" class="style1"><?php echo $pesan; ?></div></td>
          			</tr>
        			</table>          
        		</td>
      		</tr>
      		<tr>
        		<td height="34" align="center" valign="top" bgcolor="#FFFFFF"> 
          		<table width="320" border="0" cellpadding="0" cellspacing="0" bgcolor="#810704">
          			<tr>
            			<td width="213" height="24" align="right" valign="baseline">		
										<a href="javascript:CloseWindow()">
											<img src="image/close.gif" width="80" height="20" border="0">
										</a>
									</td>
          			</tr>
        			</table>		
						</td>
      		</tr>     
    		</table>
    	</td>
  	</tr>
</table>


</body>
</html>