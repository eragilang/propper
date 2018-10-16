<?php
	session_start();
	
	if($_SESSION['uSeRiD'] == ""){
    	$homelink = "index.php";
    }
   	else{
    	$homelink = "main.php";
    }
    
    $getid = $_REQUEST["idrand"];
    $trueid = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$_REQUEST["rnum"].$_SESSION["prevpage"]),0,10));
    
    require_once ("class/User.php");
   	require_once ("class/DBgateway.php");
    require_once ("class/ConfigReader.php");
    require_once ("class/Logger.php");
    
    $conf = new ConfigReader();
    
    try{
    	
    	$dbGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
	    $dbGate->OpenConnection();
	        
	    $hak = $_SESSION['aCCeSSRiGHT'];
	    	
    	if ($trueid != $getid){
    	
	        $ipaddress = "";
	        
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
	            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	        }
	        else{
	            $ipaddress = $_SERVER['REMOTE_ADDR'];
	        }
	
	        $logger = new Logger();
	        $logger->setDBConnection($dbGate);
	        $logger->Logging(HistoryType::HISTORYTYPE_PAGE_ACCESS_VIOLATION,$_SESSION["uSeRiD"],"Page Access Violation : (assessment_pre_start.php) ($trueid) ($getid) (".$_SESSION["prevpage"].")",$ipaddress);
        
        	echo "<script language=\"javascript\">alert(\"Page Access Violation. Silahkan kembali ke halaman utama dan klik tombol mulai Assessment\");self.close();</script>";
        	return;
	    }
	    else{
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
			    
    		$period = date('Ym');
			    
    		$sqlcheck = "SELECT 
		 				nb_done, 
						 if(nb_done = nb_question, 1, 0) as flag, 
						 score 
		 			FROM 
					 	pnp.cc_score 
		 			WHERE 
					 	employee_id = '".$_SESSION["uSeRiD"]."' and 
						 period = $period and 
					assess_id = '".$_SESSION["aSSeSSiD"]."'";

        	$checkres = $dbGate->ExecuteQuery($sqlcheck,"pnp");
        	$arrOfCheck = mysql_fetch_array($checkres);
        	
        	echo $arrOfCheck[2];
	  	}
    }
    catch (Exception $e){
    	
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

		<div id="page_content_assess"><!--content-->
			<div id="left_column"><!--left column-->
				<div id="sectiond">
  					<div id="userinfos">
  						<span>Welcome : </span><br />
  						<?php echo $_SESSION["uSeRNaMe"];?> <br />
  						[<?php echo $_SESSION["uSeRiD"];?>]
		  			</div>
					
				</div>
	  			<br class="clear"/>
	  			<fieldset style="width:600px;font-family:garamond;font-size:15px">
        <legend>Petunjuk </legend>
        <ul>
        <li><strong style="font-size:15px;">Silahkan pilih satu jawaban benar dari pilihan jawaban yang tersedia.</strong>
        <br /><img src="image/radio_button.jpg" width="300" height="200" />
        </li>
      </ul>
      </fieldset>	
			</div><!--left column-->

			<div id="right_column"><!--right column-->
  				<div class="section"><!--latest news-->
  					<p class="highlight">Assessment</p>
  					<?php
  					if ($_SESSION["assessStatus"] != "done"){
  						
  						if($hak == 'H4JQ'){
  							?>
  					<form name='frmStart' id='frmStart' action="assess_start.php" method="post" >
  <p>Klik tombol "Mulai Assessment" untuk memulai assessment atau melanjutkan assessment sebelumnya.</p>
  <input type="submit" name='btnSubmit' id='btnSubmit' value = "" class='submits' alt='Start Assessment' onclick='return confirm("Apakah anda yakin untuk melakukan Test Seleksi Haji 2016 ?")'/>
  </form>
  					<?php
  						}
  						else{
  					
  					?>
  					<form name='frmStart' id='frmStart' action="assess_start.php" method="post" >
  <p>Klik tombol "Mulai Assessment" untuk memulai assessment atau melanjutkan assessment sebelumnya.</p>
  <input type="submit" name='btnSubmit' id='btnSubmit' value = "" class='submits' alt='Start Assessment' onclick='return confirm("Apakah anda yakin untuk melakukan PNP Assessment ?")'/>
  </form>
  					<?php
  					}
		}
		else{
			$_SESSION["assessStatus"] = "done";
        	echo "<p>Anda telah menyelesaikan PnP Assessment ini. Terima Kasih</p>";
		}
  					?>
    				
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
