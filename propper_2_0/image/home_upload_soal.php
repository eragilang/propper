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
		
		<form name="form" action="process_upload_soal.php" method="post" enctype="multipart/form-data">
  
<table width="581" border="0" align="center" cellpadding="7" cellspacing="0">

<?php 
include "connection.php";

?>
			<tr>
    			<td width="102">Unit Type <span class="style2">*</span> </td>
    			<td width="451">
				<select name="unittype" onchange="getChange();" id="unittype">
					<option selected>--
					<option value="GRAPARI">Walk-In</option>
					<option value="CALLCENTER">Callcenter</option>
					<option value="WEBSERVICE">eCare</option>
				</select>
				</td>
    		</tr>
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

  <tr>
    <td>File Upload <span class="style2">*</span> </td>
    <td><input name="file_source" type="file" class="edt" id="file_source" value="<?php $file_source?>" size="45"></td>
    </tr>
  <tr>
    <td></td>
    <td></td>
  </tr>
 </table>
<table> 
<?php 
include "connection.php";
include_once('lib/zMysql.php');
	
	// check soal apakah sudah ada di question_active
	$slc_qs_active 	= "SELECT * FROM dev_job_value"
						." WHERE unit_type = 'GRAPARI'";
	
	$res_slc_qs_active = fetchData($slc_qs_active);
	##
	$slc_qs_active1 	= "SELECT * FROM dev_job_value"
						." WHERE unit_type = 'CALLCENTER'";
	
	$res_slc_qs_active1 = fetchData($slc_qs_active1);
	##
	$slc_qs_active2 	= "SELECT * FROM dev_job_value"
						." WHERE unit_type = 'WEBSERVICE'";
	
	$res_slc_qs_active2 = fetchData($slc_qs_active2);
	//print_r ($slc_qs_active);
	
	print_r($row_slc_qs_active);
echo "<div>
	<tr style='display: none' id='bobot_grapari'>
		<td><b>Bobot grapari</b><span class='style2'>*</span> </td>
    </tr>
	<tr style='display: none' id='head_grapari'>
		<td>Jenis Soal</td><td>Team Leader</td><td>Supervisor</td><td>Agent</td>
    </tr>
	<tr style='display: none' id='proses_grapari'>
		<td>proses</td>
		<td><input name='tl_gpr_proses' type='textnum' value='".$res_slc_qs_active[0]['bobot_nilai']."' size='5'></td>
		<td><input name='spv_gpr_proses' type='textnum' value='".$res_slc_qs_active[1]['bobot_nilai']."' size='5'></td>
		<td><input name='cs_gpr_proses' type='textnum' value='".$res_slc_qs_active[2]['bobot_nilai']."' size='5'></td>
    </tr>
	<tr style='display: none' id='produk_grapari'>
		<td>produk</td>
		<td><input name='tl_gpr_produk' type='textnum' value='".$res_slc_qs_active[3]['bobot_nilai']."' size='5'></td>
		<td><input name='spv_gpr_produk' type='textnum' value='".$res_slc_qs_active[4]['bobot_nilai']."' size='5'></td>
		<td><input name='cs_gpr_produk' type='textnum' value='".$res_slc_qs_active[5]['bobot_nilai']."' size='5'></td>
    </tr>
	<tr style='display: none' id='perilaku_grapari'>
		<td>perilaku</td>
		<td><input name='tl_gpr_perilaku' type='textnum' value='".$res_slc_qs_active[6]['bobot_nilai']."' size='5'></td>
		<td><input name='spv_gpr_perilaku' type='textnum' value='".$res_slc_qs_active[7]['bobot_nilai']."' size='5'></td>
		<td><input name='cs_gpr_perilaku' type='textnum' value='".$res_slc_qs_active[8]['bobot_nilai']."' size='5'></td>
	</div>
	<div>
	<tr style='display: none' id='bobot_cc'>
		<td><b>Bobot Callcenter</b><span class='style2'>*</span> </td>
    </tr>
	<tr style='display: none' id='head_cc'>
		<td>Jenis Soal</td><td>Team Leader</td><td>Supervisor</td><td>Agent</td>
    </tr>
	<tr style='display: none' id='proses_cc'>
		<td>proses</td>
		<td><input name='tl_cc_proses' type='textnum' value='".$res_slc_qs_active1[0]['bobot_nilai']."' size='5'></td>
		<td><input name='spv_cc_proses' type='textnum' value='".$res_slc_qs_active1[1]['bobot_nilai']."' size='5'></td>
		<td><input name='cs_cc_proses' type='textnum' value='".$res_slc_qs_active1[2]['bobot_nilai']."' size='5'></td>
    </tr>
	<tr style='display: none' id='produk_cc'>
		<td>produk</td>
		<td><input name='tl_cc_produk' type='textnum' value='".$res_slc_qs_active1[3]['bobot_nilai']."' size='5'></td>
		<td><input name='spv_cc_produk' type='textnum' value='".$res_slc_qs_active1[4]['bobot_nilai']."' size='5'></td>
		<td><input name='cs_cc_produk' type='textnum' value='".$res_slc_qs_active1[5]['bobot_nilai']."' size='5'></td>
    </tr>
	<tr style='display: none' id='perilaku_cc'>
		<td>perilaku</td>
		<td><input name='tl_cc_perilaku' type='textnum' value='".$res_slc_qs_active1[6]['bobot_nilai']."' size='5'></td>
		<td><input name='spv_cc_perilaku' type='textnum' value='".$res_slc_qs_active1[7]['bobot_nilai']."' size='5'></td>
		<td><input name='cs_cc_perilaku' type='textnum' value='".$res_slc_qs_active1[8]['bobot_nilai']."' size='5'></td>
	</div>
	<div>
	<tr style='display: none' id='bobot_ec'>
		<td><b>Bobot webservice</b><span class='style2'>*</span> </td>
    </tr>
	<tr style='display: none' id='head_ec'>
		<td>Jenis Soal</td><td>Team Leader</td><td>Supervisor</td><td>Agent</td>
    </tr>
	<tr style='display: none' id='proses_ec'>
		<td>proses</td>
		<td><input name='tl_ec_proses' type='textnum' value='".$res_slc_qs_active2[0]['bobot_nilai']."' size='5'></td>
		<td><input name='spv_ec_proses' type='textnum' value='".$res_slc_qs_active2[1]['bobot_nilai']."' size='5'></td>
		<td><input name='cs_ec_proses' type='textnum' value='".$res_slc_qs_active2[2]['bobot_nilai']."' size='5'></td>
    </tr>
	<tr style='display: none' id='produk_ec'>
		<td>produk</td>
		<td><input name='tl_ec_produk' type='textnum' value='".$res_slc_qs_active2[3]['bobot_nilai']."' size='5'></td>
		<td><input name='spv_ec_produk' type='textnum' value='".$res_slc_qs_active2[4]['bobot_nilai']."' size='5'></td>
		<td><input name='cs_ec_produk' type='textnum' value='".$res_slc_qs_active2[5]['bobot_nilai']."' size='5'></td>
    </tr>
	<tr style='display: none' id='perilaku_ec'>
		<td>perilaku</td>
		<td><input name='tl_ec_perilaku' type='textnum' value='".$res_slc_qs_active2[6]['bobot_nilai']."' size='5'></td>
		<td><input name='spv_ec_perilaku' type='textnum' value='".$res_slc_qs_active2[7]['bobot_nilai']."' size='5'></td>
		<td><input name='cs_ec_perilaku' type='textnum' value='".$res_slc_qs_active2[8]['bobot_nilai']."' size='5'></td>
	</div>";
	?>
	<tr>
    <td></td>
    <td></td>
  </tr>
  	<tr> 
    	<td>
		<input name="submit" type="submit" value="Upload" /></td>
    </tr>
