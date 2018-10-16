<?php
	require_once ("class/DBgateway.php");
    require_once ("class/ConfigReader.php");
    
    $conf = new ConfigReader();
	date_default_timezone_set("Asia/Jakarta");
    
    try{
    	$dbgate = new DBGateway($conf->Host237,$conf->User237,$conf->Pass237);
    	$dbgate->OpenConnection();
    	
    	if (date("j") >= 15){
    		$period = date("Ym");
    	}
    	else{
    		if (date("n") == "12"){
    			$period = (date("Y")-1)."01";
    		}
    		else{
    			$period = date("Ym")-1;
    		}
    	}
    	
    	$query = "SELECT 
					employee_id, 
					score, 
					(SELECT 
						AVG(duration) 
					FROM 
						score_detail d 
					WHERE 
						s.employee_id = d.employee_id AND 
						period = $period) AS duration 
				FROM 
					score s 
				WHERE 
					period = $period AND
					set_id = 2 AND
					employee_id like '0%'
				ORDER BY 
					score DESC, 
					duration
				LIMIT 0, 20";
				
		$top20res = $dbgate->executeQuery($query,"pnp");
		
		$arrOfScore = array();
		$employeeID = "";
		
		while ($row = mysql_fetch_row($top20res)){
			$arrOfScore[$row[0]] = $row[1];
			$employeeID .= "'".$row[0]."',";
		}
		
		$employeeID .= "''";
		
		$dbgate->CloseConnection();
		
		$dbgate2 = new DBGateway($conf->Host251,$conf->User251,$conf->Pass251);
    	$dbgate2->OpenConnection();
    	
    	$query = "SELECT
					employee_code, 
					employee_name, 
					job_function, 
					u.unit_name
				FROM
					csdm2.t_employee e,
					csdm2.t_unit_branch u
				WHERE
					u.unit_id = e.unit_id and
					employee_code in ($employeeID) 
				";
				
		//echo $query;
		
		$arrOfEmployee = array();
		$empres = $dbgate2->executeQuery($query,"csdm2");
		
		while ($row = mysql_fetch_row($empres)){
			$arrOfEmployee[$row[0]] = $row[1]."#".$row[2]."#".$row[3];
		}
		
		//print
		echo "<br/>";
		echo "<table>";
		echo "<caption>Top 20 Agent</caption>";
		echo "<thead>";
		echo "	<tr>";
		echo "	  <th>Nama</td>";
		echo "	  <th>Nilai</td>";
		echo "	</tr>";
		echo "</thead>";
		echo "<tbody>";
		foreach ($arrOfScore as $nik => $score){
			echo " <tr>";
			echo "	<th width='80%'>";
			list($name,$job, $unit) = split("#",$arrOfEmployee[$nik]);
			echo "<a href='#'>$name</a><br/>$unit<br/>$job";
			echo "	</td>";
			echo "	<td width='20%'>$score";
			echo "	</td>";
			echo " <tr>";
		}
		echo "</tbody>";
		echo "</table>";
		
    }
    catch (Exception $e){
    	echo "Failed to connect to DB (".$e->getMessage().")";
    }
?>
