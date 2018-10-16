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
	
	$set_id				= $_SESSION['unitSetIdSoal'];
	$regional 		= $_SESSION['unitRegSoal']; //$_POST["mnRegional"];
	$job_function = $_GET["lvl"];
	$question_id 	= $_GET["id"];
	
	// check soal apakah sudah ada di question_active
	$slc_qs_active 	= "SELECT * FROM question_active"
									." WHERE question_id = '".$question_id."';";
									
	$res_slc_qs_active = mysql_query($slc_qs_active);
		
	?>
	
	<table width="600" border="0" align="center">
  <tr>
    <td width="600" height="65" valign="top" bgcolor="#FFFFFF"><table width="585" border="0" align="center" bgcolor="#810704">
      <tr>
        <td width="587" height="41" align="center"><span class="style9">PNP</span></td>
      </tr>
    </table></td>
  </tr>
  <tr>
  <td height="6" valign="top"></td>
  </tr>
  <tr>
    <td height="474" valign="top">

<form name="form" action="process_edit_soal.php?id=<?php echo $question_id; ?>" method="post" enctype="multipart/form-data">

<table width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td width="600" height="26" valign="top">
        	<table width="585" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#810704">
          	<tr>
            	<td width="587" height="19"><div align="center" class="style1">Edit Upload Soal PNP </div></td>
            </tr>
        	</table>
       	</td>
      </tr>
      <tr>
        <td height="427" valign="top">
	
	<table border="0" cellpadding="5" cellspacing="0" width="400" align= "center">
			
	<?php
	$i=1;
	if($res_slc_qs_active) {
		$row_slc_qs_active = mysql_fetch_row($res_slc_qs_active)
			?>
			<tr>
				<td><b>Quetion</b></td>
				<td><textarea name="equestion" cols=60 rows=4><?php echo $row_slc_qs_active[2]; ?></textarea></td>
			</tr>
			<tr>
				<td><b>Option A</b></td>
				<td><textarea name="eoptiona" cols=60 rows=4><?php echo $row_slc_qs_active[3]; ?></textarea></td>
			</tr>
			<tr>
				<td><b>Option B</b></td>
				<td><textarea name="eoptionb" cols=60 rows=4><?php echo $row_slc_qs_active[4]; ?></textarea></td>
			</tr>
			<tr>
				<td><b>Option C</b></td>
				<td><textarea name="eoptionc" cols=60 rows=4><?php echo $row_slc_qs_active[5]; ?></textarea></td>
			</tr>
			<tr>
				<td><b>Option D</b></td>
				<td><textarea name="eoptiond" cols=60 rows=4><?php echo $row_slc_qs_active[6]; ?></textarea></td>
			</tr>
			<tr>
				<td><b>Solution</b></td>
				<td><input name="esolution" id="esolution" type="text" class="righttextbox" value="<?php echo $row_slc_qs_active[8]; ?>" /></td>
			</tr>
			<tr>
				<td><b>Waktu</b></td>
				<td><input name="ewaktu" id="ewaktu" type="text" class="righttextbox" value="<?php echo $row_slc_qs_active[9]; ?>" /></td>
			</tr>
			<tr>
				<td><b>Code Soal</b></td>
				<td><input name="ecdsoal" id="ecdsoal" type="text" class="righttextbox" value="<?php echo $row_slc_qs_active[10]; ?>" /></td>
			</tr>
			<tr>
				<td><b>Mandatory</b></td>
				<td><input name="emandatory" id="emandatory" type="text" class="righttextbox" value="<?php echo $row_slc_qs_active[18]; ?>" /></td>
			</tr>	
			<tr>
				<table align="center">
					<tr>							
						<td><input name="edit" type="submit" value="Edit" /></td>					
						<td></td>
						</form>
						<td><input name="cancel" type="submit" value="Cancel" onclick= "javascript:CloseWindow()"/></td>
					</tr>
				</table>
			</tr>	
			
			
		<?php				
		}
	?>
</table>

</table>

</td>
      </tr>
          
    </table>

<?php
//</form>
?>
	
	</td>
  </tr>
</table>

</body>
</html>