</table>
</form>
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
<script>
		function getChange(){
			var type = document.getElementById('unittype').options[document.getElementById('unittype').selectedIndex].value;
			
			if (type=="GRAPARI"){
				document.getElementById("bobot_grapari").style.display = "";
				document.getElementById("head_grapari").style.display = "";
				document.getElementById("proses_grapari").style.display = "";
				document.getElementById("produk_grapari").style.display = "";
				document.getElementById("perilaku_grapari").style.display = "";
				document.getElementById("bobot_cc").style.display = "none";
				document.getElementById("head_cc").style.display = "none";
				document.getElementById("proses_cc").style.display = "none";
				document.getElementById("produk_cc").style.display = "none";
				document.getElementById("perilaku_cc").style.display = "none";
				document.getElementById("bobot_ec").style.display = "none";
				document.getElementById("head_ec").style.display = "none";
				document.getElementById("proses_ec").style.display = "none";
				document.getElementById("produk_ec").style.display = "none";
				document.getElementById("perilaku_ec").style.display = "none";
			}else if (type=="CALLCENTER"){
				document.getElementById("bobot_cc").style.display = "";
				document.getElementById("head_cc").style.display = "";
				document.getElementById("proses_cc").style.display = "";
				document.getElementById("produk_cc").style.display = "";
				document.getElementById("perilaku_cc").style.display = "";
				document.getElementById("bobot_ec").style.display = "none";
				document.getElementById("head_ec").style.display = "none";
				document.getElementById("proses_ec").style.display = "none";
				document.getElementById("produk_ec").style.display = "none";
				document.getElementById("perilaku_ec").style.display = "none";
				document.getElementById("bobot_grapari").style.display = "none";
				document.getElementById("head_grapari").style.display = "none";
				document.getElementById("proses_grapari").style.display = "none";
				document.getElementById("produk_grapari").style.display = "none";
				document.getElementById("perilaku_grapari").style.display = "none";
			}else{
				document.getElementById("bobot_ec").style.display = "";
				document.getElementById("head_ec").style.display = "";
				document.getElementById("proses_ec").style.display = "";
				document.getElementById("produk_ec").style.display = "";
				document.getElementById("perilaku_ec").style.display = "";
				document.getElementById("bobot_cc").style.display = "none";
				document.getElementById("head_cc").style.display = "none";
				document.getElementById("proses_cc").style.display = "none";
				document.getElementById("produk_cc").style.display = "none";
				document.getElementById("perilaku_cc").style.display = "none";
				document.getElementById("bobot_grapari").style.display = "none";
				document.getElementById("head_grapari").style.display = "none";
				document.getElementById("proses_grapari").style.display = "none";
				document.getElementById("produk_grapari").style.display = "none";
				document.getElementById("perilaku_grapari").style.display = "none";
			}
		};
</script>
