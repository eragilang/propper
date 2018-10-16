<?php
	session_start();
	
	if($_SESSION['uSeRiD'] == ""){
    		$homelink = "index.php";
    	} else{
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
	      		} else{
	            		$ipaddress = $_SERVER['REMOTE_ADDR'];
	       	}
	
	      		$logger = new Logger();
	      		$logger->setDBConnection($dbGate);
	      		$logger->Logging(HistoryType::HISTORYTYPE_PAGE_ACCESS_VIOLATION,$_SESSION["uSeRiD"],"Page Access Violation : (home_upload_soal.php) ($trueid) ($getid) (".$_SESSION["prevpage"].")",$ipaddress);
        		
        		echo "<script language=\"javascript\">alert(\"Page Access Violation. Silahkan kembali ke halaman utama dan Login kembali\");self.close();</script>";
        		return;	
		} else {
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
				pnp.score 
		 		WHERE 
				employee_id = '".$_SESSION["uSeRiD"]."' and 
				period = $period and 
				assess_id = '".$_SESSION["aSSeSSiD"]."'";
				
        	$checkres = $dbGate->ExecuteQuery($sqlcheck,"pnp");
        	$arrOfCheck = mysql_fetch_array($checkres);
        	
        	$arrOfCheck[2];
	  }
    }
    catch (Exception $e){  	
    }	  	 		
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Upload Soal PNP</title>

<script>
		var arrayCallCenter = new Array("Billing",
										"Technical", 										 
										"Customer Care",
										"Sales",
										"CO & TL Infomedia",
										"TL, QAO, CHO, CHO 2 tier, 3G, Trainer, Buffer");
		
										
		var arrayGrapari = new Array("Frontliner-Priority",
										"Frontliner-Regular",
										"Quick Service",
										"Back Office",
										"Cashier",
										"Team Leader",
										"BE Support");

		var arrayGerai = new Array("Default");		
		var arrayPlasa = new Array("Default");
	    var arrayInduction = new Array("Default");
		var arrayDefault = new Array("Default");
			
		function ganti(pilihan) {
			var arraypilihan = eval("array"+pilihan);
			
			if ((pilihan == "CallCenter") || (pilihan == "Induction") || (pilihan == "Default")) {
				var arrayRegional = new Array("Default");
			} else {
				var arrayRegional = new Array("SumBagUt",
												"Sumbagteng",
												"Sumbagsel",
												"Western Jabotabek",
												"Central Jabotabek",
												"Eastern Jabotabek",
												"JaBar",
												"JaTeng",
												"JaTim",
												"Bali Nusra",
												"Kalimantan", 
												"Sulawesi"),
												"Puma");
			}
			
			while (arraypilihan.length < form.mnJobFunction.options.length) {
					form.mnJobFunction.options[(form.mnJobFunction.options.length - 1)] = null;
			}
			
			while (arrayRegional.length < form.mnRegional.options.length) {
					form.mnRegional.options[(form.mnRegional.options.length - 1)] = null;
			}
			
			var jml = arraypilihan.length;
			var jmlRegional = arrayRegional.length;
			
			
			for(var i = 0; i <= jml - 1; i++) {
				document.form.mnJobFunction.options[i] = new Option(arraypilihan[i]);
				if (arraypilihan[i] == "Default") {
					document.form.mnJobFunction.options[i].value = i;				
				} else {
					document.form.mnJobFunction.options[i].value = i+1;				
				}
			}
			for(var i = 0; i <= jmlRegional - 1; i++) {
				document.form.mnRegional.options[i] = new Option(arrayRegional[i]);
				document.form.mnRegional.options[i].value = i+1;
				
				if (arrayRegional[i] == "Default") {
					document.form.mnRegional.options[i].value = i;				
				} else {
					document.form.mnRegional.options[i].value = i+1;				
				}
			}		
		}
	</script>
