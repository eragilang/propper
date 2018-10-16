<?php
include "connection.php";
include_once('lib/zMysql.php');

$area = intval($_GET['area']);
$period = intval($_GET['period']);
$region = intval($_GET['region']);
$unittype = intval($_GET['unittype']);
$unitname = intval($_GET['unitname']);
$proses = $_GET['proses'];
$where = null;
$target = '';
if($_GET['unittype'] == 'callcenter' || $_GET['unittype'] == 'webservice'){
	$target = "90 %";
} else if ($_GET['unittype'] == 'gerai' || $_GET['unittype'] == 'grapari' || $_GET['unittype'] == 'plasa' || $_GET['unittype'] == 'regional' ){
	$target = "95 %";
}
//echo "{$target} - {$_GET['unittype']}";
//echo $unittype;
switch($proses){
case 'getSummary' :
		 $query = "select employee_id from pnp.score where period='".$_GET['period']."'";
		 //echo $query;
		 $res = fetchData($query);
		 foreach($res as $key=>$row){
			 if($key>0){
				$coma = ",";
				}else{
					$coma = "";
				}
				$employee .= $coma."'".$row['employee_id']."'";
				}
				if(count($res)==''){
					$employee = "'99'";
				}
		 if ($_GET['area']=='' AND $_GET['region']=='' AND $_GET['unittype']=='' AND $_GET['unitname']==''){
			 $where ="AND employee_code in (".$employee.")";
		 } 
		 if($_GET['unittype']<>'') {
			  $where .="AND unit_type='".$_GET['unittype']."'";
		 } 
		 if ($_GET['unitname']<>''){
			 $where .="AND unit_name='".$_GET['unitname']."'";
		 }
		 // if ($_GET['area']<>'' AND $_GET['region']<>'' AND $_GET['unittype']<>'' AND $_GET['unitname']<>''){
			 // $where ="AND unit_type='".$_GET['unittype']."' AND unit_name='".$_GET['unitname']."'";
		 // }
		$query2 = "select employee_code from csdm2.t_employee where end_working='0000-00-00 00:00:00' ".$where;
		//echo $query2;
		$res2 = fetchData($query2);
		foreach($res2 as $key2=>$row2){
			if($key2>0){
				$coma = ",";
				}else{
					$coma = "";
				}
				$karId .= $coma."'".$row2['employee_code']."'";
		}
			if(count($res2)==''){
					$karId = "'99'";
				}
		$query3 = "select sum(a.score) as score,count(a.employee_id) as employee_id,a.level,b.job_function from pnp.score a JOIN csdm2.t_employee b ON a.employee_id = b.employee_code where a.period='".$_GET['period']."' and a.employee_id in (".$karId.") group by a.level,b.job_function 
		union 
		select sum(a.score) as score,count(a.employee_id) as employee_id,a.level,b.job_function from pnp.cc_score a JOIN csdm2.t_employee b ON a.employee_id = b.employee_code where a.period='".$_GET['period']."' and a.employee_id in (".$karId.") group by a.level,b.job_function";
		//echo $query3;
		$res3 = fetchData($query3);
		foreach($res3 as $key3=>$row3){
			$total = $row3['score']/$row3['employee_id'];
				$content .= "<tr>
								  <td align='center'>".$row3['job_function']."</td>
								  <td align='left'>".$target."</td>
								  <td align='left'>".substr($total,0,5)." %</td>
						</tr>";
		}
	 
		echo "<div><h3> Report Based on Job Function </h3>";
		echo "<table class='zui-table'>
		<thead>
		<tr>
		<th>JOB FUCTION</th>
		<th>TARGET</th>
		<th>SCORE</th>
		</tr>
		</thead>
		".$content."";
		echo "</table>";
		echo "<div id='detail' onclick='getAll()'><b><u> Show Detail </b></u>";

break;
case 'getAll' :
		 $query = "select employee_id from pnp.score where period='".$_GET['period']."'";
		 $res = fetchData($query);
		 foreach($res as $key=>$row){
			 if($key>0){
				$coma = ",";
				}else{
					$coma = "";
				}
				$employee .= $coma."'".$row['employee_id']."'";
				}
				if(count($res)==''){
					$employee = "'99'";
				}
		 if ($_GET['area']=='' AND $_GET['region']=='' AND $_GET['unittype']=='' AND $_GET['unitname']==''){
			 $where ="AND employee_code in (".$employee.")";
		 } 
		 if($_GET['unittype']<>'') {
			  $where .="AND unit_type='".$_GET['unittype']."'";
		 } 
		 if ($_GET['unitname']<>''){
			 $where .="AND unit_name='".$_GET['unitname']."'";
		 }
		$query2 = "select employee_code from csdm2.t_employee where end_working='0000-00-00 00:00:00' ".$where." order by unit_name asc";
		$res2 = fetchData($query2);
		foreach($res2 as $key2=>$row2){
			if($key2>0){
					$coma = ",";
				}else{
					$coma = "";
				}
				$karId .= $coma."'".$row2['employee_code']."'";
		}
			if(count($res2)==''){
					$karId = "'99'";
			}
		
		
		$query5 = "SELECT unit_name,area,regional FROM csdm2.t_unit_branch where unit_type in ('callcenter','grapari','plasa','gerai','webservice', 'regional')";
		$res5 = fetchData($query5);
		foreach($res5 as $row5){
			$area1[$row5['unit_name']] = $row5['area'];
			$regional[$row5['unit_name']] = $row5['regional'];
		}
		$query4 = "select a.*,b.* from pnp.score a JOIN csdm2.t_employee b ON a.employee_id = b.employee_code where a.period='".$_GET['period']."' and a.employee_id in (".$karId.")
		union
		select a.*,b.* from pnp.cc_score a JOIN csdm2.t_employee b ON a.employee_id = b.employee_code where a.period='".$_GET['period']."' and a.employee_id in (".$karId.")";
		//echo $query4;
		$res4 = fetchData($query4);
		$no=1;
		foreach($res4 as $key4=>$row4){
			//$score[$row3['level']['job_function']] = $row3['score'];
				$content1 .= "<tr class=rowcontent id='detailCont_".$no."'>
								  <td align='center'>".$row4['employee_id']."</td>
								  <td align='center'>".$row4['employee_name']."</td>
								  <td align='center'>".$row4['job_function']."</td>
								  <td align='center'>".$row4['unit_type']."</td>
								  <td align='center'>".$row4['unit_name']."</td>
								  <td align='center'>".$area1[$row4['unit_name']]."</td>
								  <td align='center'>".$regional[$row4['unit_name']]."</td>
								  <td align='center'>".$row4['score']."</td>
						</tr>";
						$no++;
		}
	echo "<div><h3> Detail Report </h3>";
	echo "<a href='prosesExcel.php?area=".$_GET['area']."&period=".$_GET['period']."&region=".$_GET['region']."&unittype=".$_GET['unittype']."&unitname=".$_GET['unitname']."'>Export ke Excell</a><br>";
	echo "<div id='detail' onclick='showBack()'><b><u><i> Back </i></u></b></div>";
	echo "<table class='zui-table'>
	<thead>
	<tr>
	<th>NIK</th>
	 <th>Name</th>
	 <th>Job Function</th>
	 <th>Unit Type</th>
	 <th>Unit Name</th>
	 <th>Area</th>
	 <th>Regional</th>
	 <th>Score</th>
	</tr>
	</thead>
	".$content1."";
	echo "</table>";
		
break;
case 'getRegion' :
	## get regional
	$query5 = "SELECT reg_code,reg_name FROM csdm2.t_unit_area where area_code='".$_GET['area']."' order by reg_code ASC";
	$res5 = fetchData($query5);
	$regional[0]="Pilih Data";
		foreach($res5 as $row5){
			$regional[$row5['reg_code']] = $row5['reg_name'];
		}
	echo json_encode($regional);
break;
case 'getUnitname' :
	## get regional
	$query5 = "SELECT distinct unit_name FROM csdm2.t_unit_branch where unit_type='".$_GET['unittype']."' order by unit_name ASC";
	$res5 = fetchData($query5);
	$Unitname[""]="Pilih Data";
	//echo "<script>alert('$query5');</script>";
		foreach($res5 as $row5){
			$Unitname[$row5['unit_name']] = $row5['unit_name'];
		}
	echo json_encode($Unitname);
break;
}
?>