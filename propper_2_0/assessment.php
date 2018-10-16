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
    
    $trueid = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$_SESSION["transdate"].$_REQUEST["qnum"]),0,10));		
    $getid = $_REQUEST["id"];
    $transdate = date("d/m/Y");
    $_SESSION["transdate"] = $transdate;

	echo "<br /> $trueid == $getid";
    /*Validate transaction id*/
    if ($trueid != $getid){
        echo "<script language=\"javascript\">location.href=\"main.php?err=100\"</script>";
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
	     echo $query;
	    if (mysql_num_rows($accessres) <= 0){
	    	session_destroy();
	        echo "<script language=\"javascript\">location.href=\"index.php?err=75\"</script>";
	    }

	    $segment = $_SESSION['LeVeL'];
	    $period = date('Ym');

	     /*Set Parameter*/
            $setid = 0;
            $nbmaxsoal = 0;

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
		echo $query;
	    
	    if($_SESSION["assessStatus"] != "start"){
	        if ($_SESSION["assessStatus"] == "end"){
	            echo "<script language=\"javascript\">location.href=\"main.php?err=461001\"</script>";
	        }
	        else{
		    #cek apakah ada pertanyaan essay
		    $isessay = "SELECT * 
				FROM
					pnp.question_essay
				WHERE
					period = $period AND
					level = $segment AND
					set_id = $setid AND
					assess_id = '".$_SESSION["aSSeSSTYPe"]."'
               			";

			// echo $isessay;
		    $essayres = $dbGate->ExecuteQuery($isessay,"pnp");

		    if (mysql_num_rows($essayres) <= 0){
			
	            echo "<script language=\"javascript\">location.href=\"result.php\"</script>";
			}
			else{
    			$essayid = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$_SESSION["transdate"]),0,10));		
	            echo "<script language=\"javascript\">location.href=\"assessment_essay.php?id=$essayid\"</script>";
			$_SESSION["assessStatus"] = "start";
			}
	        }
	        return;
	    }
	    
	    /*cek apakah masih di periode assessment*/
	    
	    $periodesql =   "SELECT 
							if(day(now()) between start_assess and end_assess, 
								if(time_limit=0,1,if(time_to_sec(now()) between time_to_sec(start_time) - time_zone*3600 and time_to_sec(end_time) - time_zone*3600,1,0)), 0) as flag  
						FROM 
							pnp.assess_period 
						WHERE 
							/*unit_id = ".$_SESSION["uNiTiD"]." and */
							unit_type = '".$_SESSION["CHaNNeLTYPe"]."' and 
							level = $segment";
//echo   $periodesql;
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
	 	
	 	$sqlcheck = "SELECT 
		 				nb_done, 
						 if(nb_done = nb_question, 1, 0) as flag, 
						 score 
		 			FROM 
					 	score 
		 			WHERE 
					 	employee_id = '$employeeid' and 
						 period = $period and 
					assess_id = '".$_SESSION["aSSeSSTYPe"]."'";
// echo $sqlcheck;
        $checkres = $dbGate->ExecuteQuery($sqlcheck,"pnp");
		
        $arrOfRow = mysql_fetch_array($checkres);
	 		
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

<script type="text/javascript">
// if (typeof document.onselectstart!="undefined") {
// document.onselectstart=new Function ("return false");
// }
// else{
// document.onmousedown=new Function ("return false");
// document.onmouseup=new Function ("return true");
// }
</script >

<body onload='startCountdown();' oncontextmenu="return false" id="home" >
 
<script>


