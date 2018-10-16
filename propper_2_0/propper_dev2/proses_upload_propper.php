<?php
	#echo "MASUK";
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
-->
</style>
<body>

<?php
	$tanggal = date('Y-m-d');
	include "connection.php";
	//$unit_type 		= $_POST["mnUnitType"];
	$set_id			= $_SESSION['unitSetIdSoal'];
	$regional 		= $_SESSION['unitRegSoal']; //$_POST["mnRegional"];
	$assess_id		= $_SESSION['AssessIdSoal']; //$_SESSION['aSSeSSiD'];
	$jenisSoal		= $_POST['jenisSoal'];
	$periode 		= $_POST["tahun"].$_POST["bulan"];
	$start_assess	= $_POST["start_assess"];
	$end_assess		= $_POST["end_assess"];
	#echo $set_id;
	// Variable File
	$infoFile 		= pathinfo($_FILES['file_source']['name']);	
	$baseName  		= $infoFile['basename'];
	$fileName  		= $infoFile['filename'];
	$extension 		= $infoFile['extension'];
	
	//Kosongkan jenis soal yg akan di upload
	$delQuery = "DELETE FROM pnp.`dev_cc_question_temp` WHERE TYPE = '$jenisSoal'";
	$execDelQuery = mysql_query($delQuery);
	
	// name file yang di upload  not extension
	$fileUpload_name = $set_id.$regional.$job_function.$periode.mt_rand(1000, 100000);
	
	// file yang di upload
	$fileUpload = $fileUpload_name.".".$extension;
	
	// ===== Proses Upload File ======
	
	if (($baseName != "") && ($extension == "csv")) {
		$file = $_FILES['file_source'][tmp_name];
		
		if (($handle = fopen($file, "r")) !== FALSE) {
			$load_qs_temp = "INSERT INTO dev_cc_question_temp VALUES ";
			
			while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
				$data_csv .= "('";
        			$num = count($data);
					
				for ($c=0; $c < $num; $c++) {
				//	$data_csv .= $data[$c]."'";
					$data_csv .= str_replace("'", "\"", $data[$c])."'";
					if($c != $num-1) {
						$data_csv .= ", '";
					}
        			}
				$data_csv .= ",'$jenisSoal', '$tanggal'), ";
				#echo $data_csv."\n";
 	   		}
				
			$data_csv = substr($data_csv, 0, -2);	
			$load_qs_temp .= $data_csv.";";
			
			fclose($handle);
			
			//Cek jumlah soal di temp
			$cekData = "SELECT * FROM `dev_cc_question_temp` WHERE TYPE = '$jenisSoal'";
			$execCekData = mysql_query($cekData);
			$rowData = mysql_num_rows($execCekData);
			if($rowData == 0){
				// Load file to question_temp_...
				$res_load_qs_temp = mysql_query($load_qs_temp);
				
				if($res_load_qs_temp){
					$pesan = "Upload File PNP Success";
				}else{
					$pesan = "Error : Gagal upload ke data temp";
				}
			}else if($rowData > 0){
				//delete question_temp
				$delData = "DELETE FROM `dev_cc_question_temp` WHERE TYPE = '$jenisSoal'";
				
				// Load file to question_temp_...
				$res_load_qs_temp = mysql_query($load_qs_temp);
				
				if($res_load_qs_temp){
					$pesan = "Upload File PNP Success";
				}else{
					$pesan = "Error : Gagal upload ke data temp";
				}
			}else{
				$pesan = "Error : Data soal masih ada, Hapus terlebih dahulu";
			}
		}else {
			$pesan = "Error : Open File";
		}
	}else {
		$pesan = "Error : File Harus format CSV";
	}
	// End proses upload

?>

<table width="361" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="361" height="270" valign="middle"><table width="337" border="0" align="center">
      <tr>
        <td width="331" height="33" valign="top" bgcolor="#FFFFFF"><table width="320" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#810704">
          <tr>
            <td width="310"><div align="center" class="style1"><?php echo $pesan; ?></div></td>
          </tr>
        </table>          </td>
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