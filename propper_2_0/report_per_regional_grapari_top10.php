<?php
	require_once ("class/DBgateway.php");
    require_once ("class/ConfigReader.php");
    require_once ("class/Logger.php");
    
    $conf = new ConfigReader();
    
    try{
    	$unitid = 0;
    	$tanggal =date("d-F-Y");
    	$bulan=date('M');
    	$bulan_a=date('m');
    	$tahun=date('Y');

    	if (isset($_POST["btnReport"])){
        	$selmonth = trim($_POST["bulan"]);
        	$selyear = trim($_POST["tahun"]);
        	$regional = trim($_POST["regional"]);
    	}
    	else{
        	if (($_REQUEST["bln"] != "") && ($_REQUEST["thn"] != "") && ($_REQUEST["uid"] != "")){
            	$selmonth = trim($_REQUEST["bln"]);
            	$selyear = trim($_REQUEST["thn"]);
            	$regional = trim($_REQUEST["rid"]);
        	}
        	else{
            	$selmonth = $bulan_a;
            	$selyear = $tahun;
        	}
    	}
    	
    	$gatepnp = new DBGateway($conf->Host237,$conf->User237, $conf->Pass237);
        $gatepnp->OpenConnection();
        
        $gatecsdm = new DBGateway($conf->Host251,$conf->User251, $conf->Pass251);
        $gatecsdm->OpenConnection();
        
        if (($regional != "0") && ($regional != "")){
        	
        	$period = $selyear.str_pad($selmonth,2,'0',STR_PAD_LEFT);
        	
        	$query = "SELECT 
						employee_id, 
						score, 
						IF(DATE_FORMAT(NOW(),'%Y%m') = $period,
						(SELECT 
							AVG(duration) 
						FROM 
							pnp.score_detail d 
						WHERE 
							s.employee_id = d.employee_id AND 
							period = $period),
						(SELECT 
							AVG(duration) 
						FROM 
							pnp.score_detail_archive d 
						WHERE 
							s.employee_id = d.employee_id AND 
							period = $period)) AS duration  
					FROM 
						pnp.score s 
					WHERE 
						period = $period AND
						set_id = 1 and
						unit_id in (SELECT unit_id FROM pnp.t_unit_branch WHERE unit_type = 'grapari' and regional = $regional) AND
						employee_id like '0%'
					ORDER BY 
						score DESC, 
						duration
					LIMIT 0, 10";
	
			//echo $query;
					
			$top10res = $gatepnp->executeQuery($query,"pnp");
			
			$arrOfScoreGrap = array();
			$employeeID = "";
			
			while ($row = mysql_fetch_row($top10res)){
				$arrOfScoreGrap[$row[0]] = $row[1];
				$employeeID .= "'".$row[0]."',";
			}
			
			$employeeID .= "''";
			
			$query = "SELECT
						employee_code, 
						employee_name, 
						job_function, 
						u.unit_name,
						reg_name
					FROM
						csdm2.t_employee e,
						csdm2.t_unit_branch u,
						csdm2.t_unit_area a
					WHERE
						u.unit_id = e.unit_id and
						employee_code in ($employeeID) and
						u.regional = reg_code 
					";
					
			//echo $query;
					
			$arrOfEmployee = array();
			$empres = $gatecsdm->executeQuery($query,"csdm2");
			
			while ($row = mysql_fetch_row($empres)){
				$arrOfEmployee[$row[0]] = $row[1]."#".$row[2]."#".$row[3]."#".$row[4];
			}
        }
    }
    catch (Exception $e){
    	
    }	
  	 		
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en"><head> 
	<title>:: Product Knowledge and Procedure</title>
  	<link href="style/global.css" rel="stylesheet" type="text/css" media="all"/>
  	<link href="style/report.css" rel="stylesheet" type="text/css" media="all"/>
  	<!--[if lt IE 7.]>
    	<script defer type='text/javascript' src='js/fixpng.js''></script>
	<![endif]-->
