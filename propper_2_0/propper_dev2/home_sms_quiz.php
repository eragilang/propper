<?php
	session_start();
	
	if($_SESSION['uSeRiD'] == ""){
    		$homelink = "index.php";
    	} else{
    		$homelink = "main.php";
    	}
    
    	$getid = $_REQUEST["idrand"];
    	$trueid = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$_REQUEST["rnum"].$_SESSION["prevpage"]),0,10));
    
    	require_once ("class/User.php");
   	  require_once ("class/DBgateway.php");
    	require_once ("class/ConfigReader.php");
    	require_once ("class/Logger.php");
    	
    	$conf = new ConfigReader();
    
    	try{
    	
    		$dbGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
	    	$dbGate->OpenConnection();
	       
	      $gatesms = new DBGateway($conf->Host192,$conf->User192, $conf->Pass192);
        $gatesms->OpenConnection();
	       
	    	$hak = $_SESSION['aCCeSSRiGHT'];
	    	
    		if ($trueid != $getid){    	
	       	$ipaddress = "";
	        
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
	            		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	      		} else{
	            		$ipaddress = $_SERVER['REMOTE_ADDR'];
	       	}
	
	      		$logger = new Logger();
	      		$logger->setDBConnection($dbGate);
	      		$logger->Logging(HistoryType::HISTORYTYPE_PAGE_ACCESS_VIOLATION,$_SESSION["uSeRiD"],"Page Access Violation : (home_upload_soal.php) ($trueid) ($getid) (".$_SESSION["prevpage"].")",$ipaddress);
        		
        		echo "<script language=\"javascript\">alert(\"Page Access Violation. Silahkan kembali ke halaman utama dan Login kembali\");self.close();</script>";
        		return;	
		} else {
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
			echo "<script language=\"javascript\">location.href=\"index.php?err=75\"</script>";
		}
			    
    		$period = date('Ym');
			    
    		$sqlcheck = "SELECT 
		 		nb_done, 
				if(nb_done = nb_question, 1, 0) as flag, 
				score 
		 		FROM 
				pnp.score 
		 		WHERE 
				employee_id = '".$_SESSION["uSeRiD"]."' and 
				period = $period and 
				assess_id = '".$_SESSION["aSSeSSiD"]."'";
				
        	$checkres = $dbGate->ExecuteQuery($sqlcheck,"pnp");
        	$arrOfCheck = mysql_fetch_array($checkres);
        	
        	$arrOfCheck[2];
	  }
    }
    catch (Exception $e){  	
    }	  	 		
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SMS Quiz</title>
<script language="javascript">
<!--

function CloseWindow() {

  window.close();

}
//-->
</script>


<script type="text/javascript">
function notEmpty() {	
	<?php
	
	// Query Select Soal 
/*	$slc_soal = "SELECT
			SUBSTR(SUBSTR(c.description, 1, (LENGTH(a.employee_id) + LENGTH(a.quiz_id) + 1)), (LENGTH(a.employee_id) + 2)) AS quiz_id , 
			REPLACE(SUBSTR(c.description,(LENGTH(a.employee_id) + LENGTH(a.quiz_id) + 3)),'%23','#') AS soal
			FROM quiz_trans a, quiz_log c
			WHERE 
			a.employee_id = '".$_SESSION['empCodeSms']."' AND 
			c.employee_id = a.msisdn AND
			a.quiz_seq = (SELECT quiz_seq FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1) AND
			a.employee_answer IS NULL AND 
			SUBSTR(c.log_time,1,10) = a.send_date AND 
			SUBSTR(c.description, 1, (LENGTH(a.employee_id) + LENGTH(a.quiz_id) + 1)) = CONCAT(a.employee_id, '-', a.quiz_id) AND
			a.period = (SELECT period FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1);";
	
	$res_slc_soal_id_alert = $gatesms->ExecuteQuery($slc_soal ,"sms");
*/	?>
//	var i = 1;
	<?php
/*	if ($res_slc_soal_id_alert) {
		if (mysql_num_rows($res_slc_soal_id_alert) > 0 ) {		
			while ($row_slc_soal_id_alert = mysql_fetch_row($res_slc_soal_id_alert)) {
				
				$myTextField = TRIM($_POST['option_'.$row_slc_soal_id_alert[0]]);
				
				echo "alert('javascript from php ".TRIM($_POST['option_'.$row_slc_soal_id_alert[0]])."');"; 
		
		*/		
				?>
				
			//	alert("Mohon Entry Data untuk masing-masing Parameter !!!"+ i++);
				
				
				<?php
/*			}
		}
	}
*/	?>
	//	return true;
}
</script>

