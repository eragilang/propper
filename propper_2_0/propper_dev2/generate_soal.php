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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en"><head> 
	<title>:: Product Knowledge and Procedure</title>
  	<link href="style/global.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/home.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/contact.css" rel="stylesheet" type="text/css" media="all"/>
</head>
<body>
	<!--[if lt IE 7.]>
    	<script defer type='text/javascript' src='js/fixpng.js''></script>
	<![endif]-->
	<div id="page_container"><!--page container-->
		<div id="header"><!--header-->
  			<div id="logo">
			  <h1 title="PnP Home"><a href="index.php">PnP Assessment</a></h1></div>
  			
		</div><!--header-->

		<ul id="navigation"><!--navigation-->
  			<li>
		  		<a href="main.php" id="home" title="home">
				  <span>Home</span>
			  	</a>
  			</li>
  			<li>
			  	<a href="report_per_grapari.php" id="report" title="Report">
				  <span>Report</span>
				  </a>
			  </li>
  			<li>
			  <a href="help.php" id="help" title="Help">
			  	<span>Help</span>
			  </a>
  			</li>
  			<li>
			  <a href="logout.php" id="logout" title="Logout">
			  <span>Logout</span></a></li>
		</ul><!--navigation-->

		<div id="page_content"><!--content-->
			<div id="left_column"><!--left column-->
				<img src="image/header.jpg" alt="Product Knowledge and Procedure" style="margin: 0px 0pt 0pt;" height="150" width="600"/>
				<br class="clear"/>
				<div id="sectiond">
  					<div id="userinfos">
  						<span>Welcome : </span><br />
  						<?php echo $_SESSION["uSeRNaMe"]; ?> <br />
  						[<?php echo $_SESSION["uSeRiD"];?>]
		  			</div>
					<div id="uploadsoal">
