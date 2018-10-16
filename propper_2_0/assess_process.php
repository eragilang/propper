<?
    session_start();

    /*Validate Trans ID*/
    $issessionend = false;

    $hak = $_SESSION['aCCeSSRiGHT'];
    $employeeid = $_SESSION["uSeRiD"];
    if(($hak == "") || ($employeeid == "")){
        /*Session sudah expired*/
        $employeeid = $_REQUEST["userid"];
        $issessionend = true;
    }

   	$transdate = $_SESSION["transdate"];
    $questionnum = $_REQUEST["questionnum"];

    if ($transdate == ""){
        $transdate = $_REQUEST["txttransdate"];
    }

    //$trueid = strtoupper(substr(md5($employeeid."SQM".$transdate),0,10));
    $trueid = strtoupper(substr(md5($employeeid."SQM".$transdate.$questionnum),0,10));
    $getid = $_REQUEST["id"];

    /*Validate transaction id*/
    if ($trueid != $getid){
        echo "<script language=\"javascript\">location.href=\"assessment.php?id=$newid&qnum=$nextquestionnum\"</script>";
        return;
    }

    /*Initialization*/
    //$setid = 0;

    require_once ("class/User.php");
   	require_once ("class/DBgateway.php");
    require_once ("class/ConfigReader.php");
    require_once ("class/Logger.php");
    
    $conf = new ConfigReader();

    $period = date('Ym');
    $questionid = $_REQUEST["questionid"];

    try {
        $gate = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
	    $gate->OpenConnection();
	    
	    //validate access right
	    $query = "SELECT * 
				FROM 
					pnp.page_access_right
				WHERE
				 	file_name = 'assess_process.php' AND
			 		access_right = '$hak' AND
			 		enable = '1' ";
			 		
		$accessres = $gate->ExecuteQuery($query,"pnp"); 
	    
	    if (mysql_num_rows($accessres) <= 0){
	    	session_destroy();
	        echo "<script language=\"javascript\">location.href=\"index.php?err=75\"</script>";
	    }

        if (($employeeid != "") && ($questionid != "")){
		    $sqlanswer = " SELECT ".
                         "   case
				                when option_type = 2
					                then case
								when nb_option = 8
											then if(substr(option_seq, 1, 1)=solution,1,if(substr(option_seq, 3, 1)=solution,2,if(substr(option_seq, 5, 1)=solution,3,if(substr(option_seq, 7, 1)=solution,4,if(substr(option_seq, 9, 1)=solution,5,if(substr(option_seq, 11, 1)=solution,6,if(substr(option_seq, 13, 1)=solution,7,8)))))))
                                        when nb_option = 7
                                            then if(substr(option_seq, 1, 1)=solution,1,if(substr(option_seq, 3, 1)=solution,2,if(substr(option_seq, 5, 1)=solution,3,if(substr(option_seq, 7, 1)=solution,4,if(substr(option_seq, 9, 1)=solution,5,if(substr(option_seq, 11, 1)=solution,6,7))))))
                                        when nb_option = 6
                                             then if(substr(option_seq, 1, 1)=solution,1,if(substr(option_seq, 3, 1)=solution,2,if(substr(option_seq, 5, 1)=solution,3,if(substr(option_seq, 7, 1)=solution,4,if(substr(option_seq, 9, 1)=solution,5,6)))))
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
                         "  time_to_sec(timediff(now(), '".$_SESSION["lasttime"]."')) as diff ".
                         "FROM ".
                         "  question_active qa, ".
                         "  quiz q, ".
                         "  option_sequence os ".
                         "WHERE ".
                         "  q.question_id = '$questionid' and ".
                         "  q.question_id = qa.question_id and ".
                         "  employee_id = '$employeeid' and ".
                         "  os.option_seq_id = q.option_seq_id and ".
			    "  qa.assess_id = '".$_POST["txtassessmenttype"]."' ";

            //echo $sqlanswer;
            $answerres = $gate->ExecuteQuery($sqlanswer,"pnp");
            $arrOfQuiz = mysql_fetch_array($answerres);
            $solution = $arrOfQuiz[0];
            $timediff = $arrOfQuiz[1];
            $lock = 0;

            if ($timediff >= 450){
				//$lock = 1;
            }

            $isCorrect = 0;

            $ipaddress = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else{
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            }

            $logger = new Logger();
            $logger->setDBConnection($gate);

            $employeeanswer = "";

            if ($_REQUEST["optiontype"] == "2"){
                $employeeanswer = $_REQUEST["rbanswer"];
            }
            else{
                for ($kdx = 1; $kdx <= $_REQUEST["nboption"]; $kdx++){
                    if ($_REQUEST["cbanswer".$kdx] != ""){
                        $employeeanswer .= $_REQUEST["cbanswer".$kdx].",";
                    }
                }

                $employeeanswer = substr($employeeanswer, 0, strlen($employeeanswer) - 1);
            }

            if ($solution == $employeeanswer){
                $isCorrect = 1;

		  //cek apakah solusi sebelumnya sudah pernah di submit
		  		$sqlchecksubmit = 	"SELECT 
				  						is_correct 
				  					FROM 
								  		score_detail 
				  					WHERE 
									  	employee_id = '$employeeid' and 
				  						period = $period and 
									  	question_id = '$questionid' and 
									  	assess_id = '".$_POST["txtassessmenttype"]."' ";

				$checksubmitres = $gate->ExecuteQuery($sqlchecksubmit,"pnp");
            	$arrOfCheck = mysql_fetch_array($checksubmitres);

		  		if($arrOfCheck[0] == "0"){

                	$sqlscore =	"UPDATE 
									score s 
								SET 
									nb_correct = nb_correct + 1, 
									nb_wrong = nb_done - nb_correct, 
									score = score + (SELECT score_per_question FROM pnp.assess_param WHERE unit_type='".$_POST["unittype"]."' AND LEVEL = ".$_POST["level"]."), 
									end_assess = now(), 
									s.lock = $lock, 
									nb_lock = if($lock = 1, nb_lock + 1, nb_lock), 
									last_lock_date = if($lock = 1, now(), last_lock_date) 
								WHERE 
									employee_id = '$employeeid' and 
									period = $period and 
									assess_id = '".$_POST["txtassessmenttype"]."' ";

                    //echo $sqlscore;

                	$gate->ExecuteTransaction($sqlscore,"pnp");

                	$sqldetail =	"UPDATE 
										score_detail 
									SET 
										end_assess = now(), 
										duration = time_to_sec(timediff(now() , start_assess)), 
										employee_answer = '$solution', 
										is_correct = 1, 
										end_server = '".date("Y-m-d H:i:s")."', 
										duration_server = time_to_sec(timediff('".date("Y-m-d H:i:s")."' , start_server)) 
									WHERE 
										employee_id = '$employeeid' and 
										period = $period and 
										question_id = '$questionid' and 
										assess_id = '".$_POST["txtassessmenttype"]."' ";

               	 //echo $sqldetail;
                	$gate->ExecuteTransaction($sqldetail,"pnp");
                	$_SESSION["NoTe"] = "benar";
                	$_SESSION["ProCCeSSeD"] = 1;

                	$logger->Logging(HistoryType::HISTORYTYPE_ANSWER_QUESTION,$employeeid,"Answer Question $questionid {CORRECT} :: $solution",$ipaddress);
		  		}
		  		else{
					$_SESSION["NoTe"] = "benar";
                	$_SESSION["ProCCeSSeD"] = 1;

                	$logger->Logging(HistoryType::HISTORYTYPE_ANSWER_QUESTION_RESUBMIT,$employeeid,"Re-Submit Question Answer :: $questionid :: $solution",$ipaddress);
		  		}
            }
            else{
                $sqlscore =	"UPDATE 
								score s 
							SET 
								end_assess = now(), 
								s.lock = $lock, 
								nb_lock = if($lock = 1, nb_lock + 1, nb_lock), 
								last_lock_date = if($lock = 1, now(), last_lock_date) 
							WHERE 
								employee_id = '$employeeid' and 
								period = $period and 
								assess_id = '".$_POST["txtassessmenttype"]."' ";

                $gate->ExecuteTransaction($sqlscore,"pnp");

                $sqldetail = "UPDATE 
								score_detail 
							SET 
								end_assess = now(), 
								duration = time_to_sec(timediff(now() , start_assess)), 
								employee_answer = '".$employeeanswer."', 
								is_correct = 0, 
								end_server = '".date("Y-m-d H:i:s")."', 
								duration_server = time_to_sec(timediff('".date("Y-m-d H:i:s")."' , start_server)) 
							WHERE 
								employee_id = '$employeeid' and 
								period = $period and 
								question_id = '$questionid' and 
								assess_id = '".$_POST["txtassessmenttype"]."' ";

                //echo $sqldetail;
                $gate->ExecuteTransaction($sqldetail,"pnp");
                $_SESSION["NoTe"] = "salah";
                $_SESSION["ProCCeSSeD"] = 1;

                $logger->Logging(HistoryType::HISTORYTYPE_ANSWER_QUESTION,$employeeid,"Answer Question $questionid {WRONG} :: (".$employeeanswer.") ",$ipaddress);
            }

            if(isset($_REQUEST["iswrongquestion"])){
                $wrongquestsql = "INSERT INTO 
									question_wrong 
									VALUES ('$employeeid','$questionid',sysdate(), 0)";
                //echo $wrongquestsql;

                $gate->ExecuteTransaction($wrongquestsql,"pnp");

                $logger->Logging(HistoryType::HISTORYTYPE_SET_WRONG_QUESTION,$employeeid,"State $questionid as a Wrong Question",$ipaddress);
            }

            if ($lock == 1){
                $logger->Logging(HistoryType::HISTORYTYPE_TIME_FREEZE_VIOLATION,$employeeid,"Time Freeze Violation $questionid ",$ipaddress);
                echo "<script language=\"javascript\">location.href=\"logout.php?cause=82\"</script>";
                return;
            }

            $sqlcheck = "SELECT 
							nb_done, 
							if(nb_done = nb_question, 1, 0) as flag, 
							score 
						FROM 
							score 
						WHERE 
							employee_id = '$employeeid' and 
							period = $period and 
							assess_id = '".$_POST["txtassessmenttype"]."' ";  

            $checkres = $gate->ExecuteQuery($sqlcheck,"pnp");

            $arrOfRow = mysql_fetch_array($checkres);

            if ($arrOfRow[1] == 1){
                /*semua pertanyaan telah dijawab*/
                $sqldelete = "DELETE FROM quiz WHERE employee_id = '$employeeid'";

                $gate->ExecuteTransaction($sqldelete,"pnp");
                $_SESSION["assessStatus"] = "done";

                $logger->Logging(HistoryType::HISTORYTYPE_END_ASSESSMENT,$employeeid,"Assessment Done!",$ipaddress);
            }
            else if(isset($_REQUEST["isassessended"])){
                $_SESSION["assessStatus"] = "end";
                echo "<script language=\"javascript\">location.href=\"result.php\"</script>";
                $logger->Logging(HistoryType::HISTORYTYPE_END_SESSION,$employeeid,"Session Ended!",$ipaddress);
            }
            else{
                $_SESSION["assessStatus"] = "start";
            }

            if ($issessionend){
                echo "<script language=\"javascript\">location.href=\"index.php?err=999\"</script>";
            }
            else{
		  $nextquestionnum = $questionnum + 1;
                $newid = strtoupper(substr(md5($employeeid."SQM".$transdate.$nextquestionnum),0,10));
		        echo "<script language=\"javascript\">location.href=\"assessment.php?id=$newid&qnum=$nextquestionnum\"</script>";
            }
        }
        else{
            echo "<script language=\"javascript\">location.href=\"session_end.php\"</script>";
        }

        $gate->CloseConnection();
    }
    catch(Exception $e){
        echo $e->getMessage();
    }


?>
