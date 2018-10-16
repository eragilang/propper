
<?php
	session_start();
	if($_SESSION['uSeRiD'] == ""){
    	$homelink = "index.php";
    }
   	else{
    	$homelink = "main.php";
    }
    
	include('config.php');
	function initCounter() { 
		/* $ip = $_SERVER['REMOTE_ADDR']; // menangkap ip pengunjung
		$location = $_SERVER['PHP_SELF']; // menangkap server path */

    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
	  
    }
	//$location = $_SERVER['PHP_SELF']; // menangkap server path
    //return $ip;

		//membuat log dalam tabel database 'counter'
		$create_log = mysql_query("INSERT INTO counter_user_pnp(location,username,ip,tanggal)VALUES('$location','".$_SESSION['uSeRiD']."','$ip',now())");
		}
		initCounter();
		
    try{
    	require_once ("class/User.php");
    	require_once ("class/DBgateway.php");
    	require_once ("class/ConfigReader.php");
    	
    	$conf = new ConfigReader();
    	
    	$dbGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
        $dbGate->OpenConnection();
		
		
     	$csdmGate = new DBGateway($conf->Host251,$conf->User251, $conf->Pass251);
        $csdmGate->OpenConnection();
    	  	
    	/* $gatesms = new DBGateway($conf->Host192,$conf->User192, $conf->Pass192);
        $gatesms->OpenConnection(); */
    
   		
    	
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
	        echo "<script language=\"javascript\">location.href=\"index.php?err=75\"</script>";
	    }
	    
	    $transid = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".date("d/m/Y")),0,10));
	
	    /*Set Parameter*/
	    
	    $setid = 0;
	    $nbmaxsoal = 0;
	    
	    $query = "SELECT
					max_question,
					set_id
				FROM
					pnp.dev_assess_param
				WHERE
					unit_type = '".$_SESSION['CHaNNeLTYPe']."' AND
					level = '".$_SESSION['LeVeL']."'";
	
		$paramres = $dbGate->ExecuteQuery($query,"pnp");
		$arrOfParam = mysql_fetch_array($paramres);
	    
	    $setid = $arrOfParam[1];
	    $nbmaxsoal = $arrOfParam[0];

		//echo $query;
		
	    
	    if (($setid != 0) && ($nbmaxsoal != 0)){
	    	
	    	$infosql =  "SELECT 
							nb_done, 
							score, 
							date_format(end_assess,'%d/%m/%Y %H:%i:%S') as fdate, 
							s.lock 
						FROM 
							pnp.dev_score s 
						WHERE 
							employee_id = '".$_SESSION["uSeRiD"]."' and 
							period = date_format(now(),'%Y%m') and 
							assess_id = '".$_SESSION["aSSeSSiD"]."' ";
		
			$infores = $dbGate->ExecuteQuery($infosql,"pnp");
  	 		$arrOfInfo = mysql_fetch_array($infores);
  	 		
  	 		if ($arrOfInfo[0] != $nbmaxsoal){
        /*Hide button start assessment jika periode assessment sudah berakhir*/
        		$periodesql =   "SELECT 
					   				if(day(now()) between start_assess and end_assess, 
									   	if(time_limit=0,1,if(time_to_sec(now()) between time_to_sec(start_time) - time_zone*3600 and time_to_sec(end_time) - time_zone*3600,1,0)), 0) as flag  
							   	FROM 
								   	pnp.dev_assess_period 
							   	WHERE 
								   	unit_id = ".$_SESSION["uNiTiD"]." and 
								    level = ".$_SESSION["LeVeL"]." and 
									unit_type = '".$_SESSION["CHaNNeLTYPe"]."'";
         
         		$perioderes = $dbGate->ExecuteQuery($periodesql,"pnp");
        		$flag = mysql_fetch_array($perioderes);

        		$rnum = rand(111, 99999999);
        		$idrand = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$rnum."main.php"),0,10));
        		$_SESSION["prevpage"] = "main.php";
        		$_SESSION["rnum"] = $rnum;
			}