<link href="style/global.css" rel="stylesheet" type="text/css" media="all"/>
<link href="style/contact.css" rel="stylesheet" type="text/css" media="all"/>
<link href="style/assess.css" rel="stylesheet" type="text/css" media="all"/>

</head>

<body>

<?php

	$slc_soal = "SELECT
			SUBSTR(SUBSTR(c.description, 1, (LENGTH(a.employee_id) + LENGTH(a.quiz_id) + 1)), (LENGTH(a.employee_id) + 2)) AS quiz_id , 
			REPLACE(REPLACE(REPLACE(SUBSTR(c.description,(LENGTH(a.employee_id) + LENGTH(a.quiz_id) + 3)), '%23', '#'), '%3C', '('), '%3E', ')') AS soal
			FROM quiz_trans a, quiz_log c
			WHERE 
			a.employee_id = '".$_SESSION['empCodeSms']."' AND 
			c.employee_id = a.msisdn AND
			a.quiz_seq = (SELECT quiz_seq FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1) AND
			a.employee_answer IS NULL AND 
		#SUBSTR(c.log_time,1,10) = a.send_date AND 
			SUBSTR(c.description, 1, (LENGTH(a.employee_id) + LENGTH(a.quiz_id) + 1)) = CONCAT(a.employee_id, '-', a.quiz_id) AND
			a.period = (SELECT period FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1) GROUP BY 1;";


