<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Upload Soal PNP</title>
<script language="javascript">
<!--

function CloseWindow() {

  window.close();

}
//-->
</script>

<style type="text/css">
<!--
.style3 {color: #FFFF00}
-->
</style>
</head>
<style type="text/css">
<!--
.style1 {
	font-size: 16px;
	font-weight: bold;
	color: #FFFFFF;
}
body {
	background-color: #FFFFFF;
}
.style2 {
	color: #FFFF00;
	font-weight: bold;
}
-->
</style>
<body>
<?php   

	include "connection.php";
	
	$get_score = "SELECT unit_id, unit_name, DAY(DATE), score FROM service_outage.outage_score WHERE DATE BETWEEN '2010-04-01' AND '2010-04-31' ORDER BY unit_id, DAY(DATE) ASC;";
	$res_get_score = mysql_query($get_score);
	$jml_array = mysql_num_rows($res_get_score);
	
	if ($res_get_score) {
		while ($row_get_score = mysql_fetch_array($res_get_score)) {
			$data[$row_get_score[0]][$row_get_score[2]] = $row_get_score[3];
		}
	}
	echo count($data[3]);
	echo $data[3][2];
	

?>

<table  border="1" align="center" cellpadding="1" cellspacing="0">
  <tr>
    	<td valign="middle"> Grapari </td>
	<td valign="middle">
		<table border="0" align="center" cellpadding="1" cellspacing="0">
  			<tr>
    				<td align="center" valign="middle"> Maret </td>
  			</tr>
		</table>
		<table border="1" align="center" cellpadding="1" cellspacing="0">
  			<tr>
			<?php
				for($i = 1; $i <= 31; $i++) {
					echo "<td>".$i."</td>";
				}
			?>
  			</tr>
		</table>
	</td>
  </tr>
  
	<?php
		$slc_unit_name = "SELECT DISTINCT unit_id, unit_name FROM service_outage.outage_score WHERE DATE BETWEEN '2010-04-01' AND '2010-04-31' ORDER BY unit_id ASC;";
		$res_slc_unit_name = mysql_query($slc_unit_name);
		
		if ($res_slc_unit_name) {
			while ($row_slc_unit_name = mysql_fetch_array($res_slc_unit_name)) {
				echo "<tr><td>".$row_slc_unit_name[1]."</td></tr>";
				
			/*	for($s = 0; $s <= $jml_array; $s++) {
					echo
				}
			*/
			}
		}
	?>
</table>



</body>
</html>