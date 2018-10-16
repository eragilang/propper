<?php
	session_start();
	
	if($_SESSION['uSeRiD'] == ""){
    	$homelink = "index.php";
    }
   	else{
    	$homelink = "main.php";
    }
    
    require_once ("class/User.php");
   	require_once ("class/DBgateway.php");
    require_once ("class/ConfigReader.php");
    require_once ("class/Logger.php");
    
    $conf = new ConfigReader();
    
    try{
    	
    	$dbGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
	    $dbGate->OpenConnection();
	        
	    $hak = $_SESSION['aCCeSSRiGHT'];
	    	
    	$query = "SELECT * 
						FROM 
							pnp.page_access_right
						WHERE
						 	file_name = 'result.php' AND
					 		access_right = '$hak' AND
					 		enable = '1' ";
				
				$accessres = $dbGate->ExecuteQuery($query,"pnp"); 
			    
			    if (mysql_num_rows($accessres) <= 0){
			    	session_destroy();
			        echo "<script language=\"javascript\">location.href=\"index.php?err=75\"</script>";
			    }
			    
    		$period = date('Ym');
			    
    		$sqlcheck = "SELECT 
		 				nb_done, 
		 				score,
						 date_format(end_assess,'%d/%m/%Y %H:%i:%S') as fdate
		 			FROM 
					 	pnp.score 
		 			WHERE 
					 	employee_id = '".$_SESSION["uSeRiD"]."' and 
						 period = $period and 
					assess_id = '".$_SESSION["aSSeSSiD"]."'";

        	$checkres = $dbGate->ExecuteQuery($sqlcheck,"pnp");
        	$arrOfCheck = mysql_fetch_array($checkres);
    }
    catch (Exception $e){
    	echo $e;
    }	
  	 		
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en"><head> 
	<title>:: Product Knowledge and Procedure</title>
  	<link href="style/global.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/home.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/contact.css" rel="stylesheet" type="text/css" media="all"/>
  	<!--[if lt IE 7.]>
    	<script defer type='text/javascript' src='js/fixpng.js''></script>
	<![endif]-->
</head>
<body>
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
  						<span>Assessor : </span><br />
  						<?php echo $_SESSION["uSeRNaMe"];?> <br />
  						[<?php echo $_SESSION["uSeRiD"];?>]
		  			</div>
<!-- 					 <div id="userscores">
  						<span>Score : </span><br />
  						<?php #if(($hak != 'B5TQ') && ($hak != "FBIQ") && ($hak != "H4JQ") && ($hak != "HAJQ") && ($hak != "BCRQ") && ($hak != "PDRQ") && ($hak != "FOSQ") && ($hak != "SBOQ") && ($hak != "BTLQ")&& ($hak != "JKTQ")){if($arrOfCheck[1]==""){echo 0;}else{echo $arrOfCheck[1];}}?> <br />
  						[<?php #if($arrOfCheck[0]==""){echo 0;}else{echo $arrOfCheck[0];}?> soal]
		  			</div>	 --> 
				</div>
	  			<br class="clear"/>
	  			<fieldset style="width:600px;font-family:garamond;font-size:15px">
        <legend>Result </legend>
        <?php
		if (($hak != "B5TQ") && ($hak != "FBIQ") && ($hak != "H4JQ") && ($hak != "HAJQ") && ($hak != "BCRQ") && ($hak != "PDRQ") && ($hak != "FOSQ") && ($hak != "CPRQ") && ($hak != "SBOQ") && ($hak != "BTLQ")&& ($hak != "JKTQ")) {
        	if ($_SESSION["assessStatus"] == "done"){
        	echo "<p style='margin: 8px 0pt 6px 10px; font-size: 21px;'>Terimakasih, <br class='clear'/> 
				Anda telah menyelesaikan ProPPer Assesment ini. Pengumuman hasil ujian dapat dilihat di Telkomsel Care Community http://icareforum.com/ paling lambat 1 Minggu setelah ujian selesai</p> ";;
        ?>
		<?php
			}
			else if ($_SESSION["assessStatus"] == "end"){
				echo "<p style='margin: 8px 0pt 6px 10px; font-size: 21px;'>Terimakasih, <br class='clear'/> 
				Anda telah menyelesaikan ProPPer Assesment ini. Pengumuman hasil ujian dapat dilihat di Telkomsel Care Community http://icareforum.com/ paling lambat 1 Minggu setelah ujian selesai</p> ";
			}
}
else{
 echo "<p style='margin: 8px 0pt 6px 10px; font-size: 21px;'>Terimakasih, <br class='clear'/>
                                Anda telah menyelesaikan ProPPer Assesment ini. Pengumuman hasil ujian dapat dilihat di Telkomsel Care Community http://telkomselcare.vanillacommunity.com/ paling lambat 1 Minggu setelah ujian selesai. </p> ";	
}
		?>
		 
      </fieldset>	
			</div><!--left column-->

			<div id="right_column"><!--right column-->
  				<div class="section"><!--latest news-->
    				<br style="margin-top:30px;"/>
    				<p class="highlight">Administrator</p>
   					<div class="pa">
						<ul>
    						<li><a href="mailto:fajar.siddiq@ymail.com">Fajar Siddiq</a> </li>
    						<li><a href="mailto:acoel.aiya@yahoo.co.id"> Nasrulaah</a></li>
						</ul>
					</div>
  			</div><!--latest news-->

  			
		</div><!--right column-->
		<div class="clear">&nbsp;</div>
	</div><!--content-->

	<div id="footer"><!--footer-->
  		<p id="copyright">Copyright © 2009 Developed by Tools and Data Management Departement</p>
	</div><!--footer-->

</div><!--page container-->

</body></html>
