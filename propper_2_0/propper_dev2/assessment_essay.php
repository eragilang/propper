<?php
	session_start();
	
	if($_SESSION['uSeRiD'] == ""){
    	$homelink = "index.php";
    }
   	else{
    	$homelink = "main.php";
    }
    
    $hak = $_SESSION['aCCeSSRiGHT'];
    $employeeid = $_SESSION["uSeRiD"];
    
    $trueid = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$_SESSION["transdate"]),0,10));
    $getid = $_REQUEST["id"];
    $transdate = date("d/m/Y");
    $_SESSION["transdate"] = $transdate;

    /*Validate transaction id*/
    if ($trueid != $getid){
        echo "<script language=\"javascript\">location.href=\"main.php\"</script>";
        return;
    }
    
    require_once ("class/User.php");
   	require_once ("class/DBgateway.php");
    require_once ("class/ConfigReader.php");
    require_once ("class/Logger.php");
    
    $conf = new ConfigReader();
    
    try{
    	
    	$dbGate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
	$dbGate->OpenConnection();
	    
	$query = "SELECT * 
		FROM 
			pnp.page_access_right
		WHERE
		 	file_name = 'assessment.php' AND
			access_right = '$hak' AND
			enable = '1' ";
			 		
	$accessres = $dbGate->ExecuteQuery($query,"pnp"); 
	    
	if (mysql_num_rows($accessres) <= 0){
		session_destroy();
	        #echo "<script language=\"javascript\">location.href=\"index.php?err=75\"</script>";
	}
	    
	if($_SESSION["assessStatus"] != "start"){
		if ($_SESSION["assessStatus"] == "end"){
	            echo "<script language=\"javascript\">location.href=\"main.php?err=461001\"</script>";
	        }
	        else{
	            echo "<script language=\"javascript\">location.href=\"result.php\"</script>";
	        }
	        return;
	    }
	    
	    /*Set Parameter*/
	    $setid = 0;
	    $nbmaxsoal = 0;
	    $segment = $_SESSION['LeVeL'];
	    $period = date('Ym');
	    
	    $query = "SELECT
					max_question,
					set_id
				FROM
					pnp.assess_param
				WHERE
					unit_type = '".$_SESSION['CHaNNeLTYPe']."' AND
					level = ".$_SESSION['LeVeL'];
	
		$paramres = $dbGate->ExecuteQuery($query,"pnp");
		$arrOfParam = mysql_fetch_array($paramres);
	    
	    $setid = $arrOfParam[1];
	    $nbmaxsoal = $arrOfParam[0];
	    
	    /*cek apakah masih di periode assessment*/
	    
	    $periodesql =   	"SELECT 
					if(day(now()) between start_assess and end_assess, 
					if(time_limit=0,1,if(time_to_sec(now()) between time_to_sec(start_time) - time_zone*3600 and time_to_sec(end_time) - time_zone*3600,1,0)), 0) as flag  
				FROM 
					pnp.assess_period 
				WHERE 
					unit_id = ".$_SESSION["uNiTiD"]." and 
					unit_type = '".$_SESSION["CHaNNeLTYPe"]."' and 
					level = $segment";

        $perioderes = $dbGate->ExecuteQuery($periodesql,"pnp");
        $flag = mysql_fetch_array($perioderes);

	 if ($flag[0] == 0){
		/*close window*/
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
 			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        	}
        	else{
            		$ipaddress = $_SERVER['REMOTE_ADDR'];
        	}

        	$logger = new Logger();
        	$logger->setDBConnection($dbGate);
        	$logger->Logging(HistoryType::HISTORYTYPE_TIME_END_VIOLATION,$_SESSION["uSeRiD"],"Times Up",$ipaddress);

		echo "<script language=\"javascript\">alert(\"Times Up! Waktu assessment sudah habis, Anda tidak bisa lagi mengakses halaman ini.\");self.close();</script>";
		return;
	 }
	 	
	 /*Cek apakah CSR/Agent sudah pernah mengakses PNP Assessment*/
	 	
	 /*$sqlcheck = 	"SELECT 
				nb_done, 
			 	if(nb_done = nb_question, 1, 0) as flag, 
			 	score 
			FROM 
				 	score 
		 			WHERE 
					 	employee_id = '$employeeid' and 
						 period = $period and 
					assess_id = '".$_SESSION["aSSeSSTYPe"]."'";*/

	$sqlcheck = "SELECT *
		FROM
			pnp.essay_answer
		WHERE
			 employee_id = '$employeeid' and
                         period = $period and
                         assess_id = '".$_SESSION["aSSeSSTYPe"]."'"
		    ;

        $checkres = $dbGate->ExecuteQuery($sqlcheck,"pnp");
	$nbessayans = mysql_num_rows($checkres);

	if ($nbessayans > 0){
	            echo "<script language=\"javascript\">location.href=\"result.php\"</script>";
			return;
	}
        #$arrOfRow = mysql_fetch_array($checkres);
	 		
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en"><head> 
	<title>:: Product Knowledge and Procedure</title>
  	<link href="style/global.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/contact.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/assess.css" rel="stylesheet" type="text/css" media="all"/>
  	<!--[if lt IE 7.]>
    	<script defer type='text/javascript' src='js/fixpng.js''></script>
	<![endif]-->