// var pesen="Tidak diperbolehkan klik kanan oleh admin!";
// ///////////////////////////////////
// function clickIE4(){if (event.button==2){alert(pesen);return false;}}
// function clickNS4(e){if (document.layers||document.getElementById&&!document.all){if (e.which==2||e.which==3){alert(pesen);return false;}}}
// if (document.layers){document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS4;}
// else if (document.all&&!document.getElementById){document.onmousedown=clickIE4;}
// document.oncontextmenu=new Function("alert(pesen);return false")
</script>

	<div id="page_container"><!--page container-->
		<div id="header"><!--header-->
  			<div id="logo">
			  <h1 title="PnP Home"><a href="index.php">PnP Assessment</a></h1></div>
  			
		</div><!--header-->

		<div id="page_content_assess"><!--content-->
			<form name='frmQuiz' id='frmQuiz' action="assess_process.php?id=<? echo $trueid; ?>" method="post" onsubmit="document.getElementById('btnSubmit').disabled=true" >
			<div id="left_column"><!--left column-->
				<div id="sectiond">
  					<div id="userinfos">
  						<span>Welcome : </span><br />
  						<?php echo $_SESSION["uSeRNaMe"];?> <br />
  						[<?php echo $_SESSION["uSeRiD"];?>]
		  			</div>	
				</div>
	  			<br class="clear"/>
				<?php
				
				$iid = $_SESSION["uSeRiD"];
				//echo $iid;
		if(is_numeric($iid)){
			$qunit = "select unit_type from csdm2.t_appprofile where employee_code = $iid limit 0,1";	
		} else {
			$qunit = "select unit_type from csdm2.t_user_xxx where login_id = '$iid' limit 0,1";	
		}
		//echo $qunit;
		$qqu = $dbGate->ExecuteQuery($qunit,"pnp");
		$rrs = mysql_fetch_array($qqu);
		
		$unitType = $rrs[0];
		if($unitType == 'gerai' || $unitType == 'grapari' || $unitType == 'plasa' || $unitType == 'regional')
		{
			$unitType = 'GRAPARI';
		}
		if($unitType == 'CALLCENTER' || $unitType == 'callcenter')
		{
			$unitType = 'CALLCENTER';
		}
		if($unitType == 'WEBSERVICE' || $unitType == 'webservice')
		{
			$unitType = 'WEBSERVICE';
		}
				
		if (mysql_num_rows($checkres) <= 0){
		#echo "set_id:$setid|level:$segment|regional:".$_SESSION['ReGioNiD']."</br>";
            /*Jika CSR/Agent belum melakukan assessment pada bulan ini,*/
            /*maka generate soal yang akan dikerjakan oleh CSR/Agent   */
            /*sesuai dengan distribusi jumlah soal per kategori. */

            /*Get jumlah soal berdasarkan kategori*/
            $sql =  "SELECT 
						cat_group, 
						nb_question 
					FROM 
						pnp.dev_category_distribution
					WHERE 
						set_id = $setid AND 
						LEVEL = $segment";
		#echo $sql;
            $nbsoalres = $dbGate->ExecuteQuery($sql,"pnp");
            $arrOfNBSoal = array();
			
            while($row = mysql_fetch_row($nbsoalres)){
				$lims = $row[1];
				$gjf = explode("_",$row[0]);
				$gjfr = $gjf[0];
				$typegr = $gjf[2];
				//$mulk = "$gjfr_%_"
				//echo $typegr;
				$qcb = "select jenis_soal,bobot_nilai from job_value where unit_type = '$unitType' and group_job_function = '$gjfr'";
				//echo $qcb;
						$sqlbb = $dbGate->ExecuteQuery($qcb,"pnp");
						$lpros = '';
						$lprod = '';
						$lpril = '';
						
						while($rows = mysql_fetch_array($sqlbb)){
							if($rows[0] == 'proses')
							{
								$lpros = ($rows[1] / 100) * $lims;
								//echo "persen $rows[1], $lpros";
							}
							else if($rows[0] == 'produk')
							{
								$lprod = ($rows[1] / 100) * $lims;
							}
							else if($rows[0] == 'perilaku')
							{
								$lpril = ($rows[1] / 100) * $lims;
							}
						}
			#echo "assess_id:".$_SESSION["aSSeSSTYPe"]."|set_id:$setid|level:$segment|cat_group:".$row[0]."|assess_id:".$_SESSION["aSSeSSTYPe"]."|regional:".$_SESSION['ReGioNiD']."|limit:".$row[1];
		  		if($typegr == 1){
				$mulk = $gjfr . "_%_1";
				$quest  =   "INSERT INTO quiz 
				  				SELECT 
								  	'$employeeid', 
									question_id, 
									'".$_SESSION["aSSeSSTYPe"]."', 
									floor(rand()*(9999-1))+1 as num,  
									if(is_rand = 1, floor(rand()*(max_option_idx-min_option_idx+1))+min_option_idx, min_option_idx) as seq 
								FROM
									question_active qa,
									option_sequence_index osi
									WHERE 
									UPPER(TYPE) = 'PRODUK' AND
									osi.nb_option = qa.nb_option
									AND qa.set_id = $setid
									AND qa.level = $segment
									AND qa.assess_id = '".$_SESSION["aSSeSSTYPe"]."'
									ORDER BY qa.is_mandatory DESC
									LIMIT 0, ".$lprod;
								
				$dbGate->ExecuteTransaction($quest, "pnp");
				//echo $type . "\n". $quest;
				//echo "<font color=\"FFFFFF\">$quest</font>";
				}
				else if($typegr == 2){
				$mulk = $gjfr . "_%_2";
								
				$quest  =   "INSERT INTO quiz 
				  				SELECT 
								  	'$employeeid', 
									question_id, 
									'".$_SESSION["aSSeSSTYPe"]."', 
									floor(rand()*(9999-1))+1 as num,  
									if(is_rand = 1, floor(rand()*(max_option_idx-min_option_idx+1))+min_option_idx, min_option_idx) as seq 
								FROM
									question_active qa,
									option_sequence_index osi
									WHERE 
									UPPER(TYPE) = 'PROSES' AND
									osi.nb_option = qa.nb_option
									AND qa.set_id = $setid
									AND qa.level = $segment
									AND qa.assess_id = '".$_SESSION["aSSeSSTYPe"]."'
									ORDER BY qa.is_mandatory DESC 
									LIMIT 0, ".$lpros;
								
				$dbGate->ExecuteTransaction($quest, "pnp");
				//echo $type . "\n". $quest;	
				}
				else if($typegr == 3){
					$mulk = $gjfr . "_%_3";
				
					$quest  =   "INSERT INTO quiz 
				  				SELECT 
								  	'$employeeid', 
									question_id, 
									'".$_SESSION["aSSeSSTYPe"]."', 
									floor(rand()*(9999-1))+1 as num,  
									if(is_rand = 1, floor(rand()*(max_option_idx-min_option_idx+1))+min_option_idx, min_option_idx) as seq 
								FROM
									question_active qa,
									option_sequence_index osi
									WHERE 
									UPPER(TYPE) = 'PERILAKU' AND
									osi.nb_option = qa.nb_option
									AND qa.set_id = $setid
									AND qa.level = $segment
									AND qa.assess_id = '".$_SESSION["aSSeSSTYPe"]."'
									ORDER BY qa.is_mandatory DESC
									LIMIT 0, ".$lpril;
								
				$dbGate->ExecuteTransaction($quest, "pnp");
				//echo $type . "\n". $quest;
				
				}
            }
			echo "your question this $lims, $lprod, $lpros, $lpril";
			//echo $quest;
			
            $ipaddress = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else{
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            }

            $logger = new Logger();
            $logger->setDBConnection($dbGate);
            $logger->Logging(HistoryType::HISTORYTYPE_GENERATE_QUESTIONS,$employeeid,"Generating Questions",$ipaddress);

            /*  Tampilkan soal pertama    */

            $quizsql =  "SELECT 
							qa.question_id, 
							set_id, 
							question, 
							option_a, 
							option_b, 
							option_c, 
							option_d, 
							option_e, 
							solution, 
							difficulty, 
							os.option_seq_id, 
							option_seq, 
							case
				                when option_type = 2
					                then case
						                when nb_option = 5
							                then if(substr(option_seq, 1, 1)=solution,1,if(substr(option_seq, 3, 1)=solution,2,if(substr(option_seq, 5, 1)=solution,3,if(substr(option_seq, 7, 1)=solution,4,5))))
						                when nb_option = 4
							                then if(substr(option_seq, 1, 1)=solution,1,if(substr(option_seq, 3, 1)=solution,2,if(substr(option_seq, 5, 1)=solution,3,4)))
						                when nb_option = 3
							                then if(substr(option_seq, 1, 1)=solution,1,if(substr(option_seq, 3, 1)=solution,2,3))
						                when nb_option = 2
							                then if(substr(option_seq, 1, 1)=solution,1,2)
						                end
				                when option_type = 1
					                then case
						                when nb_option = 5
							                then substr(concat( if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,'),
								                                if(locate(substr(option_seq,7,1),solution)=0,'','4,'),
									                            if(locate(substr(option_seq,9,1),solution)=0,'','5,')),1,
								                        length(concat(if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,'),
								                                if(locate(substr(option_seq,7,1),solution)=0,'','4,'),
									                            if(locate(substr(option_seq,9,1),solution)=0,'','5,')))-1)
						                when nb_option = 4
							                then substr(concat( if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,'),
								                                if(locate(substr(option_seq,7,1),solution)=0,'','4,')),1,
								                        length(concat(if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,'),
								                                if(locate(substr(option_seq,7,1),solution)=0,'','4,')))-1)
						                when nb_option = 3
							                then substr(concat( if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,')),1,
								                        length(concat(if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,')))-1)
						                when nb_option = 2
							                then substr(concat( if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,')),1,
								                        length(concat(if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,')))-1)
						            end
			                    end
			                as ans, 
							nb_option, 
							option_type 
						FROM 
							question_active qa, 
							quiz q, 
							option_sequence os 
						WHERE 
							q.question_id = qa.question_id and 
							employee_id = '$employeeid' and 
							os.option_seq_id = q.option_seq_id 
						ORDER BY 
							sequence 
						LIMIT 0,1";
			//echo "<br><font color=\"FFFFFF\">$quizsql</font>";
            $quizres = $dbGate->ExecuteQuery($quizsql,"pnp");
			//print_r($quizsql);
			//print_r ($quest);
            if (mysql_num_rows($quizres) <= 0){
                /*question untuk CSR/Agent ybs belum di generate atau kurang*/
                echo "<div id='questionerror'>Questions is not enough! Contact Administrator ( 	Bianca Aulia Dica H)</div>";
                $logger->Logging(HistoryType::HISTORYTYPE_INSUFFICIENT_QUESTION,$employeeid,"View Question #1",$ipaddress);
                $questionisnotenough = 1;
            }
            else{
                $arrOfQuiz = mysql_fetch_array($quizres);
                $nbseconds = $arrOfQuiz[9];
                $questionisnotenough = 0;
                
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
                    <?
                        $optionSeq = explode(",",$arrOfQuiz[11]);

                        if ($arrOfQuiz[14] == "2"){
                    ?>
                        <table style="align:left;" id="assessmentpage">
                        		<thead><tr><th colspan="2"><?php echo "<span style='color:red;font-size:18px;'>(1)</span> ".$arrOfQuiz[2] ?></th></tr></thead>
                            <tbody>
                            <?
                                $nboption =  $arrOfQuiz[13];
                                for($jdx = 1; $jdx<= $nboption; $jdx++){
                                    if ($jdx%2 == 1){
                                        $style = "style='background-color:#DDD;'";
                                    }
                                    else{
                                        $style = "";
                                    }
                            ?>
                                <tr>
                                    <td width='1%' <? echo $style;?>><input type="radio" name='rbanswer' id='rbanswer<? echo $jdx;?>' value='<? echo $jdx;?>' style='margin:3px;'/></td>
                                    <td width='99%' <? echo $style;?> onclick='document.frmQuiz.rbanswer[<?echo $jdx-1;?>].checked = true;'><label ><? echo $arrOfQuiz[$optionSeq[$jdx-1]+2]; ?></label></td>
                                </tr>
                            <?
                                }
                            }
                            else{
                            ?>
                        <table style="align:left;" id="assessmentpage">
                        		<thead><tr><th colspan="2"><?php echo "<span style='color:red;font-size:18px;'>(1)</span> ".$arrOfQuiz[2] ?></th></tr></thead>
                            <tbody>
                            <?
                                $nboption =  $arrOfQuiz[13];
                                for($jdx = 1; $jdx<= $nboption; $jdx++){
                                    if ($jdx%2 == 1){
                                        $style = "style='background-color:#DDD;'";
                                    }
                                    else{
                                        $style = "";
                                    }
                                    ?>
                                <tr>
                                    <td width='1%' <? echo $style;?>><input type="checkbox" name='cbanswer<? echo $jdx;?>' id='cbanswer<? echo $jdx;?>' value='<? echo $jdx;?>' style='margin:3px;'/></td>
                                    <td width='99%' <? echo $style;?> onclick='if (document.getElementById("cbanswer<? echo $jdx;?>").checked){document.getElementById("cbanswer<? echo $jdx;?>").checked = false;}else{document.getElementById("cbanswer<? echo $jdx;?>").checked = true;}'><label ><? echo $arrOfQuiz[$optionSeq[$jdx-1]+2]; ?></label></td>
                                </tr>
                            <?
                                }
                            }
					?>
                                <tr>
                                    <td colspan='2'>
                                    <button type="submit" class='submit' name='btnSubmit' id='btnSubmit'></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <input type="text" style="display:none;" id="questionid" name="questionid" value="<? echo $arrOfQuiz[0]; ?>" />
                        <input type="text" style="display:none;" id="userid" name="userid" value="<? echo $employeeid; ?>" />
                        <input type="text" style="display:none;" id="txttransdate" name="txttransdate" value="<? echo $transdate; ?>" />
			            <input type="text" style="display:none;" id="txtassessmenttype" name="txtassessmenttype" value="<? echo $_SESSION["aSSeSSTYPe"]; ?>" />
			            <input type="text" style="display:none;" id="questionnum" name="questionnum" value="1" />
                        <input type="text" style="display:none;" id="optiontype" name="optiontype" value="<? echo $arrOfQuiz[14];?>" />
                        <input type="text" style="display:none;" id="nboption" name="nboption" value="<? echo $nboption;?>" />
                        <input type="text" style="display:none;" id="unittype" name="unittype" value="<? echo $_SESSION["CHaNNeLTYPe"];?>" />
                        <input type="text" style="display:none;" id="level" name="level" value="<? echo $_SESSION["LeVeL"];?>" />
                        
                <?
                /*Insert CSR/Agent Transaction to "Score" . Sebagai flag bahwa    */
                /*CSR/Agent sudah pernah melakukan assessment                     */

                $sqlscore = "INSERT INTO score ".
                            "   (employee_id, unit_id, set_id, assess_id, period, nb_done, nb_question, ".
                            "    nb_correct, nb_wrong, score, start_assess, regional, level) ".
                            "VALUES ".
                            "   ('$employeeid', ".
                            "     ".$_SESSION["uNiTiD"].", ".
                            "     $setid, ".
                            "    '".$_SESSION["aSSeSSTYPe"]."',".
                            "     $period, ".
                            "     1, ".
                            "     $nbmaxsoal, ".
                            "     0, ".
                            "     1, ".
                            "     0, ".
                            "     now(),".
				"     '".$_SESSION['ReGioNiD']."', ".
				"     $segment) ";

                $dbGate->ExecuteTransaction($sqlscore,"pnp");

                $sqldetail ="INSERT INTO score_detail ".
                            "   (employee_id, unit_id, set_id, assess_id, period, question_id,question_number,trans_id,start_assess, option_seq_id, solution, is_correct, start_server, regional) ".
                            "VALUES ".
                            "   ('$employeeid', ".
                            "     ".$_SESSION["uNiTiD"].", ".
                            "     $setid, ".
                            "    '".$_SESSION["aSSeSSTYPe"]."',".
                            "     $period, ".
                            "    '".$arrOfQuiz[0]."',".
                            "     1, ".
                            "    '$trueid',".
                            "    now(), ".
                            "    ".$arrOfQuiz[10].", ".
                            "    '".$arrOfQuiz[12]."', ".
                            "    0, ".
				"    '".date("Y-m-d H:i:s")."', ".
				"    '".$_SESSION['ReGioNiD']."') ";

                $dbGate->ExecuteTransaction($sqldetail,"pnp");

                $sqlcurrent = "SELECT now() ";
                $timeres = $dbGate->ExecuteQuery($sqlcurrent,"pnp");
                $arrOfTime = mysql_fetch_array($timeres);
                $_SESSION["lasttime"] = $arrOfTime[0];

                $logger->Logging(HistoryType::HISTORYTYPE_VIEW_QUESTION,$employeeid,"View Question #1 {".$arrOfQuiz[0]."}",$ipaddress);

                //$_SESSION["lasttime"] = date("Y-m-d H:
            }
        }
        else{
            /*Jika CSR/Agent sudah pernah melakukan assessment pada bulan ini */
            /*maka lanjutkan assessment dari soal terakhir                    */

            if ($arrOfRow[1] == 0){
                /*Jumlah soal yang dikerjakan belum lengkap*/
                $nbdonequest = $arrOfRow[0];
                $idxsoal = $nbdonequest+1;

                $quizsql =  "SELECT ".
                            "   qa.question_id, ".
                            "   set_id, ".
                            "   question, ".
                            "   option_a, ".
                            "   option_b, ".
                            "   option_c, ".
                            "   option_d, ".
                            "   option_e, ".
                            "   solution, ".
                            "   difficulty, ".
                            "   os.option_seq_id, ".
                            "   option_seq, ".
                            "   case
				                when option_type = 2
					                then case
						                when nb_option = 5
							                then if(substr(option_seq, 1, 1)=solution,1,if(substr(option_seq, 3, 1)=solution,2,if(substr(option_seq, 5, 1)=solution,3,if(substr(option_seq, 7, 1)=solution,4,5))))
						                when nb_option = 4
							                then if(substr(option_seq, 1, 1)=solution,1,if(substr(option_seq, 3, 1)=solution,2,if(substr(option_seq, 5, 1)=solution,3,4)))
						                when nb_option = 3
							                then if(substr(option_seq, 1, 1)=solution,1,if(substr(option_seq, 3, 1)=solution,2,3))
						                when nb_option = 2
							                then if(substr(option_seq, 1, 1)=solution,1,2)
						                end
				                when option_type = 1
					                then case
						                when nb_option = 5
							                then substr(concat( if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,'),
								                                if(locate(substr(option_seq,7,1),solution)=0,'','4,'),
									                            if(locate(substr(option_seq,9,1),solution)=0,'','5,')),1,
								                        length(concat(if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,'),
								                                if(locate(substr(option_seq,7,1),solution)=0,'','4,'),
									                            if(locate(substr(option_seq,9,1),solution)=0,'','5,')))-1)
						                when nb_option = 4
							                then substr(concat( if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,'),
								                                if(locate(substr(option_seq,7,1),solution)=0,'','4,')),1,
								                        length(concat(if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,'),
								                                if(locate(substr(option_seq,7,1),solution)=0,'','4,')))-1)
						                when nb_option = 3
							                then substr(concat( if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,')),1,
								                        length(concat(if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,'),
								                                if(locate(substr(option_seq,5,1),solution)=0,'','3,')))-1)
						                when nb_option = 2
							                then substr(concat( if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,')),1,
								                        length(concat(if(locate(substr(option_seq,1,1),solution)=0,'','1,'),
									                            if(locate(substr(option_seq,3,1),solution)=0,'','2,')))-1)
						            end
			                    end
			                as ans, ".
                        "   nb_option, ".
                        "   option_type ".
                            "FROM ".
                            "   question_active qa, ".
                            "   quiz q, ".
                            "   option_sequence os ".
                            "WHERE ".
                            "   q.question_id = qa.question_id and ".
                            "   employee_id = '$employeeid' and ".
                            "   os.option_seq_id = q.option_seq_id ".
                            "ORDER BY ".
                            "   sequence ".
                            "LIMIT $nbdonequest,1";

                $quizres = $dbGate->ExecuteQuery($quizsql,"pnp");
				//echo($quizsql);
                $ipaddress = "";

                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
                else{
                    $ipaddress = $_SERVER['REMOTE_ADDR'];
                }

                $logger = new Logger();
                $logger->setDBConnection($dbGate);

                if (mysql_num_rows($quizres) <= 0){
                    /*question untuk CSR/Agent ybs belum di generate */
                    echo "<div id='questionerror'>Questions is not enough! Contact Administrator (Willy Badawi123)</div>";
                    $logger->Logging(HistoryType::HISTORYTYPE_INSUFFICIENT_QUESTION,$employeeid,"View Question #$idxsoal",$ipaddress);
                    $questionisnotenough = 1;
                }
                else{
                    $arrOfQuiz = mysql_fetch_array($quizres);
                    $nbseconds = $arrOfQuiz[9];
                    $questionisnotenough = 0;
                    
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
                    <?
                        $optionSeq = explode(",",$arrOfQuiz[11]);
                    if ($arrOfQuiz[14] == "2"){
                    ?>
                        <table style="align:left;" id="assessmentpage">
                        		<thead><tr><th colspan="2"><?php echo "<span style='color:red;font-size:18px;'>($idxsoal)</span> ".$arrOfQuiz[2] ?></th></tr></thead>
                            <tbody>
                            <?
                                $nboption =  $arrOfQuiz[13];
                                for($jdx = 1; $jdx<= $nboption; $jdx++){
                                    if ($jdx%2 == 1){
                                        $style = "style='background-color:#DDD;'";
                                    }
                                    else{
                                        $style = "";
                                    }
                            ?>
                                <tr>
                                    <td width='1%' <? echo $style;?>><input type="radio" name='rbanswer' id='rbanswer<? echo $jdx;?>' value='<? echo $jdx;?>' style='margin:3px;'/></td>
                                    <td width='99%' <? echo $style;?> onclick='document.frmQuiz.rbanswer[<?echo $jdx-1;?>].checked = true;'><label ><? echo $arrOfQuiz[$optionSeq[$jdx-1]+2]; ?></label></td>
                                </tr>
                            <?
                                }
                            }
                            else{
                            ?>
                        <table style="align:left;" id="assessmentpage">
                        		<thead><tr><th colspan="2" align="left"><?php echo "<span style='color:red;font-size:18px;'>($idxsoal)</span> ".$arrOfQuiz[2] ?></th></tr></thead>
                            <tbody>
                            <?
                                $nboption =  $arrOfQuiz[13];
                                for($jdx = 1; $jdx<= $nboption; $jdx++){
                                    if ($jdx%2 == 1){
                                        $style = "style='background-color:#DDD;'";
                                    }
                                    else{
                                        $style = "";
                                    }
                                    ?>
                                <tr>
                                    <td width='1%' <? echo $style;?>><input type="checkbox" name='cbanswer<? echo $jdx;?>' id='cbanswer<? echo $jdx;?>' value='<? echo $jdx;?>' style='margin:3px;'/></td>
                                    <td width='99%' <? echo $style;?> onclick='document.getElementById("cbanswer<? echo $jdx;?>").checked = true;'><label ><? echo $arrOfQuiz[$optionSeq[$jdx-1]+2]; ?></label></td>
                                </tr>
                            <?
                                }
                            }
					?>
                                <tr>
                                    <td colspan='2'><button type="submit" class='submit' name='btnSubmit' id='btnSubmit'></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <input type="text" style="display:none;" id="questionid" name="questionid" value="<? echo $arrOfQuiz[0]; ?>" />
                        <input type="text" style="display:none;" id="userid" name="userid" value="<? echo $employeeid; ?>" />
                        <input type="text" style="display:none;" id="txttransdate" name="txttransdate" value="<? echo $transdate; ?>" />
			            <input type="text" style="display:none;" id="txtassessmenttype" name="txtassessmenttype" value="<? echo $_SESSION["aSSeSSTYPe"]; ?>" />
			            <input type="text" style="display:none;" id="questionnum" name="questionnum" value="<? echo $idxsoal;?>" />
                        <input type="text" style="display:none;" id="optiontype" name="optiontype" value="<? echo $arrOfQuiz[14];?>" />
                        <input type="text" style="display:none;" id="nboption" name="nboption" value="<? echo $nboption;?>" />
                        <input type="text" style="display:none;" id="unittype" name="unittype" value="<? echo $_SESSION["CHaNNeLTYPe"];?>" />
                        <input type="text" style="display:none;" id="level" name="level" value="<? echo $_SESSION["LeVeL"];?>" />
                      
                    <?

                    /*Update CSR/Agent Transaction di "Score". Sebagai flag bahwa     */
                    /*CSR/Agent sudah pernah mengakses pertanyaan nomor               */

                    $sqlscore = "UPDATE ".
                                "   score ".
                                "SET ".
                                "   nb_done = nb_done + 1, ".
                                "   nb_wrong = nb_wrong + 1 ".
                                "WHERE ".
                                "   employee_id = '$employeeid' and ".
                                "   period = $period and ".
				    "	assess_id = '".$_SESSION["aSSeSSTYPe"]."'";

                    $dbGate->ExecuteTransaction($sqlscore,"pnp");

                    $sqldetail ="INSERT INTO score_detail ".
                                "   (employee_id, unit_id, set_id, assess_id, period, question_id,question_number,trans_id,start_assess, option_seq_id, solution, is_correct, start_server, regional) ".
                                "VALUES ".
                                "   ('$employeeid', ".
                                "     ".$_SESSION["uNiTiD"].", ".
                                "     $setid, ".
                                "    '".$_SESSION["aSSeSSTYPe"]."',".
                                "     $period, ".
                                "    '".$arrOfQuiz[0]."',".
                                "     $idxsoal, ".
                                "    '$trueid',".
                                "    now(), ".
                                "    ".$arrOfQuiz[10].", ".
                                "    '".$arrOfQuiz[12]."', ".
                                "    0, ".
				    "    '".date("Y-m-d H:i:s")."', ".
				    "    '".$_SESSION['ReGioNiD']."') ";

                    $dbGate->ExecuteTransaction($sqldetail,"pnp");

                    $sqlcurrent = "SELECT now() ";
                    $timeres = $dbGate->ExecuteQuery($sqlcurrent,"pnp");
                    $arrOfTime = mysql_fetch_array($timeres);
                    $_SESSION["lasttime"] = $arrOfTime[0];

                    $logger->Logging(HistoryType::HISTORYTYPE_VIEW_QUESTION,$employeeid,"View Question #$idxsoal {".$arrOfQuiz[0]."}",$ipaddress);
                }
            }
            else{
                /*Soal sudah dikerjakan semua, tampilkan pesan  */
                $isassessmentdone = true;
                echo "<script language=\"javascript\">location.href=\"result.php\"</script>";
            }
        }
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
					if(($hak != 'B5TQ') && ($hak != "FBIQ") && ($hak != "H4JQ") && ($hak != "HAJQ") && ($hak != "BCRQ")  && ($hak != "PDRQ") && ($hak != "FOSQ") && ($hak != "CPRQ") && ($hak != "SBOQ") && ($hak != "BTLQ") && ($hak != "JKTQ") && ($hak != "KPKQ")){
			
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
