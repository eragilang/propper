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
	
	$set_id			  = $_SESSION['unitSetIdSoal'];
	$regional 	  = $_SESSION['unitRegSoal']; //$_POST["mnRegional"];
	$employee_id  = $_SESSION["uSeRiD"];
	
	$question 	  = $_POST["aquestion"];
	$optiona 		  = $_POST["aoptiona"];
	$optionb 		  = $_POST["aoptionb"];
	$optionc 		  = $_POST["aoptionc"];
	$optiond 		  = $_POST["aoptiond"];
	$wrong 	      = $_POST["awrong"];
	$correct 		  = $_POST["acorrect"];
	$keterangan 	= $_POST["aketerangan"];
	$jml_csr 	    = $_POST["ajmlcsr"];	
	$question_id  = $_GET["id"];
		
	$slc_lvl = "SELECT LEVEL, periode FROM question_active WHERE question_id = '".$question_id."';";	
	$res_slc_lvl = mysql_query($slc_lvl);
	$row_slc_lvl = mysql_fetch_array($res_slc_lvl);
	
	$job_function = $row_slc_lvl[0];
	$periode = $row_slc_lvl[1];
	
  // Insert question adjusment
  $ins_qs_adj = "INSERT INTO pnp.question_adjustment
									VALUES ('$question_id', '$question', '$optiona', '$optionb', '$optionc', '$optiond', 
									'$wrong', '$correct', NOW(), $jml_csr, '$employee_id', '$set_id', '$job_function', '$regional', '$periode', '$keterangan');";
  
  if($set_id == 1) {
		$point = 4;
	} else if ($set_id == 2) {
		$point = 2;
	}
  
  // Update Score  
  echo $upd_scr_adj = "UPDATE score_archive SET nb_correct = nb_correct + 1, nb_wrong = nb_wrong - 1, score = nb_correct * $point 
  								WHERE employee_id IN 
  									(SELECT employee_id FROM score_detail_archive WHERE question_id IN ('".$question_id."') 
  									AND is_correct = 0 AND end_assess IS NOT NULL)
  								AND period = '".$periode."';";
  
  // Update Score detail
  echo $upd_scr_dtl_adj = "UPDATE score_detail_archive SET is_correct = 1, employee_answer = solution WHERE 
										question_id IN ('".$question_id."') AND is_correct = 0 AND period = '".$periode."' AND end_assess IS NOT NULL;";
  
  
  $res_upd_scr_adj = mysql_query($upd_scr_adj);
  
  if($res_upd_scr_adj) {
		$res_upd_scr_dtl_adj = mysql_query($upd_scr_dtl_adj);
		
		if($res_upd_scr_dtl_adj) {
			$res_ins_qs_adj = mysql_query($ins_qs_adj);
			
			if($res_ins_qs_adj) {
				$pesan = "Prosess Adjustment soal berhasil";  
			} else {
				$pesan = "Error : Question Adjustment GAGAL";
			}
		} else {
			$pesan = "Error : Scr Detail GAGAL";
		}
	} else {
		$pesan = "Error : Scr GAGAL";		
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