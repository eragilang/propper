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
	echo $jenisSoal;
	// Variable File
	$infoFile 		= pathinfo($_FILES['file_source']['name']);	
	$baseName  		= $infoFile['basename'];
	$fileName  		= $infoFile['filename'];
	$extension 		= $infoFile['extension'];
	
	##BOBOT
	$tl_gpr_proses = $_POST['tl_gpr_proses'];
	$spv_gpr_proses = $_POST['spv_gpr_proses'];
	$cs_gpr_proses = $_POST['cs_gpr_proses'];
	$fos_gpr_proses = $_POST['fos_gpr_proses'];
	$tl_gpr_produk = $_POST['tl_gpr_produk'];
	$spv_gpr_produk = $_POST['spv_gpr_produk'];
	$cs_gpr_produk = $_POST['cs_gpr_produk'];
	$fos_gpr_produk = $_POST['fos_gpr_produk'];
	$tl_gpr_perilaku = $_POST['tl_gpr_perilaku'];
	$spv_gpr_perilaku = $_POST['spv_gpr_perilaku'];
	$cs_gpr_perilaku = $_POST['cs_gpr_perilaku'];
	$fos_gpr_perilaku = $_POST['fos_gpr_perilaku'];
	
	$tl_cc_proses = $_POST['tl_cc_proses'];
	$spv_cc_proses = $_POST['spv_cc_proses'];
	$cs_cc_proses = $_POST['cs_cc_proses'];
	$tl_cc_produk = $_POST['tl_cc_produk'];
	$spv_cc_produk = $_POST['spv_cc_produk'];
	$cs_cc_produk = $_POST['cs_cc_produk'];
	$tl_cc_perilaku = $_POST['tl_cc_perilaku'];
	$spv_cc_perilaku = $_POST['spv_cc_perilaku'];
	$cs_cc_perilaku = $_POST['cs_cc_perilaku'];
	
	$tl_ec_proses = $_POST['tl_ec_proses'];
	$spv_ec_proses = $_POST['spv_ec_proses'];
	$cs_ec_proses = $_POST['cs_ec_proses'];
	$cho_ec_proses = $_POST['cho_ec_proses'];
	$tl_ec_produk = $_POST['tl_ec_produk'];
	$spv_ec_produk = $_POST['spv_ec_produk'];
	$cs_ec_produk = $_POST['cs_ec_produk'];
	$cho_ec_produk = $_POST['cho_ec_produk'];
	$tl_ec_perilaku = $_POST['tl_ec_perilaku'];
	$spv_ec_perilaku = $_POST['spv_ec_perilaku'];
	$cs_ec_perilaku = $_POST['cs_ec_perilaku'];
	$cho_ec_perilaku = $_POST['cho_ec_perilaku'];
	
	//Kosongkan jenis soal yg akan di upload
	$delQuery = "DELETE FROM pnp.`question_temp` WHERE TYPE = '$jenisSoal'";
	$execDelQuery = mysql_query($delQuery);
	
	// name file yang di upload  not extension
	$fileUpload_name = $set_id.$regional.$job_function.$periode.mt_rand(1000, 100000);
	
	// file yang di upload
	$fileUpload = $fileUpload_name.".".$extension;
	
	// ===== Proses Upload File ======
	
	if (($baseName != "") && ($extension == "csv")) {
		$file = $_FILES['file_source'][tmp_name];
		
		if (($handle = fopen($file, "r")) !== FALSE) {
			$load_qs_temp = "INSERT INTO question_temp VALUES ";
			
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
			$cekData = "SELECT * FROM `question_temp` WHERE TYPE = '$jenisSoal'";
			$execCekData = mysql_query($cekData);
			$rowData = mysql_num_rows($execCekData);
			if($rowData == 0){
				// Load file to question_temp_...
				$res_load_qs_temp = mysql_query($load_qs_temp);
				//echo $load_qs_temp;
				if($res_load_qs_temp){
					$updQuery = "UPDATE pnp.`job_value` set bobot_nilai='".$tl_gpr_proses."'  WHERE jenis_soal = 'proses' AND unit_type='GRAPARI' AND group_job_function='TL'";
					$UpdQuery = mysql_query($updQuery);
					$updQuery1 = "UPDATE pnp.`job_value` set bobot_nilai='".$spv_gpr_proses."'  WHERE jenis_soal = 'proses' AND unit_type='GRAPARI' AND group_job_function='SPV'";
					$UpdQuery1 = mysql_query($updQuery1);
					$updQuery2 = "UPDATE pnp.`job_value` set bobot_nilai='".$cs_gpr_proses."' WHERE jenis_soal = 'proses' AND unit_type='GRAPARI' AND group_job_function='CS'";
					$UpdQuery2 = mysql_query($updQuery2);
					$updQuery27 = "UPDATE pnp.`job_value` set bobot_nilai='".$fos_gpr_proses."' WHERE jenis_soal = 'proses' AND unit_type='GRAPARI' AND group_job_function='FOS'";
					$UpdQuery27 = mysql_query($updQuery27);
					
					$updQuery3 = "UPDATE pnp.`job_value` set bobot_nilai='".$tl_gpr_produk."' WHERE jenis_soal = 'produk' AND unit_type='GRAPARI' AND group_job_function='TL'";
					$UpdQuery3 = mysql_query($updQuery3);
					$updQuery4 = "UPDATE pnp.`job_value` set bobot_nilai='".$spv_gpr_produk."' WHERE jenis_soal = 'produk' AND unit_type='GRAPARI' AND group_job_function='SPV'";
					$UpdQuery4 = mysql_query($updQuery4);
					$updQuery5 = "UPDATE pnp.`job_value` set bobot_nilai='".$cs_gpr_produk."' WHERE jenis_soal = 'produk' AND unit_type='GRAPARI' AND group_job_function='CS'";
					$UpdQuery5 = mysql_query($updQuery5);
					$updQuery28 = "UPDATE pnp.`job_value` set bobot_nilai='".$fos_gpr_produk."' WHERE jenis_soal = 'produk' AND unit_type='GRAPARI' AND group_job_function='FOS'";
					$UpdQuery28 = mysql_query($updQuery28);
					
					$updQuery6 = "UPDATE pnp.`job_value` set bobot_nilai='".$tl_gpr_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='GRAPARI' AND group_job_function='TL'";
					$UpdQuery6 = mysql_query($updQuery6);
					$updQuery7 = "UPDATE pnp.`job_value` set bobot_nilai='".$spv_gpr_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='GRAPARI' AND group_job_function='SPV'";
					$UpdQuery7 = mysql_query($updQuery7);
					$updQuery8 = "UPDATE pnp.`job_value` set bobot_nilai='".$cs_gpr_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='GRAPARI' AND group_job_function='CS'";
					$UpdQuery8 = mysql_query($updQuery8);
					$updQuery29 = "UPDATE pnp.`job_value` set bobot_nilai='".$fos_gpr_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='GRAPARI' AND group_job_function='FOS'";
					$UpdQuery29 = mysql_query($updQuery29);
					
					$updQuery9 = "UPDATE pnp.`job_value` set bobot_nilai='".$tl_cc_proses."'  WHERE jenis_soal = 'proses' AND unit_type='CALLCENTER' AND group_job_function='TL'";
					$UpdQuery9 = mysql_query($updQuery9);
					$updQuery10 = "UPDATE pnp.`job_value` set bobot_nilai='".$spv_cc_proses."' WHERE jenis_soal = 'proses' AND unit_type='CALLCENTER' AND group_job_function='SPV'";
					$UpdQuery10 = mysql_query($updQuery10);
					$updQuery11 = "UPDATE pnp.`job_value` set bobot_nilai='".$cs_cc_proses."' WHERE jenis_soal = 'proses' AND unit_type='CALLCENTER' AND group_job_function='CS'";
					$UpdQuery11 = mysql_query($updQuery11);
					
					$updQuery12 = "UPDATE pnp.`job_value` set bobot_nilai='".$tl_cc_produk."' WHERE jenis_soal = 'produk' AND unit_type='CALLCENTER' AND group_job_function='TL'";
					$UpdQuery12 = mysql_query($updQuery12);
					$updQuery13 = "UPDATE pnp.`job_value` set bobot_nilai='".$spv_cc_produk."' WHERE jenis_soal = 'produk' AND unit_type='CALLCENTER' AND group_job_function='SPV'";
					$UpdQuery13 = mysql_query($updQuery13);
					$updQuery14 = "UPDATE pnp.`job_value` set bobot_nilai='".$cs_cc_produk."' WHERE jenis_soal = 'produk' AND unit_type='CALLCENTER' AND group_job_function='CS'";
					$UpdQuery14 = mysql_query($updQuery14);
					
					$updQuery15 = "UPDATE pnp.`job_value` set bobot_nilai='".$tl_cc_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='CALLCENTER' AND group_job_function='TL'";
					$UpdQuery15 = mysql_query($updQuery15);
					$updQuery16 = "UPDATE pnp.`job_value` set bobot_nilai='".$spv_cc_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='CALLCENTER' AND group_job_function='SPV'";
					$UpdQuery16 = mysql_query($updQuery16);
					$updQuery17 = "UPDATE pnp.`job_value` set bobot_nilai='".$cs_cc_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='CALLCENTER' AND group_job_function='CS'";
					$UpdQuery17 = mysql_query($updQuery17);
					
					$updQuery18 = "UPDATE pnp.`job_value` set bobot_nilai='".$tl_ec_proses."' WHERE jenis_soal = 'proses' AND unit_type='WEBSERVICE' AND group_job_function='TL'";
					$UpdQuery18 = mysql_query($updQuery18);
					$updQuery19 = "UPDATE pnp.`job_value` set bobot_nilai='".$spv_ec_proses."' WHERE jenis_soal = 'proses' AND unit_type='WEBSERVICE' AND group_job_function='SPV'";
					$UpdQuery19 = mysql_query($updQuery19);
					$updQuery20 = "UPDATE pnp.`job_value` set bobot_nilai='".$cs_ec_proses."' WHERE jenis_soal = 'proses' AND unit_type='WEBSERVICE' AND group_job_function='CS'";
					$UpdQuery20 = mysql_query($updQuery20);
					$updQuery30 = "UPDATE pnp.`job_value` set bobot_nilai='".$cho_ec_proses."' WHERE jenis_soal = 'proses' AND unit_type='WEBSERVICE' AND group_job_function='CHO'";
					$UpdQuery30 = mysql_query($updQuery30);
					
					$updQuery21 = "UPDATE pnp.`job_value` set bobot_nilai='".$tl_ec_produk."' WHERE jenis_soal = 'produk' AND unit_type='WEBSERVICE' AND group_job_function='TL'";
					$UpdQuery21 = mysql_query($updQuery21);
					$updQuery22 = "UPDATE pnp.`job_value` set bobot_nilai='".$spv_ec_produk."' WHERE jenis_soal = 'produk' AND unit_type='WEBSERVICE' AND group_job_function='SPV'";
					$UpdQuery22 = mysql_query($updQuery22);
					$updQuery23 = "UPDATE pnp.`job_value` set bobot_nilai='".$cs_ec_produk."' WHERE jenis_soal = 'produk' AND unit_type='WEBSERVICE' AND group_job_function='CS'";
					$UpdQuery23 = mysql_query($updQuery23);
					$updQuery31 = "UPDATE pnp.`job_value` set bobot_nilai='".$cho_ec_produk."' WHERE jenis_soal = 'produk' AND unit_type='WEBSERVICE' AND group_job_function='CHO'";
					$UpdQuery31 = mysql_query($updQuery31);
					
					$updQuery24 = "UPDATE pnp.`job_value` set bobot_nilai='".$tl_ec_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='WEBSERVICE' AND group_job_function='TL'";
					$UpdQuery24 = mysql_query($updQuery24);
					$updQuery25 = "UPDATE pnp.`job_value` set bobot_nilai='".$spv_ec_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='WEBSERVICE' AND group_job_function='SPV'";
					$UpdQuery25 = mysql_query($updQuery25);
					$updQuery26 = "UPDATE pnp.`job_value` set bobot_nilai='".$cs_ec_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='WEBSERVICE' AND group_job_function='CS'";
					$UpdQuery26 = mysql_query($updQuery26);
					$updQuery32 = "UPDATE pnp.`job_value` set bobot_nilai='".$cho_ec_perilaku."' WHERE jenis_soal = 'perilaku' AND unit_type='WEBSERVICE' AND group_job_function='CHO'";
					$UpdQuery32 = mysql_query($updQuery32);
					$pesan = "Upload File PNP Success";
				}else{
					$pesan = "Error : Gagal upload ke data temp";
				}
			}else if($rowData > 0){
				//delete question_temp
				$delData = "DELETE FROM `question_temp` WHERE TYPE = '$jenisSoal'";
				
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