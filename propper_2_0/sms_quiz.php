<?php
	session_start();
	
	if ($_SESSION["uSeRiD"] != ""){
		//echo "<script type=\"text/javascript\">location.href=\"main.php\"</script>";
		//return;
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en"><head> 
	<title>:: Product Knowledge and Procedure</title>
  	<link href="style/global.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/home.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/contact.css" rel="stylesheet" type="text/css" media="all"/>
  	<script type="text/javascript" src="js/jquery-1.3.2.min.js"> </script>
  	<!--[if lt IE 7.]>
    	<script defer type='text/javascript' src='js/fixpng.js''></script>
	<![endif]-->
    <style type="text/css">
<!--
.style1 {color: #FF0000}
.style2 {color: #0000FF}
-->
    </style>
</head>
<body>
	<div id="page_container"><!--page container-->
		<div id="header"><!--header-->
  			<div id="logo">
			  <h1 title="PnP Home"><a href="index.php">PnP Assessment</a></h1></div>
  			
		</div><!--header-->

		<ul id="navigation"><!--navigation-->
  			<li>
		  		<a href="report_per_cc.php" id="callcenter" title="Report Call Center">
				  <span>Call Center</span>
			  	</a>
  			</li>
  			<li>
			  	<a href="report_per_grapari.php" id="grapari" title="Report GraPARI">
				  <span>GraPARI</span>
				  </a>
			  </li>
  			<li>
			  <a href="report_per_gerai.php" id="gerai" title="Report Gerai">
			  	<span>Gerai</span>
			  </a>
  			</li>
  			<li>
			  <a href="report_per_plasa.php" id="plasa" title="Report Plasa">
			  <span>Contact</span></a></li>
		</ul><!--navigation-->

		<div id="page_content"><!--content-->
		  <div id="left_column"><!--left column-->
				<div id="sections">
				  <div id="message">
	    			<form method="post" action="login.php">
			    	  <div class="row">
				   <label for="username"></label>
					    	      <a href="http://pnp.telkomsel.co.id/">Back To Home</a> </div>
	     					   </form>
	  						</div>
 						<div id="logininfo">
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
						  
						  					  						  
							<form enctype="multipart/form-data"  action="<?php $_PHP['SELF']; ?>" method="post">
 								<p class="highlight">Create User</p>
 								
 					<table border="0" align="center" cellpadding="7" cellspacing="0"> 
 						<tr>
 							<td>	
 								
 								<table border="0" align="center" cellpadding="7" cellspacing="0"> 
 									<tr>
 										<td>Select User </td>  								
 										<td>
 											
 											<select name="pnpuser">
											
 											<?php 
 												$conn = mysql_connect("10.1.103.203","pnp","pnp123") or die ("Error");                                                                                                                                                                                    
												mysql_select_db("pnp");
												
 												
 												$slc_assess_type = "SELECT assess_id, assess_desc FROM assess_type WHERE ENABLE = '1';";
 												$res_slc_assess_type = mysql_query($slc_assess_type);
 												if ($res_slc_assess_type) {
 														while ($row_slc_assess_type = mysql_fetch_array($res_slc_assess_type)) {
															?>
															<option value='<?php echo $row_slc_assess_type[0]; ?>'> <?php echo $row_slc_assess_type[1]; ?> </option>
															<?php
														}
 												}
 											?>
 											 											
 												</select>
 										</td>
									<tr>
									<tr>	 								
 										<td>Username : </td> 
 							 	 		<td> <input type="text" name="nama" /> </td> 								 									
 								</table>	
 								
 							</td>
 						</tr>
 						<tr>
 							<td> <input type="submit" name="Submit" value="Create" /> </td>										
 						</tr>
 					</table>
 								
 								
 								<?php
									
								  
									
									if($_POST['Submit']){
									 	$nama=TRIM($_POST['nama']);
									 	$userpnp = TRIM($_POST['pnpuser']);
									 	$clean_nama=str_replace(" ", "_", $nama); 
									 	
									 	$username=$userpnp."-".$clean_nama;
									 	
									 	$slc_unitType = "SELECT b.unit_type FROM assess_type a, assess_param b 
																		WHERE a.assess_id = '".$userpnp."' AND b.level = a.level AND set_id = '3';";
									 	
									 	$res_slc_unitType = mysql_query($slc_unitType);
										$jml_unitType = mysql_num_rows($res_slc_unitType);
									 	
									 	if ($jml_unitType == 1) { 										  
							 				$row_slc_unitType = mysql_fetch_array($res_slc_unitType);									 		
									 		$unitType = $row_slc_unitType[0];
									 		
									 		if ($clean_nama == "") {
									 			echo "<b class=style2>User name harus di isi.</b> <br><br>";										
									 		} else {
									 
									 			$create="INSERT INTO pnp.assess_user(user_id,password,user_name,unit_type,assess_id,acc_status) 
									 						VALUES('$username','telkomsel','$nama','$unitType','$userpnp','1')";
									 			#echo "$create";
   								 			$create_user = mysql_query($create);                                                                                                                                                                                                                
		  						 			if (! $create_user)                                                                                                                                                                                                                             
									 			die("Cannot query: $create_user");   
									 		
									 			echo "<b class=style2>User yang telah di daftarkan : <br>User Name : <i>$username</i> <br>Password   : <i>telkomsel</i></b> <br><br>";
																								
											}											
										} else {
												echo "<b class=style2>Error : Duplicate Unit Type</b> <br><br>";																			 		
										} 
									}
									?>
 							
 																  							
 							<p class="highlight">Note :</p>
 							<ul>
 							 <!-- <li class="style1">User yang didaftarkan adalah user yang akan melakukan induction assessment PNP</li> -->
						    <li class="style1">User yang sudah terdaftar tidak dapat mendaftarkan user yang sama </li>
						    <li class="style1">Contoh : User: Andika Arsi P, result : R3G-Andika_Arsi_P </li>
						  </ul>
 							<p class="highlight">
 												
						            </p>

									  </form>
								
								<?php								 
 							}
 							?>	  
							
				  </div>
				</div>
				
				
  			</div><div id="right_column"><div class="section"></div>
	  			  <!--latest news-->

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