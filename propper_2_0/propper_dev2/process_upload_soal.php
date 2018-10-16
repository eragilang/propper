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
-->
</style>
<body>
<?php   

	include "connection.php";
	
	//$unit_type 		= $_POST["mnUnitType"];
	$set_id		= $_SESSION['unitSetIdSoal'];
	$regional 		= $_SESSION['unitRegSoal']; //$_POST["mnRegional"];
	$job_function 	= $_POST["mnJobFunction"];
	$assess_id		= $_SESSION['AssessIdSoal']; //$_SESSION['aSSeSSiD'];
	$periode 		= $_POST["tahun"].$_POST["bulan"];
	$start_assess		= $_POST["start_assess"];
	$end_assess		= $_POST["end_assess"];


	// Variable File
	$infoFile 		= pathinfo($_FILES['file_source']['name']);	
	$baseName  		= $infoFile['basename'];
	$fileName  		= $infoFile['filename'];
	$extension 		= $infoFile['extension'];
	
	// name file yang di upload  not extension
	$fileUpload_name = $set_id.$regional.$job_function.$periode.mt_rand(1000, 100000);
	
	// file yang di upload
	$fileUpload = $fileUpload_name.".".$extension;
	
	if ($set_id == 3) {
		//$job_function = 5;
		$slc_lvl_3 = "SELECT DISTINCT LEVEL FROM assess_type WHERE assess_id = '".$assess_id."';"; 
		$res_slc_lvl_3 = mysql_query($slc_lvl_3);
		
		if($res_slc_lvl_3) {
			$row_slc_lvl_3 = mysql_fetch_array($res_slc_lvl_3);
			$job_function = $row_slc_lvl_3['0'];
		}
	}
	
/*
	if ($unit_type == "Induction") {
		$set_id = 0;
		$assess_id = "R3G";
	} else if (($unit_type == "Grapari") || ($unit_type == "Gerai") || ($unit_type == "Plasa")) {
		$set_id = 1;
		$assess_id = "REG";
	} else if ($unit_type == "CallCenter") {
		$set_id = 2;
		$assess_id = "REG";
	}
*/	

	// check soal apakah sudah ada di question_active
	$chk_qs_active 	= "SELECT * FROM dev_question_active"
						." WHERE set_id = '".$set_id."' AND"
						." level = '".$job_function."' AND"
						." regional = '".$regional."'";
					//	." periode = '".$periode."'";
	
	
	// Create question_temp_.....
	$create_tb_qs_temp = "CREATE TABLE `dev_question_temp_".$fileUpload_name."` (
  							`questions` text,
  							`sola` text,
  							`solb` text,
							`solc` text,
  							`sold` text,
  							`sole` text,
                            `solf` text,
                            `solg` text,
                             `solh` text,  
  							`solution` char(10) default NULL,
  							`g1` char(20) default NULL,
  							`difficulty` smallint(6) default NULL,
  							`source` text,
 							`mandatory` char(1) default '0'
						) ENGINE=MyISAM DEFAULT CHARSET=latin1;";  
	
	// load data csv to question_temp_...