</head>
<body onload='startCountdown();' oncontextmenu="return false" id="home" >
	<div id="page_container"><!--page container-->
		<div id="header"><!--header-->
  			<div id="logo">
			  <h1 title="PnP Home"><a href="index.php">PnP Assessment</a></h1></div>
  			
		</div><!--header-->

		<div id="page_content_assess"><!--content-->
			<form name='frmQuiz' id='frmQuiz' action="assess_essay_process.php?id=<? echo $trueid; ?>" method="post" onsubmit="document.getElementById('btnSubmit').disabled=true" >
			<div id="left_column"><!--left column-->
				<div id="sectiond">
  					<div id="userinfos">
  						<span>Welcome : </span><br />
  						<?php echo $_SESSION["uSeRNaMe"];?> <br />
  						[<?php echo $_SESSION["uSeRiD"];?>]
		  			</div>
					  <div id="userscores">
  						<span>Score : </span><br />
  						 <br />
  						[]
		  			</div>
					  <div id="result">
					  	<?php
						if (($hak != "B5TQ") && ($hak != "H4JQ") && ($hak != "HAJQ") && ($hak != "BCRQ") && ($hak != "PDRQ")) {
				  		if($_SESSION["NoTe"]=="salah"){
				  			echo "<div id='wrong'><span>Nomor</span><br/>".($_REQUEST["qnum"]-1)."<br>&nbsp;<br>&nbsp;</div>";
				  		} 
				  		else if($_SESSION["NoTe"]=="benar"){
				  			echo "<div id='correct'><span>Nomor</span><br/>".($_REQUEST["qnum"]-1)."<br>&nbsp;<br>&nbsp;</div>";
				  		}
}
					  	?>
					  </div>	
				</div>
	  			<br class="clear"/>
				<?php

				#get question
				$quest = "SELECT question_id, question
					FROM
						pnp.question_essay
					WHERE
						period = $period AND
						level = $segment AND
						set_id = $setid AND
						assess_id = '".$_SESSION["aSSeSSTYPe"]."'
					";

				$questres = $dbGate->ExecuteQuery($quest,"pnp");

					#echo $row[0];
				
				
			            $ipaddress = "";

			            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            			}
            			else{
                			$ipaddress = $_SERVER['REMOTE_ADDR'];
    			        }

		            $logger = new Logger();
		            $logger->setDBConnection($dbGate);
		            $logger->Logging(HistoryType::HISTORYTYPE_GENERATE_QUESTIONS,$employeeid,"DISPLAY ESSAY QUESTION" ,$ipaddress);

                	    $nbseconds = 3000;
                
                ?>
                <script type="text/javascript">
                        var timerDown = new Number();
                        var timerDown = <? echo $nbseconds;?>;
                        var timeremaining;

                        function startCountdown()
                        {
                            timerDown--;
                            document.getElementById("numberCountdown").innerHTML = timerDown;
                            setTimeout("startCountdown()",1000);

                            if(timerDown==0){
                                timerDown = -1;
                                document.forms[0].submit();
                            }
                        }

                        function CheckMyKeys(){
                            var key = event.keyCode;
                            if (key==122 || key==116 || key==117 || key==118 || key==119 || key==120 || key==121){ // F11 and F5 key, do something then cancel
                                return cancel();
                            }
                            // Inline cancel function
                            function cancel(){
                                event.cancelBubble=true;
                                event.keyCode=0;
                                return false
                            }
                        }
                        document.onkeydown = CheckMyKeys
                    </script>
                        <table style="align:left;" id="assessmentpage">
                        		<thead><tr><th colspan="2"><?php echo "<span style='color:red;font-size:18px;'>ESSAY</span>" ?></th></tr></thead>
                            <tbody>
                            <?php
				
				$idx=0;
				while ($row = mysql_fetch_row($questres)){
					$idx++;
					echo "<tr>";
					echo "<td colspan='2'>".$row[1]."<input type='text' style='display:none;' name='questionid".$idx."' id='questionid".$idx."' value='".$row[0]."'/></td>";	
					echo "</tr>";	
					echo "<tr>";
					echo "<td colspan='2'><textarea style='width:100%;' rows='15' name='answer".$idx."' id='answer".$idx."'></textarea></td>";	
					echo "</tr>";

					$sqldetail ="INSERT INTO essay_answer 
                              (employee_id, question_id, period)
                            VALUES 
                               ('$employeeid', 
                                '".$row[0]."', date_format(now(),'%Y%m'))";

                $dbGate->ExecuteTransaction($sqldetail,"pnp");
	
				}
                            ?>
				<tr>
                                    <td colspan='2'>
                                    <button type="submit" class='submit' name='btnSubmit' id='btnSubmit'></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <input type="text" style="display:none;" id="userid" name="userid" value="<? echo $employeeid; ?>" />
                        <input type="text" style="display:none;" id="txttransdate" name="txttransdate" value="<? echo $transdate; ?>" />
			            <input type="text" style="display:none;" id="txtassessmenttype" name="txtassessmenttype" value="<? echo $_SESSION["aSSeSSTYPe"]; ?>" />
			            <input type="text" style="display:none;" id="questionnum" name="questionnum" value="1" />
                        <input type="text" style="display:none;" id="unittype" name="unittype" value="<? echo $_SESSION["CHaNNeLTYPe"];?>" />
                        <input type="text" style="display:none;" id="level" name="level" value="<? echo $_SESSION["LeVeL"];?>" />
                        
                <?
                /*Insert CSR/Agent Transaction to "Score" . Sebagai flag bahwa    */
                /*CSR/Agent sudah pernah melakukan assessment                     */

                /*$sqldetail ="INSERT INTO essay_answer ".
                            "   (employee_id, question_id) ".
                            "VALUES ".
                            "   ('$employeeid', ".
                            "    '".$arrOfQuiz[0]."')";

                $dbGate->ExecuteTransaction($sqldetail,"pnp");

                $sqlcurrent = "SELECT now() ";
                $timeres = $dbGate->ExecuteQuery($sqlcurrent,"pnp");
                $arrOfTime = mysql_fetch_array($timeres);
                $_SESSION["lasttime"] = $arrOfTime[0];

                $logger->Logging(HistoryType::HISTORYTYPE_VIEW_QUESTION,$employeeid,"View Question #1 {".$arrOfQuiz[0]."}",$ipaddress);

                //$_SESSION["lasttime"] = date("Y-m-d H:*/

				?>	
			</div><!--left column-->

			<div id="right_column"><!--right column-->
  				<div class="section"><!--latest news-->
  					<p id="numberCountdown">&nbsp;</p>
					<br style="margin-top:30px;"/>
					<?php
					if ($questionisnotenough == 0){
					?>
    				<div id='option' style="margin-left:10px;">
				<?php
					if(($hak != 'B5TQ') && ($hak != "FBIQ") && ($hak != "H4JQ") && ($hak != "HAJQ") && ($hak != "BCRQ")  && ($hak != "PDRQ")){
				?>
                        <table>
                        	<tbody>
                                <tr style="border-top: dotted 1px #333">
                                <td width='5%'><input type="checkbox" name='iswrongquestion' id='iswrongquestion'/></td>
                                <td width='90%'>Soal ini salah</td>
                            </tr>
                            <tr>
                                <td width='5%'><input type="checkbox" name='isassessended' id='isassessended'/></td>
                                <td width='90%'>Akhiri assessment setelah soal ini</td>
                            </tr>
                            </tbody>
                        </table>
				<?php
					}
				?>
                    </div>
                    <?
                    }
                    ?>
  			</div><!--latest news-->

  			
		</div><!--right column-->
		<div class="clear">&nbsp;</div>
		</form>
	</div><!--content-->

	<div id="footer"><!--footer-->
  		<p id="copyright">Copyright © 2009 Developed by Tools and Data Management Departement</p>
	</div><!--footer-->

</div><!--page container-->

</body></html>

<?php
	}
    catch (Exception $e){
    	echo "<p style='font-size:15px;font-family:Garamond;color:red;padding:5px;border:1px solid red;'>".$e->getMessage()."</p>";	
    }
?>