</head>
<body>
	<div id="page_container"><!--page container-->
		<div id="header"><!--header-->
  			<div id="logo">
			  <h1 title="PnP Home"><a href="index.php">PnP Assessment</a></h1></div>
  			
		</div><!--header-->
		
		<ul id="navigation"><!--navigation-->
  			<li>
		  		<a href="report_per_cc.php" id="callcenter" title="Report Call Center">
				  <span>Call Center</span>
			  	</a>
  			</li>
  			<li>
			  	<a href="report_per_grapari.php" id="grapari" title="Report GraPARI">
				  <span>GraPARI</span>
				  </a>
			  </li>
  			<li>
			  <a href="report_per_gerai.php" id="gerai" title="Report Gerai">
			  	<span>Gerai</span>
			  </a>
  			</li>
  			<li>
			  <a href="report_per_plasa.php" id="plasa" title="Report Plasa">
			  <span>Contact</span></a></li>
		</ul><!--navigation-->

		<div id="page_content_assess"><!--content-->
			<div id="left_column"><!--left column-->
				<div id="sectiond">
  					<div id="userinfos">
  						<a href="report_per_grapari.php">Per Grapari</a><br />
  						<a href="report_per_regional_grapari.php">Per Regional</a><br />
  						<a href="report_per_area_grapari.php">Per Area</a> <br />
  						<a href="report_nasional_grapari.php">Nasional</a>
		  			</div>
					  <div id="reporttop10">
  						<span style="text-decoration:underline;">Top 10 Frontliner : </span><br />
  						Regional<br />
  						<a href="report_per_area_grapari_top10.php">Area</a> <br />
  						<a href="report_nasional_grapari_top10.php">Nasional</a>
		  			</div>	
				</div>
	  			<br class="clear"/>
	  			<?php
 			if (($regional != "0") && ($regional != "")){
				$i=0;
  				$total = 0;
				echo "<table style='margin-right:5px;'>
						<caption>Top 10 Regional $regional</caption>
                	<thead>	
				      <tr>
					  	<th width='45%'>Nama</th>
					  	<th width='15%'>NIK</th>
	  					<th width='25%'>Info</th>
  					 	<th width='15%'>Nilai</th>
		     		</tr></thead><tbody>";
		     		
    			foreach($arrOfScoreGrap as $nik => $score){
    				list($name,$job, $unit, $regname) = split("#",$arrOfEmployee[$nik]);
    				echo "<tr>
    							<th>$name</th>
    							<td>$nik</td>
    							<td>$regname<br/>$unit</td>
    							<td>$score</td>
  							</tr>
							";
   				}


				    ?>
                    </table>

			     <br class='spacer' />
                    
                    ket : * : belum melakukan assessment
					<?php
					
				$ipaddress = "";

	            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
	                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	            }
	            else{
	                $ipaddress = $_SERVER['REMOTE_ADDR'];
	            }

	            $logger = new Logger();
	            $logger->setDBConnection($gatepnp);
		     	$userid = $_SESSION['uSeRiD'];
		     	if(trim($userid) == ""){$userid = "{Anonymous}";}

	     		$logger->Logging(HistoryType::HISTORYTYPE_VIEW_REPORT_GRAPARI_REGIONAL_TOP10,$userid,"View Report per Regional GraPARI $regional Periode $selmonth - $selyear",$ipaddress);
					}
					?>   	
			</div><!--left column-->

			<div id="right_column"><!--right column-->
  				<div class="section"><!--latest news-->
  					<p class="highlight">Regional</p>
  					<form action="report_per_regional_grapari_top10.php" method="post">
  					<?php
				try{
  					$channel = "SELECT 
					  				reg_code, 
					  				reg_name 
					  			FROM 
								  csdm2.t_unit_area 
					  			WHERE
					  				reg_code <> 0 
					  			ORDER BY 1 asc";
						    
				    $geraires = $gatecsdm->ExecuteQuery($channel,"csdm2");
				    
				    echo "<select name='regional' style='margin-left:10px;padding:2px;font-family:\"Trebuchet MS\", Arial;font-size:13px;width:180px;'>\n";
            		
					while($row = mysql_fetch_row($geraires)){
                		if ($regional == $row[0]){
                    		echo "<option value='".$row[0]."' style='margin:3px;' selected>".$row[1]."</option>\n";
                		}
                		else{
                    		echo "<option value='".$row[0]."' style='margin:3px;'>".$row[1]."</option>\n";
                		}
            		}
            		
					echo "</select>\n";
				}
				catch (Exception $e){
					
				}
  					
  					?>
    				<p class="highlight">Bulan</p>
    				<select name = 'bulan' style='margin-left:10px;padding:2px;font-family:"Trebuchet MS", Arial;font-size:13px;width:180px;'>
				       <option value="1" <? if($selmonth == 1){echo "selected";} ?>>Januari</option>
				       <option value="2" <? if($selmonth == 2){echo "selected";} ?>>Februari</option>
				       <option value="3" <? if($selmonth == 3){echo "selected";} ?>>Maret</option>
				       <option value="4" <? if($selmonth == 4){echo "selected";} ?>>April</option>
				       <option value="5" <? if($selmonth == 5){echo "selected";} ?>>Mei</option>
				       <option value="6" <? if($selmonth == 6){echo "selected";} ?>>Juni</option>
				       <option value="7" <? if($selmonth == 7){echo "selected";} ?>>Juli</option>
				       <option value="8" <? if($selmonth == 8){echo "selected";} ?>>Agustus</option>
				       <option value="9" <? if($selmonth == 9){echo "selected";} ?>>September</option>
				       <option value="10" <? if($selmonth == 10){echo "selected";} ?>>Oktober</option>
				       <option value="11" <? if($selmonth == 11){echo "selected";} ?>>November</option>
				       <option value="12" <? if($selmonth == 12){echo "selected";} ?>>Desember</option>
</select>
    				<p class="highlight">Tahun</p>
    				<select name='tahun' style='margin-left:10px;padding:2px;font-family:"Trebuchet MS", Arial;font-size:13px;width:180px;'>
				    <?
				        for($i=2006;$i<=2017;$i++){
				            if ($i == $selyear){
				                ?>
				                <option value="<? echo $i;?>" selected> <? echo $i;?></option>
				                <?
				            }
				            else{
				                ?>
				                <option value="<? echo $i;?>" > <? echo $i;?></option>
				                <?
				            }
				        }
				    ?>
</select>

				<br />
				<button type="submit" id="btnReport" name="btnReport" style="margin-left:10px;"></button>
				</form>
    				
  			</div><!--latest news-->

  			
		</div><!--right column-->
		<div class="clear">&nbsp;</div>
	</div><!--content-->

	<div id="footer"><!--footer-->
  		<p id="copyright">Copyright © 2009 Developed by Tools and Data Management Departement</p>
	</div><!--footer-->

</div><!--page container-->

</body></html>
