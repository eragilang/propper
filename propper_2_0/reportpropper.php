
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Report Propper</title>
<body>
<div><h2>Report Propper</h2></div>
<style>
.zui-table {
    border: solid 1px #F08080;
    border-collapse: collapse;
    border-spacing: 0;
    font: normal 13px Arial, sans-serif;
}
.zui-table thead th {
    background-color: #B22222;
    border: solid 1px #F08080;
    color: #F0FFF0;
    padding: 10px;
    text-align: left;
}
.zui-table tbody td {
    border: solid 1px #F08080;
    color: #333;
    padding: 10px;
    text-shadow: 1px 1px 1px #fff;
}
body {
    background-color:#F2F3F4;
}
.form-submit-button {
background: #B22222;
color: white;
border-style: outset;
border-color: #B22222;
height: 40px;
width: 80px;
font: bold 15px arial, sans-serif;
text-shadow:none;

}
</style>
<?php 
include "connection.php";
include_once('lib/zMysql.php');
	
	// get periode
	$slc 	= "SELECT distinct period FROM score order by period desc";
	$qres=mysql_query($slc) or die(mysql_error($slc));
	$optPeriod ="<option value=''>Pilih data</option>";
	while($rDat=mysql_fetch_assoc($qres))
	{
		$optPeriod .="<option value='".$rDat['period']."'>".$rDat['period']."</option>";
	}
	
	// get area
	$slc_qs_active 	= "SELECT distinct area_code,area_name FROM csdm2.t_unit_area";
	$res=mysql_query($slc_qs_active) or die(mysql_error($slc_qs_active));
	$optArea ="<option value=''>Seluruhnya</option>";
	while($rData=mysql_fetch_assoc($res))
	{
		$optArea .="<option value='".$rData['area_code']."'>".$rData['area_name']."</option>";
	}
	## get regional
	$slc_qs_active1 	= "SELECT reg_code,reg_name FROM csdm2.t_unit_area";
	$res1=mysql_query($slc_qs_active1) or die(mysql_error($slc_qs_active1));
	$optReg ="<option value=''>Seluruhnya</option>";
	while($rData1=mysql_fetch_assoc($res1))
	{
		$optReg .="<option value='".$rData1['reg_code']."'>".$rData1['reg_name']."</option>";
	}
	##
	// get area
	$slc_qs_active2 = "SELECT distinct unit_type FROM csdm2.t_unit_branch where unit_type in ('grapari','gerai','plasa','callcenter','webservice', 'regional')";
	$res2=mysql_query($slc_qs_active2) or die(mysql_error($slc_qs_active2));
	$optUnittype ="<option value=''>seluruhnya</option>";
	while($rData2=mysql_fetch_assoc($res2))
	{
		$optUnittype .="<option value='".$rData2['unit_type']."'>".$rData2['unit_type']."</option>";
	}
	//echo $slc_qs_active2;
	// get 
	$slc_qs_active3 	= "SELECT distinct unit_name FROM csdm2.t_unit_branch where unit_type in ('grapari','gerai','plasa','callcenter','webservice', 'regional')";
	$res3=mysql_query($slc_qs_active3) or die(mysql_error($slc_qs_active3));
	$optKar ="<option value=''>Seluruhnya</option>";
	while($rData3=mysql_fetch_assoc($res3))
	{
		$optKar .="<option value='".$rData3['unit_name']."'>".$rData3['unit_name']."</option>";
	}
echo "<div>
	<table class='zui-table'>
	<tr>
	   <td>Periode</td>
	   <td><select id=period name=period style=\"width:150px;\" >".$optPeriod."</select></td>
	 </tr>
	 <tr>
	   <td>Area</td>
	   <td><select id=area name=area onchange='getRegion();' style=\"width:150px;\" >".$optArea."</select></td>
	 </tr>
	 <tr>
	   <td>Regional</td>
	   <td><select id=region name=region style=\"width:150px;\" >".$optReg."</select></td>
	 </tr>
	  <tr>
	   <td>Unit Type</td>
	   <td><select id=unittype name=unittype onchange='getUnitname();' style=\"width:150px;\" >".$optUnittype."</select></td>
	 </tr>
	 <tr>
	   <td>Unit Name</td>
	   <td><select id=unitname name=unitname style=\"width:150px;\" >".$optKar."</select></td>
	 </tr>
	 </table>
	 <br></br>
	 <div>
		<input class='form-submit-button' type=submit name=submit onclick='showDetail()'/></td>
	</div>
	 
	 
	 <br></br>
	 <div id='txtHint'></div>
	 <div id='txtHint1'></div>
	</div>";
	?>
	</body>
