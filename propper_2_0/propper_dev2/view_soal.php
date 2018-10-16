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

a:visited {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	color: #FFFF00;
	text-decoration: none;
}
a:focus {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	color: #ffffff;
	text-decoration: none;
}
a:link {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	color: #FFFF00;
	text-decoration: none;
	margin-left: 7px;
}
a:hover {
	text-decoration: none;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 15px;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	color: #00CC00;
}
-->
</style>
<body>
<?php   

	include "connection.php";
	
	$set_id				= $_SESSION['unitSetIdSoal'];
	$regional 		= $_SESSION['unitRegSoal']; //$_POST["mnRegional"];
	$job_function = $_GET["lvl"];
	
	// check soal apakah sudah ada di question_active
	$slc_qs_active 	= "SELECT * FROM question_active"
						." WHERE set_id = '".$set_id."' AND"
						." level = '".$job_function."' AND"
						." regional = '".$regional."'";
	
	$res_slc_qs_active = mysql_query($slc_qs_active);
	
	?>
	
	<table width="1600" border="0" align="center">
  <tr>
    <td width="1600" height="65" valign="top" bgcolor="#FFFFFF"><table width="1590" border="0" align="center" bgcolor="#810704">
      <tr>
        <td width="1590" height="41" align="center"><span class="style9">PNP</span></td>
      </tr>
    </table></td>
  </tr>
  <tr>
  <td height="6" valign="top"></td>
  </tr>
  <tr>
    <td height="474" valign="top">


<table width="1600" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td width="1600" height="26" valign="top">
        	<table width="1590" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#810704">
          	<tr>
            	<td width="1590" height="19"><div align="center" class="style1">View Upload Soal PNP </div></td>
            </tr>
        	</table>
       	</td>
      </tr>
      
      
   	<tr>  		
    	<td width="1600" height="26" valign="top">			
    		<table width="1590" border="0" align="center" cellpadding="0" cellspacing="0" >
      	<tr>
        	<td width="1590" height="19">
            	
          	<table border="0" align="left" cellpadding="5" cellspacing="0" >
          		
          		<?php
          		
          			if($set_id == 1) {
          				$uts = "Grapari";
          			} else if ($set_id == 2) {
          				$uts = "CallCenter";
          			} else if ($set_id == 3) {
          				$uts = "Induction";
          			}
          			
          		?>
          		
          		<form name="form" action="process_edit_assess.php?uts=<?php echo $uts ?>&lvl=<?php echo $job_function ?> " method="post" enctype="multipart/form-data">
          		          		
          		<tr>
          			
            		<td><b><?php echo $uts ?></b></td>
            		<td>Start Assess
									<select name="start_assess" id="start_assess">
        						
        						<?php 
        						for($k = 1; $k <= 31; $k++) {
        							echo "<option value=$k>$k</option>";        							
        						}
        						?>
        						
        					</select>  
	End Assess 
	<select name="end_assess" id="end_assess">
        	
        	<?php 
        						for($k = 1; $k <= 31; $k++) {
        							echo "<option value=$k>$k</option>";        							
        						}
        						?>
        						
      </select>
