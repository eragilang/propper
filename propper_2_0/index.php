<?php
	session_start();
	
	if ($_SESSION["uSeRiD"] != ""){
		//echo "<script type=\"text/javascript\">location.href=\"main.php\"</script>";
		//return;
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en"><head> 
	<title>:: PROPPER</title>
  	<link href="style/global.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/home.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/contact.css" rel="stylesheet" type="text/css" media="all"/>
  	<script type="text/javascript" src="js/jquery-1.3.2.min.js"> </script>
  	<!--[if lt IE 7.]>
    	<script defer type='text/javascript' src='js/fixpng.js''></script>
	<![endif]-->
  	<script type="text/javascript">
  		jQuery(function($) {
    		$('#top20agent').load('top20agent.php');
    		$('#top10frontliner').load('top10frontliner.php');
    		/*$('#top10unit').load('top10unit.php');*/
		}); 
		
		$('#top20agent').ajaxSend(function(r,s){  
			$("#contentLoading1").show();  
		});
		  
		$('#top20agent').ajaxStop(function(r,s){  
			$("#contentLoading1").fadeOut("fast");   
		});
		
		$('#top10frontliner').ajaxSend(function(r,s){  
			$("#contentLoading2").show();  
		});
		  
		$('#top10frontliner').ajaxStop(function(r,s){  
			$("#contentLoading2").fadeOut("fast");   
		}); 
		
		//setInterval( "updateTopInfo()", 10000 );
 	</script>
</head>
<body>
	<div id="page_container"><!--page container-->
		<div id="header"><!--header-->
  			<div id="logo">
			  <h1 title="PnP Home"><a href="index.php">PnP Assessment</a></h1></div>
  			
		</div><!--header-->

		<ul id="navigation">
  			<!--<li>
			    
		  		<a href="report_most_wrong_callcenter.php" id="callcenter" title="Report Call Center">
				  <span>Call Center</span>
			  	</a>
  			</li>
  			<li>
				
			  	<a href="report_per_grapari1.php" id="grapari" title="Report GraPARI">
				  <span>GraPARI</span>
				  </a>
			  </li>
  			<li>
			  
			  <a href="report_per_gerai1.php" id="gerai" title="Report Gerai">
			  	<span>Gerai</span>
			  </a>
  			</li>
  			<li>
			  <a href="report_per_bes1.php" id="plasa" title="Report BES">
			  <span>Contact</span></a></li>-->

		<div id="page_content"><!--content-->
			<div id="left_column"><!--left column-->
				<div id="sections">
  					<div id="message">
					
					<?php
						$IP = $_SERVER['REMOTE_ADDR'];
						$cekIP = substr($IP, 0, 6);
						//echo $cekIP;
						if($cekIP == "172.28")
						{
					?>					
			    			<form method="post" action="login.php">
						<?php }else{  ?><form method="post" action="filter_login.php"> <?php } ?>
						    	<div class="row">
		          					<label for="username">Username</label>
	          						<div class="textinput">
									  <input class="textinput" id="username" name="username" value="" type="text"/>
							  	</div>
	     					   </div>
	     					   <div class="row">
		          					<label for="password">Password</label>
	          						<div class="textinput">
									  <input class="textinput" id="password" name="password" value="" type="password"/>
							  	</div>
	     					   </div>
	     					   <button type="submit" title="Send message"><span>Send message</span></button>
			    			</form>
				  </div>
 						<div id="logininfo">
 							<p>
 							  <?php
 								if ($_REQUEST["err"] != ""){
 									
								echo "<p class='error'>Pesan Kesalahan Login</p>";
 								echo "<p class='errormini'>";	
								switch ($_REQUEST["err"]){
	                                case "462":
	                                    echo "Login gagal. Password salah";
	                                    break;
	                                case "461":
	                                    echo "Login gagal. User dilock karena gagal login 5 kali. 
										Hubungi Admin!";
	                                    break;
	                                case "463":
	                                    echo "Login gagal. Kontrak anda sudah habis. Silahkan 
										Update masa kontrak anda di CSDM!";
	                                    break;
	                                case "464":
	                                    echo "Login Gagal. Masa berlaku password anda sudah melebihi 3 jam.
										 Silahkan SMS ulang ke 1166 untuk mendapatkan password baru";
	                                    break;
	                                case "465":
	                                    echo "Login Gagal. Anda sedang login ke sistem PNP. Hubungi admin.";
	                                    break;
			      					case "466":
	                                    echo "Login gagal. Anda tidak berhak mengikuti PNP.";
	                                    break;
			      					case "467":
	                                    echo "Login gagal. Segmen anda tidak terdaftar. Hubungi admin!";
	                                    break;
			      					case "468":
	                                    echo "Login gagal. Account anda hanya bisa digunakan dari gerai masing-masing!.";
	                                    break;	
	                                case "75":
	                                    echo "You have no right to access that page!";
	                                    break;
	                                case "82":
	                                    echo "Login gagal. Hubungi admin!";
	                                    break;
	                                case "582":
	                                    echo "Anda dilogout otomatis oleh sistem karena anda menghentikan 
										waktu assessment. Hubungi admin karena account anda sudah dilock!";
	                                    break;
									case "999":
	                                    echo "Sesi anda sudah habis. Silahkan login ulang untuk melanjutkan!";
	                                    break;
	                            }
	                            echo "</p>";
 								
							}
 							else{
 							?>
						  </p>
 							<p>
 							<form method="post" action="index.php">
 							  NIK Employee : 
 							    <input type="text" name="nik" />
 							    <input type="submit" name="Submit" value="Submit" />
 							</p>
 																					<?php
									
								  //$conn = mysql_connect("10.250.193.238","apps","Jumat#Kl1w0n") or die ("Error");
									//edited by thomi
								  $conn = mysql_connect("10.250.193.238","apps","B4nC1@K35uRuP4nkwoK") or die ("Error");                                                                                                                                                                                    
									mysql_select_db("csdm2");

									
									if($_POST['Submit']){
								
									 $nik=TRIM($_POST['nik']);
									 
									 $update_last_req="UPDATE csdm2.t_appprofile set last_request=now() WHERE login_id='$nik'";
									 #echo "$update_last_req <br>";
   								 $update = mysql_query($update_last_req);                                                                                                                                                                                                                
		  						 if (! $update)                                                                                                                                                                                                                             
									 die("Cannot query: $update");   
									 
					function filter($string){
						//$string = htmlentities($string, ENT_NOQUOTES, 'UTF-8');
						$string = htmlspecialchars($string);
						$string = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '-', $string);
						$string = mysql_escape_string($string);
						return $string;
					}					
				 	
                   $filter_nik = filter($nik);
                   $cek_msisdn="SELECT 
								  b.login_id,
								  b.password,
								  a.phone,
								  a.employee_code 
								FROM
								  t_employee a,
								  t_appprofile b 
								WHERE a.employee_code = b.employee_code 
								  AND a.employee_flag = '1' 
								  AND a.employee_code = '$filter_nik' 
								  AND b.app_code = 'PNP'";
												   #echo "$cek_msisdn";
                   
                   $sms_kirim=mysql_query($cek_msisdn,$conn) or die('Could not connect: ' . mysql_error());
                   $row=mysql_fetch_row($sms_kirim);
                   
                   $login=$row[0];
                   $password=$row[1];
                   $msisdn=$row[2];
                   $employee_code=$row[3];
                   
                   #echo "test $login , $password";
                   
                   #echo "<br><b class=style2>SMS telah dikirimkan ke user <i>$login</i></b> <br>";
                   
                   
                   $teks="[PNP] - User anda : $login dan Password : $password . Batas pengerjaan adalah 3 Jam dari Request User dan Password. Terima Kasih";

									 
		               $fp=fopen("http://10.2.230.27:7777/webcc/sms_wartahalo?pmsisdn=$msisdn&pmessagetext=" . rawurlencode($teks),"r");
		               fpassthru($fp);	
                   
                   
                   $insert_data="INSERT INTO t_sms_trans(action_date,login_id,login_action) VALUES(now(),'$employee_code','MSISDN anda : $msisdn')";
									 #echo "$update_last_req <br>";
   								 $insert = mysql_query($insert_data);                                                                                                                                                                                                                
		  						 if (! $insert)                                                                                                                                                                                                                             
									 die("Cannot query: $insert");   
									 
									 
                   
                   
									}
									
									?>
									  
 							
 							
 							<p class="highlight">Cara Login</p>
 							<ul>
 								<li>1. Masukkan NIK CSDM ke field diatas dan tekan submit </li>
								<li>2. Kemudian masukan username dan password di kolom sebelah kiri </li>
 							</ul>
							<div class="section"><!--latest news--><br><br>
								<p class="highlight">Info</p>
								<div class="pa">
									<ul>
									<li><a href="report_most_wrong_callcenter.php">Most Wrong Answer Callcenter</a> </li>
									</ul>
								</div>
							</div><!--latest news-->
 							<?php
 								}
 							?>
 							</form>

 							</div>
				</div>
	  			<br clear="left"/>	

			</div><!--left column-->

			<div id="right_column"><!--right column-->
  				<div class="section"><!--latest news-->
    				<p class="highlight">Administrator</p>
   					<div class="pa">
						<ul>
    						<li><a href="http://10.2.130.251/helpdesk/">HelpDesk CCS</a> </li>
						</ul>
					</div>
  			</div><!--latest news-->

  			<h3> News </h3>
		</div><!--right column-->
		<div class="clear">&nbsp;</div>
	</div><!--content-->

	<div id="footer"><!--footer-->
  		<p id="copyright">Copyright © 2009 Built by Tools and Data Management Departement</p>
	</div><!--footer-->

</div><!--page container-->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script><script src="style/ga.js" type="text/javascript"></script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-181114-3");
pageTracker._trackPageview();
</script>
</body></html>