<script>
function showDetail() {
  var area = document.getElementById('area').options[document.getElementById('area').selectedIndex].value,
		period = document.getElementById('period').options[document.getElementById('period').selectedIndex].value,
		unittype = document.getElementById('unittype').options[document.getElementById('unittype').selectedIndex].value,
		region = document.getElementById('region').options[document.getElementById('region').selectedIndex].value,
		unitname = document.getElementById('unitname').options[document.getElementById('unitname').selectedIndex].value,
		param = "area="+area+"&period="+period+"&region="+region+"&unittype="+unittype+"&unitname="+unitname;
		if (period == ''){
			alert('Periode harus diisi');
			exit();
		}
		if (unittype == ''){
			alert('Unit Type harus diisi');
			exit();
		}
		//alert(param);
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      document.getElementById("txtHint").innerHTML=this.responseText;
    }
  }
  xmlhttp.open("GET","report_slave_propper.php?area="+area+"&period="+period+"&region="+region+"&unittype="+unittype+"&unitname="+unitname+"&proses=getSummary",true);
  xmlhttp.send();
}
function getAll() {
  var area = document.getElementById('area').options[document.getElementById('area').selectedIndex].value,
		period = document.getElementById('period').options[document.getElementById('period').selectedIndex].value,
		unittype = document.getElementById('unittype').options[document.getElementById('unittype').selectedIndex].value,
		region = document.getElementById('region').options[document.getElementById('region').selectedIndex].value,
		unitname = document.getElementById('unitname').options[document.getElementById('unitname').selectedIndex].value,
		param = "area="+area+"&period="+period+"&region="+region+"&unittype="+unittype+"&unitname="+unitname;
		//alert(param);
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      document.getElementById("txtHint1").innerHTML=this.responseText;
	  document.getElementById("txtHint").style.display='none';
    }
  }
  xmlhttp.open("GET","report_slave_propper.php?area="+area+"&period="+period+"&region="+region+"&unittype="+unittype+"&unitname="+unitname+"&proses=getAll",true);
  xmlhttp.send();
}

function getRegion() {
  var area = document.getElementById('area').options[document.getElementById('area').selectedIndex].value,
	  region= document.getElementById('region');
		//alert(param);
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
   // document.getElementById("region").options[document.getElementById('region').selectedIndex].innerHTML=this.responseText;
		eval('var res='+this.responseText);
		region.options.length=0;
		for(i in res){
			region.options[region.options.length] = new Option(res[i],i);
		}
    }
  }
  xmlhttp.open("GET","report_slave_propper.php?area="+area+"&proses=getRegion",true);
  xmlhttp.send();
}

function getUnitname() {
  var area = document.getElementById('area').options[document.getElementById('area').selectedIndex].value,
	  region = document.getElementById('region').options[document.getElementById('region').selectedIndex].value,
	  unittype = document.getElementById('unittype').options[document.getElementById('unittype').selectedIndex].value,
	  unitname= document.getElementById('unitname');
		//alert(param);
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
   // document.getElementById("region").options[document.getElementById('region').selectedIndex].innerHTML=this.responseText;
		eval('var res='+this.responseText);
		unitname.options.length=0;
		for(i in res){
			unitname.options[unitname.options.length] = new Option(res[i],i);
		}
    }
  }
  xmlhttp.open("GET","report_slave_propper.php?area="+area+"&region="+region+"&unittype="+unittype+"&proses=getUnitname",true);
  xmlhttp.send();
}

function showBack() {
	document.getElementById("txtHint").style.display='';
	document.getElementById("txtHint1").style.display='none';
}
</script>