//echo $periodesql;
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
  						<span>Welcome : <?php echo $_SESSION["aSSeSSTYPe"];?></span><br />
  						<?php echo $_SESSION["uSeRNaMe"]; ?> <br />
  						[<?php echo $_SESSION["uSeRiD"];?>]
		  			</div>
<!-- 					 <div id="userscores">
  						<span>Score : </span><br />
  						<?php #if(($hak != 'B5TQ') && ($hak != "FBIQ") && ($hak != "H4JQ") && ($hak != "HAJQ") && ($hak != "BCRQ") && ($hak != "PDRQ") && ($hak != "FOSQ") && ($hak != "SBOQ") && ($hak != "BTLQ")&& ($hak != "JKTQ")){if($arrOfCheck[1]==""){echo 0;}else{echo $arrOfCheck[1];}}?> <br />
  						[<?php #if($arrOfCheck[0]==""){echo 0;}else{echo $arrOfCheck[0];}?> soal]
		  			</div>	 --> 	

					
					<?php
						
						$ckassupload = "SELECT * ".
   			                 			 "FROM ".
		                    				 "pnp.assess_upload_soal ".
                    						 "WHERE ".
 								 "login_id = '".$_SESSION["uSeRiD"]."' AND ".
								 "assess = 1";
						
						$resckassupload = $dbGate->ExecuteQuery($ckassupload,"pnp");
						
						if (mysql_num_rows($resckassupload) > 0 ){
							$rowckassupload = mysql_fetch_row($resckassupload);
							
							$_SESSION['unitTypeSoal'] = $rowckassupload[2];
							$_SESSION['unitNameSoal'] = $rowckassupload[3];
							$_SESSION['unitSetIdSoal'] = $rowckassupload[4];
							$_SESSION['unitRegSoal'] = $rowckassupload[5];
							$_SESSION['AssessIdSoal'] = $rowckassupload[7];
										
					?>
					
					<div id="uploadsoal">
						
						<form name='formUploadSoal' id='formUploadSoal' action="upload_create_popup.php" method="post" >
  							<input type="submit" name='btnUploadSoal' id='btnUploadSoal' value = "" class='uploadsoal' alt='Upload Soal'/>
  						</form>
						
					</div>	
					<?php
						} else {
							
							// start
							$chk_emp_code = "SELECT COUNT(*), employee_code FROM csdm2.t_user WHERE login_id = '".$_SESSION["uSeRiD"]."'
																GROUP BY login_id;";
							
							$res_chk_emp_code = $csdmGate->ExecuteQuery($chk_emp_code, "csdm2");
							$row_chk_emp_code = mysql_fetch_row($res_chk_emp_code);
							
							if($row_chk_emp_code[0] == 1) {
								$_SESSION['empCodeSms'] = $row_chk_emp_code[1]; 
							} else {
								$_SESSION['empCodeSms'] = $_SESSION["uSeRiD"];							
							}
						// end
							
							$chk_period_sms = "SELECT IF(DATE_FORMAT(NOW(), \"%Y-%m-%d\") BETWEEN start_date_manual AND end_date_manual, 1, 0)
							FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1;";
							#echo $chk_period_sms;
							//$res_chk_period_sms = $gatesms->ExecuteQuery($chk_period_sms ,"sms");
							//$row_chk_period_sms = mysql_fetch_row($res_chk_period_sms);
							$row_chk_period_sms[0] = 0;
							
						if($row_chk_period_sms[0] == 1) { // benar = 1 81148  006017
				//			if(($row_chk_period_sms[0] == 1) && (	$_SESSION['empCodeSms'] == '72328')) {
							
								$chc_soal = "SELECT COUNT(*) FROM quiz_trans 
															WHERE 
															employee_id = '".$_SESSION['empCodeSms']."' AND 
															quiz_seq = (SELECT quiz_seq FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1) AND
															employee_answer IS NULL AND
															period = (SELECT period FROM quiz_period ORDER BY period DESC, quiz_seq DESC LIMIT 1);";		
							#echo $chc_soal;	
								$res_chc_soal = $gatesms->ExecuteQuery($chc_soal, "sms");
								$row_chc_soal = mysql_fetch_row($res_chc_soal);
								
								if($row_chc_soal[0] > 0) {
							
							?>
							
										
							<div id="requestsoal">
						
								<form name='formRequestSoal' id='formRequestSoal' action="sms_create_popup.php" method="post" >
  								<input type="submit" name='btnRequestSoal' id='btnRequestSoal' value = "" class='requestsoal' alt='Request Soal'/>
  							</form>
						
							</div>	
						
							
							<?php
								}
							} else {
								
						//		<p>Anda tidak bisa melakukan assessment, karena periode assessment belum dimulai atau periode assessment sudah berakhir.</p>
							
							}
						}
					?>


				</div>
	  			<br class="clear"/>
				<fieldset style="width:600px;font-family:garamond;font-size:15px">
        <legend>Cara Melakukan PROPPER Assessment</legend>
        <ul style="list-style-type:circle;">
	     <li><strong style='color:red;font-size:12px;text-decoration:underline;'>Rule:</strong>Anda cukup mensubmit jawaban sebanyak 1 (satu) kali. Jika anda telah mensubmit jawaban, tapi halaman assessment belum berubah, maka - mungkin - koneksi ke server sedang lambat. </li>
	     <li><strong style='color:red;font-size:12px;text-decoration:underline;'>Tips:</strong>Jika anda kesulitan untuk memilih pilihan jawaban, maka cukup klik text jawaban, maka pilihan yang bersesuaian akan otomatis terpilih</li>
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
				<?php
					if($_SESSION['uSeRiD'] == 'upload_soal'){
					
				?>
				<form action="generate_soal.php" method="post" >
				<table border="0" cellpadding="7" cellspacing="0">
					<tr>
						<td>Category Soal
						  <select name="unittype" id="unittype">
							<option value="GRAPARI" selected>Walk-In</option>
							<option value="CALLCENTER">Callcenter</option>
							<option value="WEBSERVICE">eCare</option>
						  </select>
						</td>
						</tr>
					<tr>
						<td><input name="tahun" type="text" value="<?php echo date('Y'); ?>" size="4">Tahun
						  <select name="bulan" id="bulan">
							<option value="01" selected>January</option>
							<option value="02">Februari</option>
							<option value="03">Maret</option>
							<option value="04">April</option>
							<option value="05">Mei</option>
							<option value="06">Juni</option>
							<option value="07">Juli</option>
							<option value="08">Agustus</option>
							<option value="09">September</option>
							<option value="10">Oktober</option>
							<option value="11">November</option>
							<option value="12">Desember</option>
						  </select>Bulan
						</td>
						</tr>
						<tr>
							<td>
							<select name="start_assess" id="start_assess">
								<option value="1" selected>1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
								<option value="16">16</option>
								<option value="17">17</option>
								<option value="18">18</option>
								<option value="19">19</option>
								<option value="20">20</option>
								<option value="21">21</option>
								<option value="22">22</option>
								<option value="23">23</option>
								<option value="24">24</option>
								<option value="25">25</option>
								<option value="26">26</option>
								<option value="27">27</option>
								<option value="28">28</option>
								<option value="29">29</option>
								<option value="30">30</option>
								<option value="31">31</option>
						  </select>Start Assess
							</td>
						</tr>
						<tr>
							<td>
						  <select name="end_assess" id="end_assess">
								<option value="1" selected>1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
								<option value="16">16</option>
								<option value="17">17</option>
								<option value="18">18</option>
								<option value="19">19</option>
								<option value="20">20</option>
								<option value="21">21</option>
								<option value="22">22</option>
								<option value="23">23</option>
								<option value="24">24</option>
								<option value="25">25</option>
								<option value="26">26</option>
								<option value="27">27</option>
								<option value="28">28</option>
								<option value="29">29</option>
								<option value="30">30</option>
								<option value="31">31</option>
						  </select>End Assess
							</td>
						</tr>
				</table>
				
					<input style="background-color:silver; color: black; width:150px; height: 30px; font-size:15px;" type="submit" name='btnGenerateSoal' id='btnGenerateSoal' value = "Generate Soal"/>
				</form>
				<?php
					}
				?>
  					<p class="highlight">Assessment</p>
  					<?php
  					if ($arrOfInfo[0] != $nbmaxsoal){
  						if ($flag[0] == 1){
  							if ($arrOfInfo[3] == 0){
					//print_r($_SESSION);	
  					?>
  					<form name='frmStart' id='frmStart' action="assess_create_popup.php" method="post" >
  <p>Klik tombol "Mulai Assessment" untuk memulai assessment atau melanjutkan assessment sebelumnya.</p>
  <input type="submit" name='btnSubmit' id='btnSubmit' value = "" class='submits' alt='Start Assessment'/>
  </form>
  
  					<?php
  						
  						
		  					}
		  					else{
  					?>
					<p>Anda sementara tidak bisa melakukan assessment. Account anda sudah di lock karena menghentikan waktu assessment. Hubungi Admin </p>
  					<?php
	  						}
						}
						else{
							if(($hak != 'B5TQ') && ($hak != "FBIQ") && ($hak != "H4JQ") && ($hak != "HAJQ") && ($hak != "BCRQ") && ($hak != "PDRQ") && ($hak != "FOSQ") && ($hak != "CPRQ") && ($hak != "SBOQ") && ($hak != "BTLQ") && ($hak != "JKTQ") && ($hak != "KPKQ")){
						//print_r($_SESSION);
					?>
						<p>Anda tidak bisa melakukan assessment, karena periode assessment belum dimulai atau periode assessment sudah berakhir <b>(Silahkan konfirmasi dengan regional masing - masing).</b></p>
						
					<?php
							}
							else{
								?>
						<p>Anda tidak bisa melakukan assessment, karena periode assessment belum dimulai atau periode assessment sudah berakhir</p>
					<?php
							}
						
						}
					}
					else{
						$_SESSION["assessStatus"] = "done";
			        	echo "<p>Anda telah menyelesaikan PnP Assessment ini. Terima Kasih</p>";
					}
  					?>
    				
    				<br style="margin-top:30px;"/>
    				<p class="highlight">Troubleshooting:</p>
   					<div class="pa">
						<ul>
    						<li>Jika tombol assessment sudah diklik, tapi halaman assessment tidak tampil (selalu kembali ke halaman ini), maka pastikan bahwa pop up blocker sudah didisable. Caranya (jika browser adalah Internet Explorer) : (1) Pilih menu tools > pop up blocker > turn off pop up blocker (2) tutup semua window kemudian login kembali </li>
						</ul>
					</div>
					<br style="margin-top:30px;"/>
    				<p class="highlight">Administrator</p>
   					<div class="pa">
						<ul>
    						<li><a href="http://10.2.130.251/helpdesk/">HelpDesk CCS</a> </li>
						</ul>
					</div>
  			</div><!--latest news-->

  			
		</div><!--right column-->
		<div class="clear">&nbsp;</div>
	</div><!--content-->

	<div id="footer"><!--footer-->
  		<p id="copyright">Copyright © 2009 Developed by Tools and Data Management Departement</p>
	</div><!--footer-->

</body></html>

<?php
		}
	    else{
			echo "<p style='font-family:Garamond;color:red;padding:5px;border:1px solid red;'>PnP configuration error, contact your administrator!</p>";
		}
	}
	catch (Exception $e){
		echo "<p style='font-family:Garamond;color:red;padding:5px;border:1px solid red;'>".$e->getMessage()."</p>";
	}
?>
