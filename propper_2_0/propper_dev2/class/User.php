<?php

    class User{
    	private $_userID; /* private */
        private $_password; /* private */
        private $_userName; /* private */
        private $_accessRight; /* private */
        private $_assessID; /* private */
        private $_gatecsdm; /* private */
        private $_gatepnp; /* private */
        private $_unitID; /* private */
        private $_areaID; /* private */
        private $_channelType; /* private */
        private $_timeZone; /* private */
        private $_regionalID; /* private */
	 			private $_jobFunction; /* private */
	 			private $_segment; /* private */
	 			private $_level; /* private */
				
        function __construct($id){
            $this->_userID = $id;
        }

        function __destruct(){
            
        }
        
        function setDBConnectionCSDM($db){
        	$this->_gatecsdm = $db;
        }
        
        function setDBConnectionPNP($db){
        	$this->_gatepnp = $db;
        }

        function AuthenticateMe($pwd, $flag){

            /*Log In From CSDM */
            if (is_numeric($this->_userID)){
                $qry =  "SELECT ".
                        "   password, ".
                        "   nb_of_failed_attempt, ".
                        "   account_lock, ".
                        "   app.employee_code, ".
                        "   emp.unit_id, ".
                        "   employee_flag, ".
                        "   if(now() > date_add(last_request, interval 3 hour), 0,1) as valid ".
                        " FROM ".
                        "   t_appprofile app, t_employee emp ".
                        " WHERE ".
                        "   login_id = '".$this->_userID."' AND ".
                        "   emp.employee_code = app.employee_code AND ".
                        "   app_code = 'PNP'";

                try {
                    $result = $this->_gatecsdm->ExecuteQuery($qry, "csdm2");
                    $isValidUser = false;
					//echo $qry;
					//break;
                    $idx = 0;

                    if (mysql_num_rows($result) <= 0)
                        return false;

                    while ($user = mysql_fetch_row($result)){
                        $idx++;
                        if ($idx > 1) break;

                        if ($user[2] == '1'){
                            //account has been locked
                            $flag = "LOCK";
                            return false;
                        }
                        else if ($user[5] != '1'){
                            //account has been disabled
                            $flag = "DISABLED";
                            return false;
                        }
                        else if ($user[6] != '1'){
                            //account has been disabled
                            $flag = "TIME-OUT";
                            return false;
                        }
                        else{
                            if ($user[0] != $pwd){
                            // login attemp failed. User id and password did not match
                            // increment number of failed attemp
                            // if number of failed attemp exceed, then lock the account

                                if ($user[1] >= 5){
                                    $qry =  " UPDATE t_appprofile ".
                                            " SET account_lock = 1 ".
                                            " WHERE ".
                                            "  (login = '$this->_userID') AND".
                                            "  (app_code = 'PNP')";
                                }
                                else{
                                    $nbFailed = $user[1] + 1;
                                    $qry =  " UPDATE t_appprofile ".
                                            " SET nb_of_failed_attempt = $nbFailed ".
                                            " WHERE ".
                                            "  (login_id = '$this->_userID') AND".
                                            "  (app_code = 'PNP')";
                                }

                                $updtResult = $this->_gatecsdm->ExecuteTransaction($qry, "csdm2");

                                if (!$updtResult){
                                    throw (new Exception ("updating user status failed !"));
                                }

                                $flag = "WRONG-PASSWORD";
                            }
                            else{
                                //login attemp success, get cs info from
                                $qry =  " SELECT ".
                                        "   employee_name,".
                                        "   gender, ".
                                        "   emp.unit_id, ".
                                        "   job_function, ".
                                        "   unit_type, ".
                                        "   unit_name, ".
					     "	  segment, ".
					     "   (select regional from csdm2.t_unit_branch u where u.unit_id = emp.unit_id) as regional ".
                                        " FROM ".
                                        "   t_employee emp ".
                                        " WHERE ".
                                        "   employee_code = '$user[3]' ".
                                        "";

				//echo $qry;

                                try {
                                    $rest = $this->_gatecsdm->ExecuteQuery($qry, "csdm2");
                                    $count = 0;
                                    $ltrans  ="";
                                    while ($userinfo = mysql_fetch_row($rest)){
                                        $count++;
                                        if ($count > 1) break;
						if(strtoupper($userinfo[4])=='CALLCENTER'){
							$this->_channelType = strtoupper($userinfo[4]);
                                        $this->_userName = ucwords(strtolower($userinfo[0]));
                                        $this->_unitID = $userinfo[2];
     					     $this->_jobFunction = strtoupper($userinfo[3]);
					     $this->_segment = strtoupper($userinfo[6]);
                                        $this->_accessRight = "CSAG";
                                        $this->_assessID = "REG";

					     if(strtoupper($userinfo[4])=='CALLCENTER') {
							$this->_regionalID = 0;
					     }
					     else{
					     	$this->_regionalID = $userinfo[7];
					     }

					     $arrOfGakBoleh = array();//("PA","VALIDATION","ACTIVATION","SUPERVISOR","SUPERVISOR_BO","TPR","CASHIER_PAYMENT_POINT");

					     if(in_array(strtoupper($userinfo[3]),$arrOfGakBoleh )){
						$flag = "NO-RIGHT";
                            	    	return false;
					     }

										switch (strtoupper($userinfo[3])){
											#CALLCENTER InBOUND#
											/*case "AGENT_POSTPAID_BILLINGCC"		: $this->_level = 3; break;
											case "AGENT_PREPAID_BILLINGCC"		: $this->_level = 6; break;
											case "AGENT_POSTPAID_NONTECHNICAL"	: $this->_level = 42; break;
											case "AGENT_POSTPAID_TECHNICAL"		: $this->_level = 1; break;
											case "AGENT_PREPAID_NONTECHNICAL"	: $this->_level = 41; break;
											case "AGENT_PREPAID_TECHNICAL"		: $this->_level = 4; break;
											case "AGENT_POSTPAID_SALES"			: $this->_level = 2; break;
											case "AGENT_PREPAID_SALES"			: $this->_level = 5; break;
											case "AGENT_MKIOS"					: $this->_level = 10; break;
											case "AGENT_WEBSERVICE"				: $this->_level = 47; break;
											case "CHO_ECARE"					: $this->_level = 48; break;
											case "AGENT_FACEBOOK"				: $this->_level = 50; break;
											case "AGENT_TWITTER"				: $this->_level = 51; break;
											case "AGENT_EMAIL"					: $this->_level = 52; break;
											case "AGENT_LIVECHAT"				: $this->_level = 53; break;
											case "AGENT_WHATSAPP"				: $this->_level = 54; break;
											case "AGENT_SMS1111"				: $this->_level = 55; break;
											case "AGENT_LOOPNESIA"				: $this->_level = 56; break;
											case "AGENT_MYTELKOMSELWEB"			: $this->_level = 57; break;
											case "AGENT_PRIORITY"				: $this->_level = 58; break;
											case "AGENT_CORPORATE"				: $this->_level = 59; break;*/
											
											#INBOUND & CHO CALLCENTER
											case "AGENT_PREPAID_TECHNICAL"		: $this->_level = 1; break;
											case "AGENT_PREPAID_NONTECHNICAL"	: $this->_level = 1; break;
											case "AGENT_POSTPAID_TECHNICAL"		: $this->_level = 1; break;
											case "AGENT_POSTPAID_NONTECHNICAL"	: $this->_level = 1; break;
											case "AGENT_MKIOS"					: $this->_level = 1; break;
											case "COMPLAINT_HANDLING_OFFICER"	: $this->_level = 1; break;
											#case "QUALITY_ASSURANCE"			: $this->_level = 1; break;
											case "AGENT_PRIORITY"				: $this->_level = 2; break;
											case "AGENT_CORPORATE"				: $this->_level = 3; break;
											
											#OUTBOUND CALLCENTER
											case "AGENT_OUTBOUND_CARING"		: $this->_level = 4; break;
											case "AGENT_OUTBOUND_SALES"			: $this->_level = 5; break;
											case "AGENT_OUTBOUND_TELECOLLECTION": $this->_level = 6; break;
											
											#SPV CALLCENTER
											case "SPV_CHO"						: $this->_level = 9; break;
											case "SPV_INBOUND"					: $this->_level = 9; break;
											case "SPV_OUTBOUND"					: $this->_level = 10; break;
											case "SPV_QA"						: $this->_level = 9; break;
											
											#TL CALLCENTER
											case "TL_CHO"						: $this->_level = 11; break;
											case "TL_OUTBOUND"					: $this->_level = 12; break;
											case "TL_QA"						: $this->_level = 11; break;
											case "TEAM_LEADER_CC"				: $this->_level = 11; break;
											case "TL_INBOUND"					: $this->_level = 11; break;
											case "TL_DESK"						: $this->_level = 11; break;
											case "Trainer"						: $this->_level = 11; break;
											
											#CALLCENTER Infomedia#
											/*case "COMPLAINT_HANDLING_OFFICER"	: $this->_level = 7;break;
											case "COMPLAINT_HANDLING_OFFICER2T"	: $this->_level = 7;break;
											case "AGENT_BUFFER"					: $this->_level = 7;break;
											case "QUALITY_ASSURANCE"	        : $this->_level = 7; break;
											case "SDM"							: $this->_level = 7; break;
											case "KOORDINATOR_CHO"				: $this->_level = 7; break;
											case "AGENT_3GCALL"					: $this->_level = 7; break;
											case "OPERATIONAL_OUTBOUND_CC"		: $this->_level = 7; break;
											case "TL_INDBOUND"					: $this->_level = 7; break;
											case "TL_OUTBOUND"					: $this->_level = 7; break;
											case "TL_CHO"						: $this->_level = 7; break;
											case "TL_QA"						: $this->_level = 7; break;
											case "TL_DESK"						: $this->_level = 7; break;
											case "SPV_INBOUND"					: $this->_level = 7; break;
											case "SPV_OUTBOUND"					: $this->_level = 7; break;
											case "SPV_CHO"						: $this->_level = 7; break;
											case "SPV_QA"						: $this->_level = 7; break;
											case "SPV_QIA"						: $this->_level = 7; break;
											case "SPV_IT"						: $this->_level = 7; break;
											case "AGENT_OUTBOUND_SALES"			: $this->_level = 7; break;
											case "AGENT_OUTBOUND_CARING"		: $this->_level = 7; break;
											case "AGENT_OUTBOUND_TELECOLLECTION": $this->_level = 7; break;
											case "TEAM_SUPPORT"					: $this->_level = 7; break;
											case "TRAINER"						: $this->_level = 7; break;
											case "AGENT_OUTBOUND_CALL"			: $this->_level = 43; break;*/
											
											#NEW CALLCENTER
												#PRIORITY
												
												#CORPORATE
												
												#REGULER
												
												#SALES
												
												#CARRING
												
												#COLLECTION
											
											#Gerai#
											case "SUPERVISOR_BO"				: $this->_level = 45; break;
											#case "TEAM_LEADER"					: $this->_level = 40; break;	
											break;		
											case "FRONTLINER"					: $this->_level = 3; break;
											case "CASHIER_GERAI"				: $this->_level = 46; break;
											#case "FRONT_OFFICE_SUPPORT"			: $this->_level = 1; break;
											
											#Plasa#
											case "FRONTLINER_PLASA"				: $this->_level = 3; break;
											case "CASHIER_PSO"					: $this->_level = 3; break;
											case "ADMIN_WAREHOUSE"				: $this->_level = 3; break;
											case "SALES_REPRESENTATIF"			: $this->_level = 3; break;
											case "TEAM_LEADER_PLASA"			: $this->_level = 60; break;
											
											#GraPARI#
											case "FRONTLINER_REGULER"			: $this->_level = 2; break;
											case "CASHIER"						: $this->_level = 5; break;
											case "FRONT_OFFICE_SUPPORT"			: 
													if(strtoupper($userinfo[4])=='GRAPARI') {
														$this->_level = 9;

													}else if(strtoupper($userinfo[4])=='GERAI') {
														$this->_level = 1; 
													}
											 break;
											case "TEAM_LEADER"			: 
													if(strtoupper($userinfo[4])=='GRAPARI') {
														$this->_level = 6;

													}else if(strtoupper($userinfo[4])=='GERAI') {
														$this->_level = 40; 
													
													
													}else{
														if (substr($userinfo[2],3,1) == "1"){
															$this->_level = 5;
														}else if(strtoupper($userinfo[4])=='WEBSERVICE') {
														$this->_level = 49; 
														}
														else{
															$this->_level = 7;
														//	$this->_level = 5;														
														}
													} 
													break;
											case "QUICK_SERVICE"				: $this->_level = 3; break;
											/*case "TEAM_LEADER_CC"				: 
													if (substr($userinfo[2],3,1) == "1"){
														$this->_level = 8;
													}
													else{
														$this->_level = 8;
													//	$this->_level = 5;
													}
 													break;*/
											case "CRO"							: $this->_level = 3; break;
											case "BUFFER_CSR"					: $this->_level = 3; break;
											
											case "QUALITY_ASSURANCE"	: 
													if (substr($userinfo[2],3,1) == "1"){
														$this->_level = 1;
													}
													else{
														$this->_level = 1;
													}
													break;
											
											case "BE_SUPPORT"					: $this->_level = 8; break;
											case "TEAM_LEADER_BES"				: $this->_level = 44; break;
										}


										if (trim($this->_level) == ""){
											$flag = "NO-RIGHT";
											
				                            return false;
										}
						}else{
							$flag = "NO-RIGHT";
							return false;
						}
					     
                                    }

                                    /*reset nb_of_failed_attempt*/
                                    if ($user[1] != 0){
                                        $qry =  " UPDATE t_appprofile ".
                                                " SET nb_of_failed_attempt = 0 ".
                                                " WHERE ".
                                                "  (login_id = '$this->_userID') AND".
                                                "  (app_code = 'PNP')";

                                        $updt = $this->_gatecsdm->ExecuteTransaction($qry,"csdm2");

                                        if (!$updt){
                                            throw (new Exception ("failed to reset failed attempt!"));
                                        }
                                    }
                                }
                                catch(Exception $e){
                                    throw (new Exception ($e->getMessage()));
                                }

                                $isValidUser = true;
                            }
                        }
                    }
                }
                catch(Exception $e){
                    throw new Exception ($e->getMessage());
                }
            }
            else{
          		
          		$queryaccess = "SELECT 
									assess_id,
									access_right,
									level
								FROM
									pnp.assess_type
								WHERE
									assess_user_prefix = '".strtoupper(substr($this->_userID,0,4))."' AND
									enable = '1' 
									";

							$accessres = $this->_gatepnp->executeQuery($queryaccess,"pnp");
				
				if(	mysql_num_rows($accessres) > 0){
					$arrOfAccess = mysql_fetch_array($accessres);
					
					$qry = "SELECT 
								password, 
								user_name, 
								assess_id, 
								unit_type,
								regional 
							FROM 
								pnp.assess_user 
							WHERE 
								user_id = '".$this->_userID."' AND 
								acc_status = '1'";
					
					try{
						$result = $this->_gatepnp->ExecuteQuery($qry, "pnp");
						$isValidUser = false;

						if (mysql_num_rows($result) <= 0)
							return false;
					
						$idx = 0;					
						while ($user = mysql_fetch_row($result)){
							$idx++;
							if ($idx > 1) break;
							if ($user[0] != $pwd){
								return false;
							}
							else{
								$this->_channelType = strtoupper($user[3]);
								$this->_userName = ucwords(strtolower($user[1]));
								$this->_unitID = $user[4];
								$this->_jobFunction = "";
								$this->_accessRight = $arrOfAccess[1];
								$this->_level = $arrOfAccess[2];
								$this->_assessID = $arrOfAccess[0];
								$isValidUser = true;
								$this->_regionalID = $user[4];
							}
                        
						}
					}
					catch(Exception$e){
							throw new Exception ($e->getMessage());
       				}
				}
				else{
	                $qry =  " SELECT ".
	                        "  employee_code, ".
	                        "  full_username, ".
	                        "  job_function, ".
	                        "  unit_type, ".
	                        "  user_area, ".
	                        "  (select regional from csdm2.t_unit_branch u where u.unit_id = us.unit_id) as user_regional, ".
	                        "  unit_id, ".
	                        "  password, ".
	                        "  nb_of_failed_attempt, ".
	                        "  account_lock, ".
	                        "  login_type, ".
	                        "  user_level, ".
				   "  job_functioncode ".
	                        " FROM ".
	                        "  t_user_xxx us ".
	                        " WHERE ".
	                        "  (login_id = '$this->_userID') ".
	                        "";
                try{
                    $result = $this->_gatecsdm->ExecuteQuery($qry, "csdm2");
                    $isValidUser = false;

                    if (mysql_num_rows($result) <= 0)
                        return false;

                    $idx = 0;
                    while ($user = mysql_fetch_row($result)){
                        $idx++;
                        if ($idx > 1) break;
                        
                        if ($user[9] == '1'){
                            $flag = "LOCK";
                            return false;
                        }
                        else{
                            if ($user[7] != $pwd){
                                if ($user[8] >= 5){
                                    $qry =  " UPDATE t_user_xxx ".
                                            " SET account_lock = 1 ".
                                            " WHERE ".
                                            "  (login_id = '$this->_userID') ";

                                }
                                else{
                                    $nbFailed = $user[8] + 1;
                                    $qry =  " UPDATE t_user_xxx ".
                                            " SET nb_of_failed_attempt = $nbFailed ".
                                            " WHERE ".
                                            "  (login_id = '$this->_userID') ";
                                }

                                $updtResult = $this->_gatecsdm->ExecuteTransaction($qry, "csdm2");

                                if (!$updtResult){
                                    throw (new Exception ("updating user status failed !"));
                                }

                                $flag = "WRONG-PASSWORD";

                            }
                            else{
                                $isValidUser = true;
                                $this->_userName = $user[1];
                                $this->_unitID = $user[6];
                                
                                $this->_areaID = $user[4];
                                $this->_channelType = strtoupper($user[10]);
                                $this->_assessID = "REG";
				    if(strtoupper($user[10])=='CALLCENTER') {
					$this->_regionalID = 0;
				    }
				    else{
					$this->_regionalID = $user[5];
				     }
				    
                                if (($user[10] == "grapari") && ($user[11]=="supervisor")){
                                    $this->_accessRight = "GRPV";
					 				$this->_segment = "BO";
					 				$this->_level = 4;
                                }
				    			else if (($user[10] == "gerai") && ($user[11]=="user")){
                                    $this->_accessRight = "GSPV";
                                }
				    			else if (($user[10] == "gerai") && ($user[11]=="supervisor")){
                                    $this->_accessRight = "GSPV";
                                }
                                else if (($user[10] == "callcenter") && ($user[12]=="SPV")){
                                    $this->_accessRight = "CSAG";
					 				$this->_segment = "BO";
					 				$this->_level = 7;
                                }
				    			else if (($user[10] == "callcenter") && ($user[12]=="STAFF")){
                                    $this->_accessRight = "CSAG";
					 				$this->_segment = "BO";
					 				$this->_level = 7;
                                }
				    			else if (($user[10] == "grapari") && ($user[11]=="bo")){
                                    $this->_accessRight = "BOGH";
					 				$this->_segment = "BO";
					 				$this->_level = 4;
                                }
				    			else if (($user[10] == "grapari") && ($user[11]=="user")){
                                    $this->_accessRight = "BOGH";
					 				$this->_segment = "BO";
					 				$this->_level = 4;
                                }
                                else if (($user[10] == "headquarter") && ($user[11] == "administrator")){
                                    $this->_accessRight = "ADMN";
                                }

								else if (($user[10] == "regional_sq") && ($user[11] == "supervisor")){
																	$this->_accessRight = "RGSM";
													 $this->_level = 7;

                                }
								else if (($user[10] == "regional_thirdparty") && ($user[11] == "supervisor")){
																	$this->_accessRight = "RGSM";
																		 $this->_level = 7;

                                }


								else if (($user[10] == "regional_fs") && ($user[11] == "supervisor")){
																	$this->_accessRight = "RGSM";
													 $this->_level = 7;

                                }


                                /*reset nb_of_failed_attempt*/
                                if ($user[8] != 0){
                                    $qry =  " UPDATE t_user_xxx ".
                                            " SET nb_of_failed_attempt = 0 ".
                                            " WHERE ".
                                            "  (login_id = '$this->_userID')";

                                    $updt = $this->_gatecsdm->ExecuteTransaction($qry,"csdm2");

                                    if (!$updt){
                                        throw (new Exception ("failed to reset failed attempt!"));
                                    }
                                }
                            }
                        }
                    }
                }
                catch(Exception $e){
                    throw new Exception ($e->getMessage());
                }
				}
            }

            if ($isValidUser){
                //$this->_gatecsdm->CloseConnection();
                return true;
            }
            else{
                //$this->_gatecsdm->CloseConnection();
                return false;
            }
        }

        function SetUserName($name){
            $this->_userName = $name;
        }

        function GetUserName(){
            return $this->_userName;
        }

        function SetUserID($id){
            $this->_userID = $id;
        }

        function GetUserID(){
            return $this->_userID;
        }

        function SetUserAccessRight($hak){
            $this->_accessRight = $hak;
        }

        function GetUserAccessRight(){
            return $this->_accessRight;
        }
        
        function SetUserAssessID($assessid){
            $this->_assessID = $hak;
        }

        function GetUserAssessID(){
            return $this->_assessID;
        }

        function GetAreaID(){
            return $this->_areaID;
        }

        function SetAreaID($area){
            $this->_areaID = $area;
        }

        function GetRegionalID(){
            return $this->_regionalID;
        }

        function SetRegionalID($reg){
            $this->_regionalID = $reg;
        }

        function GetUnitID(){
            return $this->_unitID;
        }

        function SetUnitID($unitid){
            $this->_unitID = $unitid;
        }

        function GetChannelType(){
            return $this->_channelType;
        }

        function SetChannelType($type){
            $this->_channelType = $type;
        }

        function GetTimeZone(){
            return $this->_timeZone;
        }

        function SetTimeZone($zone){
            $this->_timeZone = $zone;
        }

	 	function GetJobFunction(){
            return $this->_jobFunction;
        }

	 	function GetSegment(){
            return $this->_segment;
        }

	 	function GetLevel(){
            return $this->_level;
        }
}

?>