<?php
	include "connection.php";
	$period = date('Ym');
	
	#UPDATE PERIODE PENGERJAAN
	$periode 		= $_POST["tahun"].$_POST["bulan"];
	$start_assess	= $_POST["start_assess"];
	$end_assess		= $_POST["end_assess"];
	
	$upd_ass_period = "UPDATE dev_cc_assess_period SET start_assess = ".$start_assess.", end_assess = ".$end_assess." WHERE unit_type = 'callcenter'";
	$res_upd_ass_period = mysql_query($upd_ass_period);
	
	$cekJobLevel = "SELECT LEVEL, level_type, TYPE FROM `dev_cc_job_level` GROUP BY 1,2,3 ORDER BY 1";
	$resCekJobLevel = mysql_query($cekJobLevel);
	
	#DELETE QUESTION ACTIVE
	$delSoalActive = "DELETE FROM `dev_cc_question_active`";
	$resDelSoalActive = mysql_query($delSoalActive);
	
	if($resDelSoalActive){
	$jml_row = 0;
		while($rowCekJobLevel = mysql_fetch_array($resCekJobLevel)){
			$level 		= $rowCekJobLevel[0];
			$level_type = $rowCekJobLevel[1];
			$type		= $rowCekJobLevel[2];
			
			$chk_min_soal = "SELECT DISTINCT max_question FROM dev_cc_assess_param WHERE level = '".$level."' AND set_id = '".$set_id."';";
			$res_chk_min_soal = mysql_query($chk_min_soal);
			$row_chk_min_soal = mysql_fetch_array($res_chk_min_soal);
			$min_soal = $row_chk_min_soal[0];
			
			$chk_jml_soal = "SELECT concat('Q',date_format(now(),'%y%m%d'),'-',lpad(floor(rand()*(9999999-1)+1),7,'0')) as id,
			a.* FROM dev_cc_question_temp a WHERE a.LEVEL = '$level_type'";
			$res_chk_jml_soal = mysql_query($chk_jml_soal);					
			$jml_soal = mysql_num_rows($res_chk_jml_soal);
			
			if($jml_soal > $min_soal){
				while($rowInsertQuest = mysql_fetch_array($res_chk_jml_soal)){
					if($level == 9 || $level == 10 || $level == 11 || $level == 12){
						$jenis = 'CALLCENTER';
					}else{
						$jenis = $rowInsertQuest[15];
					}
					$cekCatSoal = "SELECT cat_id FROM `dev_cc_category` WHERE description = '$type' AND group_description = '$jenis'";
					$resCekCatSoal = mysql_query($cekCatSoal);
					$rowCekCatSoal = mysql_fetch_array($resCekCatSoal);
					$catSoal = $rowCekCatSoal[0];
					$jumlahCatSoal = mysql_num_rows($resCekCatSoal);
					
					if($jumlahCatSoal > 0){
						#echo "ADA \n";
						$insertSoalActive = "INSERT INTO dev_cc_question_active VALUES(
						'".$rowInsertQuest[0]."',
						'".$_SESSION['unitSetIdSoal']."',
						'".$rowInsertQuest[1]."',
						'".$rowInsertQuest[2]."',
						'".$rowInsertQuest[3]."',
						'".$rowInsertQuest[4]."',
						'".$rowInsertQuest[5]."',
						'".$rowInsertQuest[6]."',
						'".$rowInsertQuest[7]."',
						'".$rowInsertQuest[8]."',
						'".$rowInsertQuest[9]."',
						'".$rowInsertQuest[10]."',
						'".$rowInsertQuest[12]."',
						'".$catSoal."',
						'".$period."',
						'".$_SESSION['AssessIdSoal']."',
						'1',
						'".$rowInsertQuest[13]."',
						'".$level."',
						'4',
						'2',
						'".$rowInsertQuest[13]."',
						'".$_SESSION['unitRegSoal']."'
						)";
						
						$resInsertSoalActive = mysql_query($insertSoalActive);
						
						$insertSoalHistory = "INSERT INTO dev_cc_question_history VALUES(
						'".$rowInsertQuest[0]."',
						'".$_SESSION['unitSetIdSoal']."',
						'".$rowInsertQuest[1]."',
						'".$rowInsertQuest[2]."',
						'".$rowInsertQuest[3]."',
						'".$rowInsertQuest[4]."',
						'".$rowInsertQuest[5]."',
						'".$rowInsertQuest[6]."',
						'".$rowInsertQuest[7]."',
						'".$rowInsertQuest[8]."',
						'".$rowInsertQuest[9]."',
						'".$rowInsertQuest[10]."',
						'".$rowInsertQuest[12]."',
						'".$catSoal."',
						'".$period."',
						'".$_SESSION['AssessIdSoal']."',
						'1',
						'".$rowInsertQuest[13]."',
						'".$level."',
						'4',
						'2',
						'".$rowInsertQuest[13]."',
						'".$_SESSION['unitRegSoal']."'
						)";
						
						$resInsertSoalHistory = mysql_query($insertSoalHistory);
						
						if($resInsertSoalActive){
							#echo "BERHASIL \n";
							$jml_row = $jml_row + 1;
							$pesan = "Berhasil generate ".$jml_row." soal.";
						}else{
							#echo $insertSoalActive."\n";
							$pesan = "Error : Gagal saat generate soal, CEK SOAL!!!.";
						}
					}else{
						$pesan = "Error : Kategori soal tidak tersedia. : $cekCatSoal";
						#echo $cekCatSoal."\n";
					}
				}
			}else{
				$pesan = "Error : Soal Kurang dari ".$min_soal;
			}
		}
	}else{
		$pesan = "Error : Gagal mengkosongkan soal.";
	}
	// Bersihkan jawaban dari .
	$upd_qs_active = "UPDATE dev_cc_question_active SET 
					solution = replace(solution, '.', ',') 
					WHERE solution LIKE '%.%'";
	//Bersihkan Jawaban dari .			
	$res_upd_qs_active = mysql_query($upd_qs_active);
								
	// Bersihkan question dari <>
	$upd_qs_active_qst = "UPDATE dev_cc_question_active 
						SET 
						question = REPLACE(question ,'<','('), 
						question = REPLACE(question ,'>',')')
						WHERE question LIKE '%<%>%';";
	//Bersihkan question, option_a,... dari <>		
	$res_upd_qs_active_qst = mysql_query($upd_qs_active_qst);
								
	// Bersihkan option_a dari <>
	$upd_qs_active_opta = "UPDATE dev_cc_question_active 
						SET 
						option_a = REPLACE(option_a ,'<','('), 
						option_a = REPLACE(option_a ,'>',')')
						WHERE option_a LIKE '%<%>%';";
	//$dbGate->ExecuteQuery($upd_qs_active_qst,"pnp");   					
	$res_upd_qs_active_opta = mysql_query($upd_qs_active_opta);
								
	// Bersihkan option_b dari <>
	$upd_qs_active_optb = "UPDATE dev_cc_question_active 
						SET 
						option_b = REPLACE(option_b ,'<','('), 
						option_b = REPLACE(option_b ,'>',')')
						WHERE option_b LIKE '%<%>%';";
	//$dbGate->ExecuteQuery($upd_qs_active_opta,"pnp");  					
	$res_upd_qs_active_optb = mysql_query($upd_qs_active_optb);
								
	// Bersihkan option_c dari <>
	$upd_qs_active_optc = "UPDATE dev_cc_question_active 
						SET 
						option_c = REPLACE(option_c ,'<','('), 
						option_c = REPLACE(option_c ,'>',')')
						WHERE option_c LIKE '%<%>%';";
	//$dbGate->ExecuteQuery($upd_qs_active_optb,"pnp");  								
	$res_upd_qs_active_optc = mysql_query($upd_qs_active_optc);
								
	// Bersihkan option_d dari <>
	$upd_qs_active_optd = "UPDATE dev_cc_question_active 
						SET 
						option_d = REPLACE(option_d ,'<','('), 
						option_d = REPLACE(option_d ,'>',')')
						WHERE option_d LIKE '%<%>%';";
	//$dbGate->ExecuteQuery($upd_qs_active_optc,"pnp");  					
	$res_upd_qs_active_optd = mysql_query($upd_qs_active_optd);
								
	// Bersihkan option_e dari <>
	$upd_qs_active_opte = "UPDATE dev_cc_question_active 
						SET 
						option_e = REPLACE(option_e ,'<','('), 
						option_e = REPLACE(option_e ,'>',')')
						WHERE option_e LIKE '%<%>%';";
	//$dbGate->ExecuteQuery($upd_qs_active_optd,"pnp");  					
	$res_upd_qs_active_opte = mysql_query($upd_qs_active_opte);
								
	// Bersihkan option_f dari <>	
	$upd_qs_active_optf = "UPDATE dev_cc_question_active
						SET
						option_f = REPLACE(option_f ,'<','('),
						option_f = REPLACE(option_f ,'>',')')
						WHERE option_f LIKE '%<%>%';";
	//$dbGate->ExecuteQuery($upd_qs_active_opte,"pnp");  					
	$res_upd_qs_active_optf = mysql_query($upd_qs_active_optf);

	$upd_qs_active_optg = "UPDATE dev_cc_question_active
						SET
						option_g = REPLACE(option_g ,'<','('),
						option_g = REPLACE(option_g ,'>',')')
						WHERE option_g LIKE '%<%>%';";
	$res_upd_qs_active_optg = mysql_query($upd_qs_active_optg);
	
	$upd_qs_active_opth = "UPDATE dev_cc_question_active
						SET
						option_h = REPLACE(option_h ,'<','('),
						option_h = REPLACE(option_h ,'>',')')
						WHERE option_h LIKE '%<%>%';";
	$res_upd_qs_active_opth = mysql_query($upd_qs_active_opth);
