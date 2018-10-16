<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=report_proper.xls");
include "connection.php";
include_once('lib/zMysql.php');
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
		$query2 = "select employee_code from csdm2.t_employee where end_working='0000-00-00 00:00:00' ".$where."";
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
	//print_r ($_GET);
	echo "<table>
	<tr>
		<td><b>REPORT PROPPER</b></td>
	<tr>
		<td> Period </td>
		<td align='left'>: ".$_GET['period']." </td>
	<tr>
	<tr>
		<td> UNIT TYPE </td>
		<td align='left'>: ".$_GET['unittype']." </td>
	<tr>
	<tr>
		<td> UNIT NAME </td>
		<td align='left'>: ".$_GET['unitname']." </td>
	<tr>
	</table>
	<table border='1' cellspacing='1'>
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
	".$content1."";
	echo "</table>";
	?>
</table>