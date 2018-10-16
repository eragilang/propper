<?php

    class Logger{

        private $_gate; /* private */

        public function __construct(){
            
        }

        public function setDBConnection($gt){
            $this->_gate = $gt;
        }

        public function Logging($type, $user, $desc, $ip){
        	
        	$ipaddress = "";

			if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
			{
				$ipaddress=$_SERVER['HTTP_CLIENT_IP'];
    		}
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
			{
				$ipaddress=$_SERVER['HTTP_X_FORWARDED_FOR'];
    		}
			else
			{
				$ipaddress=$_SERVER['REMOTE_ADDR'];
    		}
        	
            $qry =  " INSERT ".
                    " INTO ".
                    "   pnp.logs ".
                    " VALUES ".
                    "   ('$type'," .
                    "    SYSDATE()," .
                    "    '$user', " .
                    "    \"$desc\", " .
                    "    '$ipaddress'" .
                    "   )".
                    "";

            try{
            	//echo $qry;
                $result = $this->_gate->ExecuteQuery($qry,"pnp");
            }
            catch(Exception $e){
                throw (new Exception($e->getMessage()));
            }
        }

        function __destruct(){
            //$this->_gate->CloseConnection();
        }
	}

    final class HistoryType /* final  */{
    
	    const HISTORYTYPE_LOGIN =  0;
	    const HISTORYTYPE_VIEW_QUESTION =   1; 
	    const HISTORYTYPE_END_ASSESSMENT =   2; 
	    const HISTORYTYPE_END_SESSION =   3; 
	    const HISTORYTYPE_ANSWER_QUESTION =   4; 
	    const HISTORYTYPE_ANSWER_QUESTION_RESUBMIT =   5; 
	    const HISTORYTYPE_INSUFFICIENT_QUESTION =   9; 
	    const HISTORYTYPE_VIEW_REPORT_CALLCENTER =   11;
		const HISTORYTYPE_VIEW_MOST_WRONG_ANSWERS_CALLCENTER =   111; 
	    const HISTORYTYPE_VIEW_REPORT_CATEGORY_CALLCENTER =   112;
		const HISTORYTYPE_VIEW_REPORT_SEGMENT_CALLCENTER =   113;
		const HISTORYTYPE_VIEW_REPORT_CALLCENTER_TOP10 =   114; 
	    const HISTORYTYPE_VIEW_REPORT_GRAPARI =   12; 
	    const HISTORYTYPE_VIEW_REPORT_GRAPARI_REGIONAL =   121; 
	    const HISTORYTYPE_VIEW_REPORT_GRAPARI_AREA =   122; 
	    const HISTORYTYPE_VIEW_REPORT_GRAPARI_NASIONAL =   123;
		const HISTORYTYPE_VIEW_REPORT_GRAPARI_REGIONAL_TOP10 = 124;
		const HISTORYTYPE_VIEW_REPORT_GRAPARI_AREA_TOP10 = 125;
		const HISTORYTYPE_VIEW_REPORT_GRAPARI_NASIONAL_TOP10 = 126;  
	    const HISTORYTYPE_VIEW_REPORT_GERAI =   13; 
	    const HISTORYTYPE_VIEW_REPORT_GERAI_REGIONAL =   131; 
	    const HISTORYTYPE_VIEW_REPORT_GERAI_AREA =   132; 
	    const HISTORYTYPE_VIEW_REPORT_GERAI_NASIONAL =   133;
		const HISTORYTYPE_VIEW_REPORT_GERAI_REGIONAL_TOP10 = 134;
		const HISTORYTYPE_VIEW_REPORT_GERAI_AREA_TOP10 = 135;
		const HISTORYTYPE_VIEW_REPORT_GERAI_NASIONAL_TOP10 = 136;  
	    const HISTORYTYPE_VIEW_REPORT_PLASA =   14; 
	    const HISTORYTYPE_VIEW_REPORT_PLASA_REGIONAL =   141; 
	    const HISTORYTYPE_VIEW_REPORT_PLASA_AREA =   142; 
	    const HISTORYTYPE_VIEW_REPORT_PLASA_NASIONAL =   143;
		const HISTORYTYPE_VIEW_REPORT_PLASA_REGIONAL_TOP10 = 144;
		const HISTORYTYPE_VIEW_REPORT_PLASA_AREA_TOP10 = 145;
		const HISTORYTYPE_VIEW_REPORT_PLASA_NASIONAL_TOP10 = 146;  
	    const HISTORYTYPE_GENERATE_QUESTIONS =   31; 
	    const HISTORYTYPE_SET_WRONG_QUESTION =   51; 
	    const HISTORYTYPE_UPLOAD_QUESTION =   71; 
	    const HISTORYTYPE_PAGE_ACCESS_VIOLATION =   81; 
	    const HISTORYTYPE_TIME_FREEZE_VIOLATION =   82; 
	    const HISTORYTYPE_TIME_END_VIOLATION =   83; 
	}
?>