?>
						PESAN : <?php echo $pesan;?>
					</div>
				</div>
	  			<br class="clear"/>
				<fieldset style="width:600px;font-family:garamond;font-size:15px">
        <legend>Cara Melakukan PROPPER Assessment</legend>
        <ul style="list-style-type:circle;">
	     <li><strong style='color:black;font-size:15px;text-decoration:underline;'>Rule:</strong><br>
		 Anda cukup mensubmit jawaban sebanyak 1 (satu) kali. Jika anda telah mensubmit jawaban, tapi halaman assessment belum berubah, maka - mungkin - <strong style='color:black;font-size:15px;>koneksi ke server sedang lambat.</strong> </li><br>
	     <li><strong style='color:black;font-size:15px;text-decoration:underline;'>Tips:</strong><br>
		 Jika anda kesulitan untuk memilih pilihan jawaban, maka cukup klik text jawaban, maka pilihan yang bersesuaian akan otomatis terpilih</li>
         <li>Assessment hanya dapat dimulai dengan mengklik tombol "<strong>Mulai Assessment</strong>" (di bagian kanan halaman ini)</li>
         <li><strong style='color:red;font-size:15px;'>Jangan pernah menutup halaman assessment</strong> Anda jika soal sudah ditampilkan, karena jawaban Anda untuk pertanyaan tersebut
            akan langsung dianggap salah.
            </li>
            <!--<li>Jika jumlah soal yang dikerjakan
            belum lengkap dan Anda ingin mengakhiri sesi PNP Assessment, maka cukup memilih opsi "<strong>Akhiri Assessment setelah soal ini"</strong>
            yang akan muncul di atas setiap pertanyaan yang diberikan.
            <br class='clear'/>
            <img src="image/opsi.jpg" width="372" height="60" />
            <br class='clear'/>
            Jika jumlah pertanyaan yang diberikan sudah lengkap, aplikasi akan secara otomatis mengakhiri sesi Assessment Anda.
            </li>
            <li>Jika Anda yakin bahwa soal yang diberikan salah atau jawaban yang disediakan tidak ada yang benar, maka Anda bisa memilih opsi
            "<strong>Soal ini salah</strong>", dan Admin akan menindaklanjuti pernyataan Anda. Mohon untuk menggunakan opsi ini dengan bijak.
            </li>-->
        </ul>
      </fieldset>	
			</div><!--left column-->

			<div id="right_column"><!--right column-->
  				<div class="section"><!--latest news-->
				</div><!--latest news-->

  			
			</div><!--right column-->
			<div class="clear">&nbsp;</div>
		</div><!--content-->

		<div id="footer"><!--footer-->
			<p id="copyright">Copyright © 2009 Developed by Tools and Data Management Departement</p>
		</div><!--footer-->
	</div>
</body></html>