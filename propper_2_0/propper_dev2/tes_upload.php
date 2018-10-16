<?php
		session_start();
		if($_SESSION['uSeRiD'] == ""){
    		$homelink = "index.php";
    	} else{
    		$homelink = "main.php";
    	}
		
    	$getid = $_REQUEST["idrand"];
    	$trueid = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$_REQUEST["rnum"].$_SESSION["prevpage"]),0,10));
		echo $getid."-".$trueid."-".$_SESSION['aCCeSSRiGHT']."-".$_SESSION['unitSetIdSoal']."-".$_SESSION['unitRegSoal'];
    
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
		catch (Exception $e){  	
		}	  	 		
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Upload Soal PROPPER</title>

<script>
	var agent;
	
	function getAgent(){
		agent = document.getElementsByName("agentType")[0].value;
		if(agent == 'in'){
			document.getElementById('level').innerHTML="<option value='991'>Priority</option>"+
														"<option value='992'>Corporate</option>"+
														"<option value='993'>Regular</option>";
		}else{
			document.getElementById('level').innerHTML="<option value='994'>Sales</option>"+
														"<option value='995'>Caring</option>"+
														"<option value='996'>Collection</option>"
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
        <td width="587" height="41" align="center"><span class="style9">PROPPER</span></td>
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
              <td width="587" height="19"><div align="center" class="style1">Upload Soal PROPPER </div></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td height="427" valign="top">
		
		<form name="form" action="proses_upload_propper.php" method="post" enctype="multipart/form-data">
  
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

?>
			<tr>
    			<td width="102">Jenis Soal <span class="style2">*</span> </td>
    			<td width="451">
				<select name="jenisSoal">
					<option selected>--
					<option value='produk'>Produk
					<option value='proses'>Proses
					<option value='perilaku'>Perilaku
				</select>
				</td>
    		</tr>
<?php /*\
	<tr>
    			<td width="102">Level <span class="style2">*</span> </td>
    			<td width="451">
					<select id="level" name="level">
					</select>
				</td>
    		</tr>
  <tr>
    <td>Job Function <span class="style2">*</span> </td>
    <td><select name="mnJobFunction">
	<option value='0'> Default
	</select></td>
    </tr>
*/ ?>


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