<style type="text/css">
<!--
.style1 {
	font-size: 16px;
	font-weight: bold;
	color: #FFFFFF;
}
body {
	background-color: #333333;
}
.style2 {color: #FF0000}
.style9 {
	color: #FFFFFF;
	font-weight: bold;
	font-family: "Bookman Old Style";
	font-size: 36px;
}

a:visited {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	color: #333333;
	text-decoration: none;
}
a:focus {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	color: #ffffff;
	text-decoration: none;
}
a:link {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	color: #333333;
	text-decoration: none;
	margin-left: 7px;
}
a:hover {
	text-decoration: none;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 15px;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	color: #00CC00;
}

-->
</style>
</head>

<body>
<table width="600" border="0" align="center">
  <tr>
    <td width="600" height="65" valign="top" bgcolor="#FFFFFF"><table width="585" border="0" align="center" bgcolor="#810704">
      <tr>
        <td width="587" height="41" align="center"><span class="style9">PNP</span></td>
      </tr>
    </table></td>
  </tr>
  <tr>
  <td height="6" valign="top"></td>
  </tr>
  <tr>
    <td height="474" valign="top">
	
	<table width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td width="600" height="26" valign="top"><table width="585" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#810704">
            <tr>
              <td width="587" height="19"><div align="center" class="style1">Upload Soal PNP </div></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td height="427" valign="top">
		
		<form name="form" action="process_upload_soal.php" method="post" enctype="multipart/form-data">
  
<table width="581" border="0" align="center" cellpadding="7" cellspacing="0">


<?php /*
  <tr>
    <td width="102">Unit Type <span class="style2">*</span> </td>
    <td width="451">
	<select name="mnUnitType" onChange="ganti(document.form.mnUnitType.options[document.form.mnUnitType.selectedIndex].value)">
	<option value='CallCenter' selected>CallCenter
	<option value='Grapari'>Grapari
	<option value='Gerai'>Gerai
	<option value='Plasa'>Plasa
	<option value='Induction'>Induction
	</select>	</td>
    </tr>
  <tr>
    <td>Regional <span class="style2">*</span> </td>
    <td><select name="mnRegional">
	<option value='0'> Default
	</select></td>
    </tr>
*/
?>


<?php 
include "connection.php";

if($_SESSION['unitSetIdSoal'] == 1) { ?>

		<tr>
    			<td width="102">Job Function <span class="style2">*</span> </td>
    			<td width="451">
				<select name="mnJobFunction" onChange="home_upload_soal.php">
					<option value='4'selected>Supervisor
					<option value='6'>Team Leader
					<option value='2'>Frontliner Reguler
					<option value='5'>Cashier
					<option value='9'>Front Office Support
					<option value='45'>Supervisor Gerai
					<option value='40'>Team Leader Gerai
					<option value='3'>Frontliner
					<option value='46'>Cashier Gerai
					<option value='1'>Front Office Support Gerai
					<option value='8'>BE Support
					<option value='44'>TL BE Support
					<option value='60'>Team Leader Plasa
					<option value='3'>Frontliner Plasa
						
				</select>
			</td>
    		</tr>

<?php } else if($_SESSION['unitSetIdSoal'] == 2) { 
			if ($_SESSION['uSeRiD'] == 'R3G-CallCenter'){
?>

			<tr>
    			<td width="102">Job Function <span class="style2">*</span> </td>
    			<td width="451">
				<select name="mnJobFunction">
				<!-- 	<option value='1' selected>AGENT POSTPAID TECHNICAL
					<option value='2'>AGENT POSTPAID SALES
					<option value='3'>AGENT POSTPAID BILLINGCC
					<option value='4'>AGENT PREPAID TECHNICAL
					<option value='5'>AGENT PREPAID SALES
					<option value='6'>AGENT PREPAID BILLINGCC
					<option value='7' selected>OTHERS SEGMENT	
					 <option value='41'>AGENT PREPAID NONTECHNICAL	
					<option value='42'>AGENT POSTPAID NONTECHNICAL -->
					<option value='43'>AGENT OUTBOUND CALL
				</select>
			</td>
    		</tr>
<?php 
	}else{
?>
			<tr>
    			<td width="102">Job Function <span class="style2">*</span> </td>
    			<td width="451">
				<select name="mnJobFunction">
					<option value='1' selected>AGENT POSTPAID TECHNICAL
					<option value='42'>AGENT POSTPAID NONTECHNICAL
					<option value='2'>AGENT POSTPAID SALES
					<option value='3'>AGENT POSTPAID BILLINGCC
					<option value='4'>AGENT PREPAID TECHNICAL
					<option value='41'>AGENT PREPAID NONTECHNICAL
					<option value='5'>AGENT PREPAID SALES
					<option value='6'>AGENT PREPAID BILLINGCC
					<option value='47'>AGENT WEBSERVICE
					<option value='48'>CHO ECare
					<option value='49'>TEAM LEADER Ecare
					<option value='50'>Agent Facebook
					<option value='51'>Agent Twitter
					<option value='52'>Agent Email
					<option value='53'>Agent Livechat
					<option value='54'>Agent Whatsapp
					<option value='55'>Agent SMS1111
					<option value='56'>Agent Loopnesia
					<option value='57'>Agent MyTelkomselWeb
					<option value='58'>Agent Priority
					<option value='59'>Agent Corporate
					<option value='10'>AGENT MKIOS
					<option value='7'>OTHERS SEGMENT
					<!-- <option value='43'>AGENT OUTBOUND CALL -->
				</select>
			</td>
    		</tr>
	
<?php		
	}

} ?>


<?php /*
  <tr>
    <td>Job Function <span class="style2">*</span> </td>
    <td><select name="mnJobFunction">
	<option value='0'> Default
	</select></td>
    </tr>
*/ ?>


  <tr>
    <td>Periode <span class="style2">*</span> </td>
    <td><input name="tahun" type="text" value="<?php echo date('Y'); ?>" size="4">
      Tahun
      <select name="bulan" id="bulan">
        <option value="01" selected>January</option>
        <option value="02">Februari</option>
        <option value="03">Maret</option>
        <option value="04">April</option>
        <option value="05">Mei</option>
        <option value="06">Juni</option>
        <option value="07">Juli</option>
        <option value="08">Agustus</option>
        <option value="09">September</option>
        <option value="10">Oktober</option>
        <option value="11">November</option>
        <option value="12">Desember</option>
      </select>      
       Bulan</td>
    </tr>

	
	<tr> <td align="right"> </td>
		<td>Start Assess
	<select name="start_assess" id="start_assess">
        	<option value="1" selected>1</option>
        	<option value="2">2</option>
       	<option value="3">3</option>
        	<option value="4">4</option>
        	<option value="5">5</option>
        	<option value="6">6</option>
        	<option value="7">7</option>
        	<option value="8">8</option>
        	<option value="9">9</option>
        	<option value="10">10</option>
        	<option value="11">11</option>
        	<option value="12">12</option>
		    <option value="13">13</option>
       	    <option value="14">14</option>
        	<option value="15">15</option>
        	<option value="16">16</option>
        	<option value="17">17</option>
        	<option value="18">18</option>
        	<option value="19">19</option>
        	<option value="20">20</option>
        	<option value="21">21</option>
        	<option value="22">22</option>
		    <option value="23">23</option>
       	    <option value="24">24</option>
        	<option value="25">25</option>
        	<option value="26">26</option>
        	<option value="27">27</option>
        	<option value="28">28</option>
        	<option value="29">29</option>
        	<option value="30">30</option>
        	<option value="31">31</option>
      </select>  
	End Assess 
	<select name="end_assess" id="end_assess">
        	<option value="1" selected>1</option>
        	<option value="2">2</option>
			<option value="3">3</option>
        	<option value="4">4</option>
        	<option value="5">5</option>
        	<option value="6">6</option>
        	<option value="7">7</option>
        	<option value="8">8</option>
        	<option value="9">9</option>
        	<option value="10">10</option>
        	<option value="11">11</option>
        	<option value="12">12</option>
			<option value="13">13</option>
			<option value="14">14</option>
        	<option value="15">15</option>
        	<option value="16">16</option>
        	<option value="17">17</option>
        	<option value="18">18</option>
        	<option value="19">19</option>
        	<option value="20">20</option>
        	<option value="21">21</option>
        	<option value="22">22</option>
			<option value="23">23</option>
			<option value="24">24</option>
        	<option value="25">25</option>
        	<option value="26">26</option>
        	<option value="27">27</option>
        	<option value="28">28</option>
        	<option value="29">29</option>
        	<option value="30">30</option>
        	<option value="31">31</option>
      </select>
</td>
    	</tr>


  <tr>
    <td>File Upload <span class="style2">*</span> </td>
    <td><input name="file_source" type="file" class="edt" id="file_source" value="<?php $file_source?>" size="45"></td>
    </tr>
  <tr>
    <td></td>
    <td></td>
  </tr>
  
  	<tr> 
    	<td>&nbsp;</td>
    	<td>
		<input name="submit" type="submit" value="Upload" />		</td>
    </tr>
</table>
        </form>		

	<table width="200" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
            		<td width="587" height="19"></td>
          	</tr>
          	<tr bgcolor="#810704">
            		<td width="587" height="19"><div align="center" class="style1">Daftar Soal</div></td>
          	</tr>
		
		<?php
			$set_id		= $_SESSION['unitSetIdSoal'];
			$regional 		= $_SESSION['unitRegSoal']; //$_POST["mnRegional"];
	
			// check soal apakah sudah ada di question_active
			$chk_soal = "SELECT DISTINCT LEVEL FROM question_active"
				   	." WHERE set_id = '".$set_id."' AND"
				   	." regional = '".$regional."' ORDER BY level ASC";
			
			$res_chk_soal = mysql_query($chk_soal); //$dbGate->ExecuteQuery($chk_soal,"pnp");
		
			if ($res_chk_soal) {
				
				$slc_lvl_3 = "SELECT DISTINCT LEVEL, assess_desc FROM assess_type WHERE assess_id = '".$_SESSION['AssessIdSoal']."';"; 
				$res_slc_lvl_3 = mysql_query($slc_lvl_3);				
				$row_slc_lvl_3 = mysql_fetch_array($res_slc_lvl_3);				
				
				while ($row_chk_soal = mysql_fetch_array($res_chk_soal)) {
				?>
					<tr >
						<td width="587" height="19"><div align="center">
						<?php
						if($set_id == 1) { 
							switch ($row_chk_soal[0]){
								case "4"	: echo '<a href = view_soal.php?lvl=4>Back Office</a>'; break;
								case "6"	: echo '<a href = view_soal.php?lvl=6>Team Leader</a>'; break;
								case "2"	: echo '<a href = view_soal.php?lvl=2>Frontliner Reguler</a>'; break;
								case "5"	: echo '<a href = view_soal.php?lvl=5>Cashier</a>'; break;
								case "9"	: echo '<a href = view_soal.php?lvl=9>Front Office Support</a>'; break;
								case "45"	: echo '<a href = view_soal.php?lvl=45>Supervisor BO</a>'; break;
								case "40"	: echo '<a href = view_soal.php?lvl=40>Team Leader Gerai</a>'; break;
								case "3"	: echo '<a href = view_soal.php?lvl=3>Frontliner</a>'; break;
								case "46"	: echo '<a href = view_soal.php?lvl=46>Cashier Gerai</a>'; break;
								case "1"	: echo '<a href = view_soal.php?lvl=1>Front Office Support</a>'; break;
								case "8"	: echo '<a href = view_soal.php?lvl=8>BE Support</a>'; break;
								case "44"	: echo '<a href = view_soal.php?lvl=44>Team_Leader_BES</a>'; break;
								case "60"	: echo '<a href = view_soal.php?lvl=60>Team_Leader_Plasa</a>'; break;
								case "3"	: echo '<a href = view_soal.php?lvl=3>Frontliner_Plasa</a>'; break;
							}
						} else if($set_id == 2) {
							switch ($row_chk_soal[0]){
								case "1"	: echo '<a href = view_soal.php?lvl=1>AGENT POSTPAID TECHNICAL</a>'; break;
								case "2"	: echo '<a href = view_soal.php?lvl=2>AGENT POSTPAID SALES</a>'; break;
								case "3"	: echo '<a href = view_soal.php?lvl=3>AGENT POSTPAID BILLINGCC</a>'; break;
								case "4"	: echo '<a href = view_soal.php?lvl=4>AGENT PREPAID TECHNICAL</a>'; break;
								case "5"	: echo '<a href = view_soal.php?lvl=5>AGENT PREPAID SALES</a>'; break;
								case "10"	: echo '<a href = view_soal.php?lvl=10>AGENT MKIOS</a>'; break;
								case "41"	: echo '<a href = view_soal.php?lvl=41>AGENT_PREPAID_NONTECHNICAL</a>'; break;
								case "42"	: echo '<a href = view_soal.php?lvl=42>AGENT_POSTPAID_NONTECHNICAL</a>'; break;
								case "43"	: echo '<a href = view_soal.php?lvl=43>AGENT_OUTBOUND_CALL</a>'; break;
								case "7"	: echo '<a href = view_soal.php?lvl=7>OTHERS SEGMENT</a>'; break;
								case "47"	: echo '<a href = view_soal.php?lvl=47>AGENT_WEBSERVICE</a>'; break;
								case "48"	: echo '<a href = view_soal.php?lvl=48>CHO Ecare</a>'; break;
								case "49"	: echo '<a href = view_soal.php?lvl=49>TEAM_LEADER</a>'; break;
								case "50"	: echo '<a href = view_soal.php?lvl=50>Agent_Facebook</a>'; break;
								case "51"	: echo '<a href = view_soal.php?lvl=51>Agent_Twitter</a>'; break;
								case "52"	: echo '<a href = view_soal.php?lvl=52>Agent_Email</a>'; break;
								case "53"	: echo '<a href = view_soal.php?lvl=53>Agent_Livechat</a>'; break;
								case "54"	: echo '<a href = view_soal.php?lvl=54>Agent_Whatsapp</a>'; break;
								case "55"	: echo '<a href = view_soal.php?lvl=55>Agent_SMS1111</a>'; break;
								case "56"	: echo '<a href = view_soal.php?lvl=56>Agent_Loopnesia</a>'; break;
								case "57"	: echo '<a href = view_soal.php?lvl=57>Agent_MyTelkomselWeb</a>'; break;
								case "58"	: echo '<a href = view_soal.php?lvl=58>Agent_Priority</a>'; break;
								case "59"	: echo '<a href = view_soal.php?lvl=59>Agent_Corporate</a>'; break;
							}
						} else if ($set_id == 3) {
							
							if ($row_chk_soal[0] == $row_slc_lvl_3['0']) {
								echo "<a href = view_soal.php?lvl=".$row_chk_soal[0].">".$row_slc_lvl_3['1']."</a>";
							}
											
						}
						?>
						</div>
						</td>
					</tr>
				<?php
			}	
		}
	?>

	   <tr>
            <td width="587" height="19"><div align="center" class="style1"></div></td>
          </tr>

       </table>


</td>
      </tr>
      <tr>
        <td height="19" valign="top"><table width="585" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#810704">
          <tr>
            <td width="587" height="19"><div align="center" class="style1"></div></td>
          </tr>
        </table>

</td>
      </tr>
    </table>
	
	</td>
  </tr>
</table>

</body>
</html>
