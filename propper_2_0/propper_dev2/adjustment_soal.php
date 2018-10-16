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

<script type="text/javascript">
function notEmpty(){	
	var acorrecttext = document.getElementById("acorrect");
	var aketerangantext = document.getElementById("aketerangan");
	
	if((acorrecttext.value == "") || (aketerangantext.value == "")){
		alert("Mohon Entry Semua Data !!!");		
		return false;
	}
	return true;
}
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
	
	// check jml user yang di lakukan adjustment
	$slc_jml_user = "SELECT employee_id FROM score_detail_archive 
										WHERE question_id = '".$question_id."' 
										AND is_correct = 0 
										AND set_id = '".$set_id."'
										AND regional = '".$regional."'
										AND end_assess IS NOT NULL;";
	
	$res_slc_jml_user = mysql_query($slc_jml_user);
  $jml_user = mysql_num_rows($res_slc_jml_user);
  
	$res_slc_qs_active = mysql_query($slc_qs_active);
		
	?>
	
	<table width="600" border="0" align="center">
  <tr>
    <td width="600" height="65" valign="top" bgcolor="#FFFFFF">
    	<table width="585" border="0" align="center" bgcolor="#810704">
        <tr>
         <td width="587" height="41" align="center"><span class="style9">PNP</span></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height="6" valign="top"></td>
  </tr>
  <tr>
    <td height="474" valign="top">

     <form name="form" action="process_adjust_soal.php?id=<?php echo $question_id; ?>" method="post" enctype="multipart/form-data">
     
     <table width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
       <tr>
        <td width="600" height="26" valign="top">
        	<table width="585" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#810704">
          	<tr>
            	<td width="587" height="19"><div align="center" class="style1">Adjustment Soal PNP </div></td>
            </tr>
        	</table>
       	</td>
       </tr>
       <tr>
        <td height="427" valign="top">
	
	      <table border="0" cellpadding="5" cellspacing="0" width="550" align= "center">
			
	<?php
	$i=1;
	if($res_slc_qs_active) {
		$row_slc_qs_active = mysql_fetch_row($res_slc_qs_active)
			?>
			<tr>
				<td valign="top"><b>Quetion</b></td>
				<td><textarea name="aquestion" readonly="readonly" cols=50 rows=4><?php echo $row_slc_qs_active[2]; ?></textarea></td>
			</tr>
			<tr>
				<td valign="top"><b>Option A</b></td>
				<td><textarea name="aoptiona" readonly="readonly" cols=50 rows=4><?php echo $row_slc_qs_active[3]; ?></textarea></td>
			</tr>
			<tr>
				<td valign="top"><b>Option B</b></td>
				<td><textarea name="aoptionb" readonly="readonly" cols=50 rows=4><?php echo $row_slc_qs_active[4]; ?></textarea></td>
			</tr>
			<tr>
				<td valign="top"><b>Option C</b></td>
				<td><textarea name="aoptionc" readonly="readonly" cols=50 rows=4><?php echo $row_slc_qs_active[5]; ?></textarea></td>
			</tr>
			<tr>
				<td valign="top"><b>Option D</b></td>
				<td><textarea name="aoptiond" readonly="readonly" cols=50 rows=4><?php echo $row_slc_qs_active[6]; ?></textarea></td>
			</tr>
			<tr>
				<td valign="top"><b>Wrong</b></td>
				<td><input name="awrong" readonly="readonly" id="ewrong" type="text" class="righttextbox" value="<?php echo $row_slc_qs_active[8]; ?>" /></td>
			</tr>
			<tr>
				<td valign="top"><b>Correct <font color="red">*</font></b></td>
				<td><input name="acorrect" id="ecorrect" type="text" class="righttextbox" value="" /></td>
			</tr>
			<tr>
				<td valign="top"><b>Jumlah CSR</b></td>
				<td><input name="ajmlcsr" id="ajmlcsr" readonly="readonly" type="text" class="righttextbox" value="<?php echo $jml_user; ?>" /> User</td>
			</tr>			
			<tr>
				<td valign="top"><b>Keterangan <font color="red">*</font></b></td>
				<td><textarea name="aketerangan" cols=50 rows=4></textarea></td>
			</tr>
			<tr>
				<table align="center">
					<tr>
						<td><input name="adjustment" type="submit" value="Adjustment" onclick="return notEmpty()" /></td>					
						<td></td>
						</form>
						<td><input name="cancel" type="submit" value="Cancel" onclick= "javascript:CloseWindow()"/></td>
					</tr>
				</table>
			</tr>	
			
			
		<?php				
		}
	?>
	<tr>
	 <td height="19"> 				
   </td>
  <tr>
	<tr>
	 <td> 
		<table width="585" border="0" align="center" cellpadding="0" cellspacing="0">
     <tr>
       <td width="587" height="19"><font color="red">*</font> Data harus di isi untuk report adjustment test PnP.</td>
     </tr>
    </table>		
   </td>
  <tr>
	
	
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