/*	$load_qs_temp = 'LOAD DATA INFILE \''
				.$_SERVER['DOCUMENT_ROOT']."upload/".$fileUpload
    				. '\' INTO TABLE question_temp_'.$fileUpload_name
        			. ' FIELDS '
        			. ' TERMINATED BY \',\''
        			. ' ENCLOSED BY \'"\''
        			. ' ESCAPED BY \'\\\\\''
       			. ' LINES TERMINATED BY \'\\r\\n\';'; 									
*/
	
	// Insert question_active from question_temp
	$ins_qs_active 	= "INSERT INTO dev_question_active 
					   SELECT concat('Q',date_format(now(),'%y%m%d'),'-',lpad(floor(rand()*(9999999-1)+1),7,'0')) as id, 
					   ".$set_id.",
					   questions,
					   sola,
					   solb,
					   solc,
					   sold,
					   sole,
					   solf,
					   solg,
					   solh,"
					   ." solution,
					   difficulty,
					   g1,
					   ".$periode.", 
					   '".$assess_id."',
					   1,
					   source,
					   ".$job_function.",
					   4,
					   2,"
					   ." mandatory,
					   ".$regional." 
					   FROM dev_question_temp_".$fileUpload_name.";";
	
	// delete tabel question_temp_...
	$dlt_tbl_question_temp = "DROP TABLE dev_question_temp_".$fileUpload_name.";";
	
	// Bersihkan jawaban dari .
	$upd_qs_active = "UPDATE dev_question_active SET"
						." solution = replace(solution, '.', ',')"
						." WHERE solution LIKE '%.%'";	
	
	// Bersihkan question dari <>
	$upd_qs_active_qst = "UPDATE dev_question_active 
													SET 
													question = REPLACE(question ,'<','('), 
													question = REPLACE(question ,'>',')')
													WHERE question LIKE '%<%>%';";
		
	// Bersihkan option_a dari <>
	$upd_qs_active_opta = "UPDATE dev_question_active 
													SET 
													option_a = REPLACE(option_a ,'<','('), 
													option_a = REPLACE(option_a ,'>',')')
													WHERE option_a LIKE '%<%>%';";
	
	// Bersihkan option_b dari <>
	$upd_qs_active_optb = "UPDATE dev_question_active 
													SET 
													option_b = REPLACE(option_b ,'<','('), 
													option_b = REPLACE(option_b ,'>',')')
													WHERE option_b LIKE '%<%>%';";
	
	// Bersihkan option_c dari <>
	$upd_qs_active_optc = "UPDATE dev_question_active 
													SET 
													option_c = REPLACE(option_c ,'<','('), 
													option_c = REPLACE(option_c ,'>',')')
													WHERE option_c LIKE '%<%>%';";
		
	// Bersihkan option_d dari <>
	$upd_qs_active_optd = "UPDATE dev_question_active 
													SET 
													option_d = REPLACE(option_d ,'<','('), 
													option_d = REPLACE(option_d ,'>',')')
													WHERE option_d LIKE '%<%>%';";
	
	// Bersihkan option_e dari <>
	$upd_qs_active_opte = "UPDATE dev_question_active 
													SET 
													option_e = REPLACE(option_e ,'<','('), 
													option_e = REPLACE(option_e ,'>',')')
													WHERE option_e LIKE '%<%>%';";
	// Bersihkan option_f dari <>	
	$upd_qs_active_optf = "UPDATE dev_question_active
													SET
													option_f = REPLACE(option_f ,'<','('),
													option_f = REPLACE(option_f ,'>',')')
													WHERE option_f LIKE '%<%>%';";

	$upd_qs_active_optg = "UPDATE dev_question_active
													SET
													option_g = REPLACE(option_g ,'<','('),
													option_g = REPLACE(option_g ,'>',')')
													WHERE option_g LIKE '%<%>%';";
	
	$upd_qs_active_opth = "UPDATE dev_question_active
													SET
													option_h = REPLACE(option_h ,'<','('),
													option_h = REPLACE(option_h ,'>',')')
													WHERE option_h LIKE '%<%>%';";
	// Menset nb_question = 0 pada category_distribution
	$upd_cat_dis_0 = "UPDATE dev_category_distribution SET nb_question = '0'"
						." WHERE set_id = '".$set_id."' AND"
						." level = '".$job_function."' AND"
						." regional = '".$regional."'";
	
	// check question distribution
	$slc_qs_active = "SELECT cat_group, count(*), sum(is_mandatory),"
						." group_description FROM question_active qa, category ca"
						." WHERE qa.cat_id = ca.cat_id AND qa.set_id = '".$set_id."'"
						." AND level = '".$job_function."' AND qa.periode = '".$periode."'"
						." AND qa.regional = '".$regional."' GROUP BY cat_group;";
	
	
	if ($job_function == 3) {
		$unit_type_soal = "('grapari', 'gerai', 'plasa')"; 
		$end_assess = 30;
	} else {
		$unit_type_soal = "('grapari')";
	}

	$flagJml = 0;
	
	$unit_id = "(";
	
	// select unit_id dari t_unit_branch
	$slc_tu_branch = "SELECT unit_id FROM csdm2.t_unit_branch WHERE regional = '".$regional."' and unit_type IN ".$unit_type_soal.";";
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
		$upd_ass_period = "UPDATE dev_assess_period SET start_assess = ".$start_assess.", end_assess = ".$end_assess." WHERE unit_id IN ".$unit_id." AND LEVEL = '".$job_function."';";
	} else if ($set_id == 2) {
		$upd_ass_period = "UPDATE dev_assess_period SET start_assess = ".$start_assess.", end_assess = ".$end_assess." WHERE unit_type = 'callcenter' AND LEVEL = '".$job_function."';";
	} else if ($set_id == 3) {
		$upd_ass_period = "UPDATE dev_assess_period SET start_assess = ".$start_assess.", end_assess = ".$end_assess." WHERE unit_id = '0' AND LEVEL = '".$job_function."';";	
	}
	
	
	// ===== Proses Upload File ======
	
	if (($baseName != "") && ($extension == "csv")) {
		// Check soal from question_active
		$res_chk_qs_active =  mysql_query($chk_qs_active); //$dbGate->ExecuteQuery($chk_qs_active,"pnp"); 
       	$row = mysql_num_rows($res_chk_qs_active);
		
		if ($row < 1) {
			$file = $_FILES['file_source'][tmp_name];
		//	$HTTP_POST_FILES['file_source']['tmp_name'];
			
			// Open file CSV
			if (($handle = fopen($file, "r")) !== FALSE) {
				$load_qs_temp = "INSERT INTO dev_question_temp_".$fileUpload_name." VALUES ";
				
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
					$data_csv .= "), ";
 	   			}
    				$data_csv = substr($data_csv, 0, -2);	
				$load_qs_temp .= $data_csv.";";
					
				fclose($handle);
				
				// Create tabel tabel question_temp_...
				$res_create_tb_qs_temp = mysql_query($create_tb_qs_temp);  //$dbGate->ExecuteQuery($create_tb_qs_temp,"pnp"); 
				
				if ($res_create_tb_qs_temp) {						      								
					// Load file to question_temp_...
					$res_load_qs_temp = mysql_query($load_qs_temp); //$dbGate->ExecuteQuery($load_qs_temp,"pnp");  	
					
					if ($res_load_qs_temp) {
						// check jml soal
						
						//check min Soal
						$chk_min_soal = "SELECT DISTINCT max_question FROM assess_param WHERE level = '".$job_function."' AND set_id = '".$set_id."';";
						$res_chk_min_soal = mysql_query($chk_min_soal);
						$row_chk_min_soal = mysql_fetch_array($res_chk_min_soal);
						$min_soal = $row_chk_min_soal[0];
						
						$chk_jml_soal = "SELECT * FROM dev_question_temp_".$fileUpload_name.";";
						$res_chk_jml_soal = mysql_query($chk_jml_soal);					
						$jml_soal = mysql_num_rows($res_chk_jml_soal);
						
						if ($jml_soal > $min_soal) {
						
						// end
							
							// Insert to question_active														
							$res_ins_qs_active = mysql_query($ins_qs_active); // $dbGate->ExecuteQuery($ins_qs_active,"pnp"); 
							
							if ($res_ins_qs_active) {
								// Delete tabel question_temp_...
								$res_dlt_tbl_question_temp = mysql_query($dlt_tbl_question_temp);  //$dbGate->ExecuteQuery($dlt_tbl_question_temp,"pnp"); 
								
								// Delete file soal from server			
								// $dlt_file_server = unlink($_SERVER['DOCUMENT_ROOT']."upload/".$fileUpload);
								// echo $_SERVER['DOCUMENT_ROOT'];
							
								//Bersihkan Jawaban dari .			
								$res_upd_qs_active = mysql_query($upd_qs_active);  //$dbGate->ExecuteQuery($upd_qs_active,"pnp");  					
								//Bersihkan question, option_a,... dari <>		
								$res_upd_qs_active_qst = mysql_query($upd_qs_active_qst);  //$dbGate->ExecuteQuery($upd_qs_active_qst,"pnp");   					
								$res_upd_qs_active_opta = mysql_query($upd_qs_active_opta);  //$dbGate->ExecuteQuery($upd_qs_active_opta,"pnp");  					
								$res_upd_qs_active_optb = mysql_query($upd_qs_active_optb);  //$dbGate->ExecuteQuery($upd_qs_active_optb,"pnp");  								
								$res_upd_qs_active_optc = mysql_query($upd_qs_active_optc);  //$dbGate->ExecuteQuery($upd_qs_active_optc,"pnp");  					
								$res_upd_qs_active_optd = mysql_query($upd_qs_active_optd); //$dbGate->ExecuteQuery($upd_qs_active_optd,"pnp");  					
								$res_upd_qs_active_opte = mysql_query($upd_qs_active_opte); //$dbGate->ExecuteQuery($upd_qs_active_opte,"pnp");  					
								$res_upd_qs_active_optf = mysql_query($upd_qs_active_optf);

								$res_upd_qs_active_optg = mysql_query($upd_qs_active_optg);

								$res_upd_qs_active_opth = mysql_query($upd_qs_active_opth);
								//	if (($res_upd_qs_active) && ($res_upd_qs_active_qst) &&
								//		($res_upd_qs_active_opta) && ($res_upd_qs_active_optb) &&
								//		($res_upd_qs_active_optc) && ($res_upd_qs_active_optd) &&
								//		($res_upd_qs_active_opte)) {
							
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
											$upd_cat_dis = "UPDATE dev_category_distribution SET"
													." nb_question = '".$min_cat[$i]."' WHERE"
													." cat_group = '".$cat_id[$i]."' AND"
													." set_id = '".$set_id."' AND"
													." level = '".$job_function."' AND"
													." regional = '".$regional."';";
											
											$res_upd_cat_dis = mysql_query($upd_cat_dis);  //$dbGate->ExecuteQuery($upd_cat_dis,"pnp");  										
										}
										
										$res_upd_ass_period = mysql_query($upd_ass_period);												
										
										if ($res_upd_ass_period) {
											$pesan = "Upload File PNP Success";
										} else {
											$pesan = "Error : Set Periode ".mysql_error();
										}
										
										
										
									} else {
										$pesan = "Error : Check Category Soal ".mysql_error();
									}
								} else {
									$pesan = "Error : Clear Category ".mysql_error();
								}
									//	} else {
									//		$pesan = "GAGAL Update question_active";
									//	}					
							} else {
								// Delete tabel question_temp_...
								$res_dlt_tbl_question_temp = mysql_query($dlt_tbl_question_temp);  //$dbGate->ExecuteQuery($dlt_tbl_question_temp,"pnp");  
							
								$pesan = "Error : Insert Question Active ".mysql_error();
							}
						} else {
							// Delete tabel question_temp_...
							$res_dlt_tbl_question_temp = mysql_query($dlt_tbl_question_temp); // $dbGate->ExecuteQuery($dlt_tbl_question_temp,"pnp");  
										
							$pesan = "Error : Soal Kurang dari ".$min_soal;				
						}	
					} else {
						// Delete tabel question_temp_...
						$res_dlt_tbl_question_temp = mysql_query($dlt_tbl_question_temp); // $dbGate->ExecuteQuery($dlt_tbl_question_temp,"pnp");  
						
						$pesan = "Error : Insert Question Temp ".mysql_error();
					}
				} else {
					$pesan = "Error : Create Tabel ".mysql_error();
				}
			} else {
				$pesan = "Error : Open File";
			}
		} else {
			$pesan = "Error : File sudah ada";
		}
	} else {
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
