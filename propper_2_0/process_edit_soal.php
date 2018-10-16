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
	
	$question 	= $_POST["equestion"];
	$optiona 		= $_POST["eoptiona"];
	$optionb 		= $_POST["eoptionb"];
	$optionc 		= $_POST["eoptionc"];
	$optiond 		= $_POST["eoptiond"];
	$solution 	= $_POST["esolution"];
	$waktu 			= $_POST["ewaktu"];
	$codesoal 	= $_POST["ecdsoal"];
	$mandatory 	= $_POST["emandatory"];
	
	$question_id = $_GET["id"];
	
	$slc_lvl = "SELECT LEVEL, periode FROM question_active WHERE question_id = '".$question_id."';";	
	$res_slc_lvl = mysql_query($slc_lvl);
	$row_slc_lvl = mysql_fetch_array($res_slc_lvl);
	
	$job_function = $row_slc_lvl[0];
	$periode = $row_slc_lvl[1];
	
	// Update Soal pada Question_active
	$upd_qs_active = "UPDATE question_active SET question = '".$question."', 
														option_a = '".$optiona."', 
														option_b = '".$optionb."', 
														option_c = '".$optionc."',
														option_d = '".$optiond."',
														solution = '".$solution."',
														difficulty = '".$waktu."',  
														cat_id = '".$codesoal."',
														is_mandatory = '".$mandatory."'
													WHERE question_id = '".$question_id."';";

	
	// Menset nb_question = 0 pada category_distribution
	$upd_cat_dis_0 = "UPDATE category_distribution SET nb_question = '0'"
						." WHERE set_id = '".$set_id."' AND"
						." level = '".$job_function."' AND"
						." regional = '".$regional."'";
	
	// check question distribution
	$slc_qs_active = "SELECT cat_group, count(*), sum(is_mandatory),"
						." group_description FROM question_active qa, category ca"
						." WHERE qa.cat_id = ca.cat_id AND qa.set_id = '".$set_id."'"
						." AND level = '".$job_function."' AND qa.periode = '".$periode."'"
						." AND qa.regional = '".$regional."' GROUP BY cat_group;";
	
	
	$res_upd_qs_active = mysql_query($upd_qs_active);
		
	if($res_upd_qs_active) {
		
		// Set nb_question = 0 pada category_distribution
		$res_upd_cat_dis_0 = mysql_query($upd_cat_dis_0);  //$dbGate->ExecuteQuery($upd_cat_dis_0,"pnp");  
		
		if($res_upd_cat_dis_0) {
			
			// check question distribution
			$res_slc_qs_active = mysql_query($slc_qs_active);  //$dbGate->ExecuteQuery($slc_qs_active,"pnp");   
			$jml_cat_soal = mysql_num_rows($res_slc_qs_active);
			
			$z = 0;									
			if ($jml_cat_soal > 0) {
								
				
				// max soal
				$slc_max_soal = "SELECT max_question FROM assess_param WHERE set_id = '".$set_id."' AND LEVEL = '".$job_function."';";
				$res_slc_max_soal = mysql_query($slc_max_soal);
				$row_slc_max_soal = mysql_fetch_array($res_slc_max_soal);
				$flag = $row_slc_max_soal[0];
										
				while ($row_slc_qs_active = mysql_fetch_array($res_slc_qs_active)) {											
					$cat_id[$z] = $row_slc_qs_active[0];
					$max_cat[$z] = $row_slc_qs_active[1];
					$min_cat[$z] = $row_slc_qs_active[2];
					$jml_cat = $jml_cat + $row_slc_qs_active[2];
											
					$z++;
				}
										
				//start proses
				while ($jml_cat < $flag) {
					$tmp_jml_cat = 0;
					for ($i = 0; $i < count($cat_id); $i++) {
						if ($tmp_jml_cat < $flag) {
							if (($min_cat[$i] + 1) <= $max_cat[$i]) {
								$min_cat[$i] = $min_cat[$i] + 1;
							}
						}
						$tmp_jml_cat = $tmp_jml_cat + $min_cat[$i];
					}
					$jml_cat = $tmp_jml_cat;
				}
				// end proses
										
				for ($i = 0; $i < count($cat_id); $i++) {
					// Set category_distribution
					$upd_cat_dis = "UPDATE category_distribution SET"
													." nb_question = '".$min_cat[$i]."' WHERE"
													." cat_group = '".$cat_id[$i]."' AND"
													." set_id = '".$set_id."' AND"
													." level = '".$job_function."' AND"
													." regional = '".$regional."';";
											
					$res_upd_cat_dis = mysql_query($upd_cat_dis);  //$dbGate->ExecuteQuery($upd_cat_dis,"pnp");  										
				}							
				
				$pesan = "Edit Question Success";
												
			} else {
				$pesan = "Error : Check Category Soal";
			}													
		} else {
			$pesan = "Error : Clear Category";
		}				
	} else {
		$pesan = "Error : Gagal Edit Question...!!!";
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