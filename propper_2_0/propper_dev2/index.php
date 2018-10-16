<?php
	session_start();
	$uSeRiD = '';
	if(isset($_SESSION["uSeRiD"])){$uSeRiD = $_SESSION["uSeRiD"];}
	
	if ($uSeRiD != ""){
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

		<div id="page_content"><!--content-->
			<div id="left_column"><!--left column-->
				<div id="sections">
  					<div id="message">
			    			<form method="post" action="login.php">
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
								$err = '';
								if(isset($_REQUEST["err"])){$err = $_REQUEST["err"];}
								
 								if ($err != ""){
 									
								echo "<p class='error'>Pesan Kesalahan Login</p>";
 								echo "<p class='errormini'>";	
								switch ($err){
	                                case "462":
	                                    echo "Login gagal. Password salah <a href='index.php'> Login Ulang</a>";
	                                    break;
	                                case "461":
	                                    echo "Login gagal. User dilock karena gagal login 5 kali. 
										Hubungi Admin! <a href='index.php'> Login Ulang</a>";
	                                    break;
	                                case "463":
	                                    echo "Login gagal. Kontrak anda sudah habis. Silahkan 
										Update masa kontrak anda di CSDM! <a href='index.php'> Login Ulang</a>";
	                                    break;
	                                case "464":
	                                    echo "Login Gagal. Masa berlaku password anda sudah melebihi 3 jam.
										 Silahkan SMS ulang ke 1166 untuk mendapatkan password baru <a href='index.php'> Login Ulang</a>";
	                                    break;
	                                case "465":
	                                    echo "Login Gagal. Anda sedang login ke sistem PNP. Hubungi admin. <a href='index.php'> Login Ulang</a>";
	                                    break;
			      					case "466":
	                                    echo "Login gagal. Anda tidak berhak mengikuti PNP. <a href='index.php'> Login Ulang</a>";
	                                    break;
			      					case "467":
	                                    echo "Login gagal. Segmen anda tidak terdaftar. Hubungi admin! <a href='index.php'> Login Ulang</a>";
	                                    break;
			      					case "468":
	                                    echo "Login gagal. Account anda hanya bisa digunakan dari gerai masing-masing!. <a href='index.php'> Login Ulang</a>";
	                                    break;	
	                                case "75":
	                                    echo "You have no right to access that page! <a href='index.php'> Login Ulang</a>";
	                                    break;
	                                case "82":
	                                    echo "Login gagal. Hubungi admin! <a href='index.php'> Login Ulang</a>";
	                                    break;
	                                case "582":
	                                    echo "Anda dilogout otomatis oleh sistem karena anda menghentikan 
										waktu assessment. Hubungi admin karena account anda sudah dilock! <a href='index.php'> Login Ulang</a>";
	                                    break;
									case "999":
	                                    echo "Sesi anda sudah habis. Silahkan login ulang untuk melanjutkan! <a href='index.php'> Login Ulang</a>";
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
	$conn = mysql_connect("10.250.193.238","apps","B4nC1@K35uRuP4nkwoK") or die ("Error");
	mysql_select_db("csdm2");
	
	$submit ='';
	if(isset($_POST['Submit'])){$submit = $_POST['Submit'];}
	
	if($submit){
		
		$nik=TRIM($_POST['nik']);
		
		$update_last_req="UPDATE csdm2.t_appprofile set last_request=now() WHERE login_id='$nik'";
		$update = mysql_query($update_last_req);
		
		if (! $update)
			die("Cannot query: $update");
		
		$teks="[PNP] - User anda : $login dan Password : $password . Batas pengerjaan adalah 3 Jam dari Request User dan Password. Terima Kasih";
	}
?>
 							<p class="highlight">Cara Login</p>
 							<ul>
								<li>1. Masukkan NIK CSDM ke field diatas dan tekan submit </li>
								<li>2. Kemudian masukan username dan password di kolom sebelah kiri </li>
 								<li>3. Pastikan Job Function di CSDM sudah sesuai </li>
								<li>4. Soal yang di tampilkan sesuai dengan Job Function </li>
								<li>5. Jika salah User atau Password 5x maka akan terlock, silahkan hubungi Admin </li>
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
  				<div><!--latest news-->
						<img src="image/clipboard.png" width = '150'>
				</div><!--latest news-->
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
