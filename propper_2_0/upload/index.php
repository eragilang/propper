<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>

<?php
include "connection.php";

class Score {
	public static function show($bln='', $thn='') {
		
		$bln = is_int($bln) ? $bln : date('m');
		$thn = is_int($thn) ? $thn : date('y');
		
		$time = mktime(0,0,0, $bln, 1, $thn);
		$days = date('t', $time);
		
		$slc_score = "SELECT unit_id, unit_name, day(date), score FROM outage_score WHERE date BETWEEN '".$thn."-".$bln."-01' AND '".$thn."-".$bln."-31' order by unit_id, day(date) ASC;";
		
		$res_slc_score = mysql_query($slc_score);
		while($row_slc_score = mysql_fetch_array($res_slc_score)) {
			$data[$row_slc_score[0]][$row_slc_score[2]] = $row_slc_score[3]; 
		}
		
		$slc_unit_name = "SELECT DISTINCT unit_id, unit_name FROM outage_score WHERE date BETWEEN '".$thn."-".$bln."-01' AND '".$thn."-".$bln."-31' order by unit_id ASC;";
				
		?>
		<table align = "center" width="1100" border="1" cellpadding="3" cellspacing="3">
			<tr>
				<td align="center" bgcolor="#810704"><b><font color="yellow">Grapari</font></b></td>		
				<?php
				for($i = 0; $i < 6; $i++) {
					for($j = 2; $j <= 7; $j++) {
						$d = $j + $i*7 - date('w', $time);
						if($d > 0 && $d <= $days) {
							echo '<td bgcolor="#810704"  align="center" width="20"><b><font color="yellow">';		
							echo $d;
							print '</font></b></td>';
						}
					}
					if($td >= $days && $i != 6) {
						break;
					}
				}
				?>
			<?php // </td> ?>

			</tr>
		<?php	// <tr> ?>
		
			<?php
				
			$flag = 0;
			$res_slc_unit_name = mysql_query($slc_unit_name);
			
			while($row_slc_unit_name = mysql_fetch_array($res_slc_unit_name)) {
				echo '<tr>';
				echo '<td bgcolor="#610704"><font color="yellow" high="30">'.$row_slc_unit_name[1].'</font></td>';
				
				for($i = 0; $i < 6; $i++) {
					for($j = 2; $j <= 7; $j++) {
						$d = $j + $i*7 - date('w', $time);
						if($d > 0 && $d <= $days) {
							if($data[$row_slc_unit_name[0]][$d] == 100) {
								echo '<td bgcolor="#210704">';		
								echo '<b><font color="white">'.$data[$row_slc_unit_name[0]][$d].'</font></b>';							
								print '</td>';
							} else {
								print '<td bgcolor="#ffffff"><b><center><font color="blue">0</font></center></b></td>';
							}
						}
					}
					
					if($td >= $days && $i != 6) {
						break;
					}
				}
								
				echo '</tr>';
			}
			?>
		<?php	// </tr> ?>
		</table>
	<?php
	}
}

Score::show();
?>


</body>
</html>