if($_POST['Submit']){ // Jawab Soal
	
	$res_slc_soal_id = $gatesms->ExecuteQuery($slc_soal ,"sms");
	
	if ($res_slc_soal_id) {
		if (mysql_num_rows($res_slc_soal_id) > 0 ) {
			while ($row_slc_soal_id = mysql_fetch_row($res_slc_soal_id)) {
				TRIM($_POST['option_'.$row_slc_soal_id[0]]);				
				//$jwb_soal = "SELECT * FROM quiz_trans WHERE employee_id = '".$_SESSION["uSeRiD"]."' AND quiz_id = '".$row_slc_soal_id[0]."';";				
				
				if (TRIM($_POST['option_'.$row_slc_soal_id[0]]) != "") {	// Jawab Soal
					$jwb_soal =	"UPDATE quiz_trans 
														SET 
														employee_answer = '".TRIM($_POST['option_'.$row_slc_soal_id[0]])."', 
														is_correct = IF(solution = employee_answer, 1, 0), 
														respond_time = NOW(), 
														state = 0, CLOSE = 1
														WHERE employee_id = '".$_SESSION['empCodeSms']."' AND quiz_id = '".$row_slc_soal_id[0]."';";
					
					$res_jwb_soal = $gatesms->ExecuteQuery($jwb_soal, "sms");
					
					$ins_log_sms = "INSERT INTO sms.quiz_log (log_time, employee_id, description) 
														VALUES (NOW(), '".$_SESSION['empCodeSms']."', 'Quiz Manual (".$row_slc_soal_id[0].") ".TRIM($_POST['option_'.$row_slc_soal_id[0]])."');";
					
					$res_ins_log_sms = $gatesms->ExecuteQuery($ins_log_sms, "sms");
					
					
				//	if ($res_jwb_soal) {
							//	$row_jwb_soal = mysql_fetch_row($res_jwb_soal);				
			//			echo "OK";						
			//		}
					
				}
			}
		}
	}
	
	$slc_score = "SELECT
				SUM(IF(employee_answer IS NOT NULL,1,0)) AS nbdone,
				SUM(IF(is_correct=1,1,0)) AS nb_correct, 
				SUM(IF(employee_answer IS NOT NULL,1,0)) - SUM(IF(is_correct=1,1,0))  AS nb_wrong,
				(SUM(IF(is_correct=1,1,0)) * 20) AS score
				FROM quiz_trans 
				WHERE  
				period = (SELECT period FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1) AND 
				quiz_seq = (SELECT quiz_seq FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1) AND 
				employee_id = '".$_SESSION['empCodeSms']."';";

	$res_slc_score = $gatesms->ExecuteQuery($slc_score, "sms");
	
	if ($res_slc_score) {	// Update Score
		$row_slc_score = mysql_fetch_row($res_slc_score); 
		
		$nb_done 		= $row_slc_score[0];
		$nb_correct = $row_slc_score[1];
		$nb_wrong 	= $row_slc_score[2];
		$score 			= $row_slc_score[3];
		
		$upd_score = "UPDATE quiz_score
										SET 
										nb_done = $nb_done,
										nb_correct = $nb_correct,
										nb_wrong = $nb_wrong,
										score = $score
										WHERE 
										period = (SELECT period FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1) AND 
										quiz_seq = (SELECT quiz_seq FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1) AND 
										employee_id = '".$_SESSION['empCodeSms']."';";
		
		
		$res_upd_score = $gatesms->ExecuteQuery($upd_score, "sms");
		
		if ($res_upd_score) {
			//	$row_jwb_soal = mysql_fetch_row($res_jwb_soal);				
			//echo "OK Nilai Anda ".$score;		
			$pesan = "Nilai Anda : ".$score;	
			?>
			
			
			<table width="361" border="0" align="center" cellpadding="0" cellspacing="0">
  		<tr>
   	 		<td width="361" height="270" valign="middle">
   	 			<table width="337" border="0" align="center">
      		<tr>
        		<td width="331" height="33" valign="top" bgcolor="#FFFFFF">
        			<table width="320" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#810704">
          		<thead>
          		<tr>
            		<th width="310"><div align="center" class="style1"><?php echo $pesan; ?></div></th>
          		</tr>
          	</thead>
        			</table>          
        		</td>
      		</tr>
      		<tr>
        		<td height="34" align="center" valign="top" bgcolor="#FFFFFF"> 
          		<table width="320" border="0" cellpadding="0" cellspacing="0" bgcolor="#810704">
          		<thead>
          		<tr>
            		<th width="213" height="24" align="right" valign="baseline">
								
									<a href="javascript:CloseWindow()">
										<img src="image/close.gif" width="80" height="20" border="0">
									</a>
									
								</th>
          		</tr>
          	</thead>
        			</table>		
						</td>
      		</tr>      
    			</table>
    		</td>
  		</tr>
			</table>
			
			<?php
					
		}
		
	}
	
	
} else { // Request Soal
	
	$res_slc_soal = $gatesms->ExecuteQuery($slc_soal ,"sms");
	
	if ($res_slc_soal) {
		if (mysql_num_rows($res_slc_soal) > 0 ) {
			?>
			<form enctype="multipart/form-data"  action="<?php $_PHP['SELF']; ?>" method="post">
				<table border="0" width='800' align="center" cellpadding="7" cellspacing="0" id="assessmentpage">
			<?php
			while ($row_slc_soal = mysql_fetch_row($res_slc_soal)) {
				list($soal, $opt_a_all, $opt_b_all, $opt_c_all, $opt_d_all) = split("%0D", $row_slc_soal[1]);
				
				list($pil_a, $opt_a) = split("A)", $opt_a_all);
				list($pil_b, $opt_b) = split("B)", $opt_b_all);
				list($pil_c, $opt_c) = split("C)", $opt_c_all);
				list($pil_d, $opt_d) = split("D)", $opt_d_all);
				
				$ins_log_sms_view = "INSERT INTO sms.quiz_log (log_time, employee_id, description) 
														VALUES (NOW(), '".$_SESSION['empCodeSms']."', 'Quiz Manual View Soal (".$row_slc_soal[0].")');";
				
				$res_ins_log_sms_view = $gatesms->ExecuteQuery($ins_log_sms_view, "sms");
				
				echo "<thead><tr align='left'>";
				echo "<th colspan='3'>".$soal."</th>";
				echo "</tr></thead>";
				echo "<tr>";
				echo "<td width='3%'></td>";							
				echo "<td width='1%'>A</td>";				
				echo "<td width='96%'><Input type = 'Radio' Name ='option_".$row_slc_soal[0]."' value= '1'>".$opt_a."</td>";			
				echo "</tr>";
				echo "<tr>";
				echo "<td width='3%'></td>";
				echo "<td width='1%'>B</td>";								
				echo "<td width='96%'><Input type = 'Radio' Name ='option_".$row_slc_soal[0]."' value= '2'>".$opt_b."</td>";			
				echo "</tr>";
				echo "<tr>";
				echo "<td width='3%'></td>";
				echo "<td width='1%'>C</td>";								
				echo "<td width='96%'><Input type = 'Radio' Name ='option_".$row_slc_soal[0]."' value= '3'>".$opt_c."</td>";			
				echo "</tr>";
				echo "<tr>";
				echo "<td width='3%'></td>";
				echo "<td width='1%'>D</td>";								
				echo "<td width='96%'><Input type = 'Radio' Name ='option_".$row_slc_soal[0]."' value= '4'>".$opt_d."</td>";			
				echo "</tr>";						
			}
			?>
			<tr align="center">
 			<td><input type="submit" name="Submit" value="Submit" ></td>										
 		</tr>
	</table>
</form>
			<?php
		} else {
			//echo "Anda Sudah menyelesaikan SMS quiz";
			$pesan = "Anda Sudah menyelesaikan SMS quiz";
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
			
			
			<?php
		}
	}
}
?>


			
			
</body>
</html>