</td>

          	  <td>
          	  	<input name="submit" type="submit" value="Update" />
          	  </td>	      
          	           	            	
            	</tr>
            	
            	</form>
            	
           <?php 	
           
           	if(($set_id == '1') && ($job_function == '3')){
          		
          		?>
          		
          		<form name="form" action="process_edit_assess.php?uts=gerai&lvl=<?php echo $job_function ?> " method="post" enctype="multipart/form-data">
          		
          		
          		<tr>
          			
            		<td><b>Gerai</b></td>
            		<td>Start Assess
									<select name="start_assess" id="start_assess">
        						
        						<?php 
        						for($k = 1; $k <= 31; $k++) {
        							echo "<option value=$k>$k</option>";        							
        						}
        						?>
        						
      </select>  
	End Assess 
	<select name="end_assess" id="end_assess">
        	
        					<?php 
        						for($k = 1; $k <= 31; $k++) {
        							echo "<option value=$k>$k</option>";        							
        						}
        					?>
        						
      </select>
</td>

          	           	            	
            	<td>
          	  	<input name="submit" type="submit" value="Update" />
          	  </td>	      
          	           	            	
            	</tr>
            	
            	</form>
          		
          		
          		
          		
          		<form name="form" action="process_edit_assess.php?uts=plasa&lvl=<?php echo $job_function ?> " method="post" enctype="multipart/form-data">
          		
          		
          		<tr>
          			
            		<td><b>Plasa</b></td>
            		<td>Start Assess
									<select name="start_assess" id="start_assess">
        						
        						<?php 
        							for($k = 1; $k <= 31; $k++) {
        								echo "<option value=$k>$k</option>";        							
        							}
        						?>
        						
      </select>  
	End Assess 
	<select name="end_assess" id="end_assess">
        	
        					<?php 
        						for($k = 1; $k <= 31; $k++) {
        							echo "<option value=$k>$k</option>";        							
        						}
        					?>
        					
      </select>
</td>

          	           	            	
            	<td>
          	  	<input name="submit" type="submit" value="Update" />
          	  </td>	      
          	           	            	
            	</tr>
            	
            	</form>
          		
          		
          		<?php
          	}
           ?> 	
            	<tr>
            		<td height="19"></td>
            	</tr>
        		</table>	
            		
       		</td>
       	</tr>
    	</table>

  	</td>
    </tr>
      
      <tr>
        <td height="427" valign="top">
        	

	<table border="1" align="center" cellpadding="5" cellspacing="0" width="1600">
		<tr>
			<td width="50" bgcolor="#333333"><div align="center" class="style1">No</div></td>
			<td width="400" bgcolor="#333333"><div align="center" class="style1">Question</div></td>
			<td width="200" bgcolor="#333333"><div align="center" class="style1">Option A</div></td>
			<td width="200" bgcolor="#333333"><div align="center" class="style1">Option B</div></td>
			<td width="200" bgcolor="#333333"><div align="center" class="style1">Option C</div></td>
			<td width="200" bgcolor="#333333"><div align="center" class="style1">Option D</div></td>			
			<td width="60" bgcolor="#333333"><div align="center" class="style1">Solution</div></td>			
			<td width="60" bgcolor="#333333"><div align="center" class="style1">Waktu</div></td>			
			<td width="110" bgcolor="#333333"><div align="center" class="style1">Code Soal</div></td>			
			<td width="60" bgcolor="#333333"><div align="center" class="style1">Mandatory</div></td>
			<td width="60" bgcolor="#333333"><div align="center" class="style1">Edit</div></td>
			<td width="60" bgcolor="#333333"><div align="center" class="style1">Adjustment</div></td>			
		</tr>		
	<?php
	$i=1;
	if($res_slc_qs_active) {
		while($row_slc_qs_active = mysql_fetch_row($res_slc_qs_active)) {
			echo '<tr>';
			echo '<td bgcolor="#333333"><div align="center" class="style1">'.$i++.'</div></td>';		
			echo '<td>'.$row_slc_qs_active[2].'</td>';
			echo '<td>'.$row_slc_qs_active[3].'</td>';
			echo '<td>'.$row_slc_qs_active[4].'</td>';
			echo '<td>'.$row_slc_qs_active[5].'</td>';
			echo '<td>'.$row_slc_qs_active[6].'</td>';			
			echo '<td><center>'.$row_slc_qs_active[8].'</center></td>';		
			echo '<td><center>'.$row_slc_qs_active[9].'</center></td>';		
			echo '<td><center>'.$row_slc_qs_active[10].'</center></td>';		
			echo '<td><center>'.$row_slc_qs_active[18].'</center></td>';	
			echo '<td bgcolor="#333333"><div align="center" class="style1"><a href="edit_soal.php?id='.$row_slc_qs_active[0].'">Edit</a></div></td>';	
			
		//	$chk_adj = "SELECT * FROM question_adjustment WHERE question_id = '".$row_slc_qs_active[0]."';";
		//	$res_chk_adj = mysql_query($chk_adj);
		//  $row = mysql_num_rows($res_chk_adj);
			
		//	if($row >= 1) {
				echo '<td bgcolor="#333333"><div align="center" class="style1"></div></td>';				
		//	} else {
		//		echo '<td bgcolor="#333333"><div align="center" class="style1"><a href="adjustment_soal.php?id='.$row_slc_qs_active[0].'">Adjustment</a></div></td>';	
		//	}
			
			
			echo '</tr>';
		}
	}
	?>
</table>

<BR>


<table align="center">
	<tr>
		<td><input name="submit" type="submit" value="Close" onclick= "javascript:CloseWindow()"/></td>
	</tr>
</table>	
	<?php
							

?>

       </table>

</td>
      </tr>
      
    </table>
	
	</td>
  </tr>
</table>

</body>
</html>