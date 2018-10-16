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
        	$area = trim($_POST["area"]);
    	}
    	else{
        	if (($_REQUEST["bln"] != "") && ($_REQUEST["thn"] != "") && ($_REQUEST["uid"] != "")){
            	$selmonth = trim($_REQUEST["bln"]);
            	$selyear = trim($_REQUEST["thn"]);
            	$area = trim($_REQUEST["rid"]);
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

	$gatesms = new DBGateway($conf->Host192,$conf->User192, $conf->Pass192);
        $gatesms->OpenConnection();
        
        if (($area != "0") && ($area != "")){
        	
        	$grapsql = "SELECT 
							unit_id, 
							unit_name,
							reg_code,
							reg_name 
						FROM 
							csdm2.t_unit_branch u,
							csdm2.t_unit_area a
						WHERE 
							area = $area and 
							unit_type = 'plasa' and
							u.regional = reg_code 
						ORDER BY 3,2 ";
						
		  	$grapres = $gatecsdm->ExecuteQuery($grapsql,"csdm2");
        	
        	$period = $selyear.str_pad($selmonth,2,'0',STR_PAD_LEFT);
        	
        	$nbgrap = 0;
		  	$totalcsrall = 0;
		  	$totalcsrallsms1 = 0;
		  	$totalcsrallsms2 = 0;
		  	$nbcsrall = 0;
		  	$nbcsrsmsna = 0;

		  	
		  	$arrOfplasaKPI = array();
		  	$arrOfplasaInRegional = array();
		  	$arrOfRegional = array();
		  	
		  	
	  	 	while($rowgrap = mysql_fetch_row($grapres)){
	  	 		
	  	 		if (!array_key_exists($rowgrap[2],$arrOfplasaInRegional)){
	  	 			$arrOfplasaInRegional[$rowgrap[2]] = array();
	  	 			$arrOfRegional[$rowgrap[2]] = $rowgrap[3];
	  	 		}
	  	 		
	  	 		$arrOfplasaInRegional[$rowgrap[2]][$rowgrap[0]] = $rowgrap[0];
        	
	        	if ($period < date("Ym")){
	        		$cssql ="SELECT 
								employee_code, 
								employee_name 
							FROM 
								csdm2.t_employee_history_summary  
							WHERE 
								unit_id = ".$rowgrap[0]." and 
								unit_type = 'plasa' and 
								period = '$period' and 
								job_function in (SELECT job_value FROM csdm2.t_employee_jobfunction WHERE pnp_assessee = '1') and 
								((end_contract >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or end_contract = '0000-00-00') and 
								(reg_datetime < '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-25' or start_contract = '0000-00-00') and 
								(unreg_datetime >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or unreg_datetime is null or unreg_datetime = '0000-00-00 00:00:00'))";
	       		}
	        	else{
	        		$cssql ="SELECT 
								employee_code, 
								employee_name 
							FROM 
								csdm2.t_employee  
							WHERE 
								unit_id = ".$rowgrap[0]." and 
								unit_type = 'plasa' and 
								job_function in (SELECT job_value FROM csdm2.t_employee_jobfunction WHERE pnp_assessee = '1') and 
								((end_contract >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or end_contract = '0000-00-00') and 
								(reg_datetime < '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-25' or start_contract = '0000-00-00') and 
								(unreg_datetime >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or unreg_datetime is null or unreg_datetime = '0000-00-00 00:00:00'))";
	        	}
	        	
	        	//echo $cssql;
				
				$scoresql = "SELECT 
								employee_id, 
								score, 
								nb_done 
							FROM 
								pnp.score 
							WHERE 
								period = '$selyear".str_pad($selmonth,2,'0',STR_PAD_LEFT)."' and 
								set_id = 1 and 
								assess_id = 'REG' ";


				$smsscoresql = "SELECT 
						employee_id, 
						SUM(IF(quiz_seq = 1, nb_question, 0)) AS nb_question_1,
						SUM(IF(quiz_seq = 1, nb_done, 0)) AS nb_done_1, 
						SUM(IF(quiz_seq = 1, score, 0)) AS score_1,
						SUM(IF(quiz_seq = 2, nb_question, 0)) AS nb_question_2,
						SUM(IF(quiz_seq = 2, nb_done, 0)) AS nb_done_2, 
						SUM(IF(quiz_seq = 2, score, 0)) AS score_2
					FROM sms.quiz_score 
					WHERE period = $period AND unit_id = ".$rowgrap[0]." and period >= 200912
					GROUP BY 1";
								
				
				$csres = $gatecsdm->ExecuteQuery($cssql,"csdm2");
	
				$arrOfCS = array();
	            while($row = mysql_fetch_row($csres)){
	            	$arrOfCS[$row[0]] = $row[1];
	           	}
	           	
	           	$scoreres = $gatepnp->ExecuteQuery($scoresql,"pnp");
	
				$arrOfScore = array();
	            while($row = mysql_fetch_row($scoreres)){
	            	$arrOfScore[strtolower($row[0])] = $row[1]."#".$row[2];
	             }

			$smsquizscore = $gatesms->ExecuteQuery($smsscoresql ,"sms");


			$arrOfSMSScore1 = array();
		$arrOfSMSScore2 = array();

		while ($row = mysql_fetch_row($smsquizscore)){
		$arrOfSMSScore1[strtolower($row[0])] = $row[1]."#".$row[2]."#".$row[3];
		$arrOfSMSScore2[strtolower($row[0])] = $row[4]."#".$row[5]."#".$row[6];

		}
	             
            	$i=0;
 				$total = 0;
				$nbcsr = 0;
				
                $totalsmscs1 = 0;
		$totalsmscs2 = 0;
                foreach($arrOfCS as $csid => $csname)
                {
                	list($score, $soal) = split("#",$arrOfScore[$csid]);
                     if($score == "") {$score = 0;}
                     if($soal == "") {$soal = 0;}
                     $total += $score;

			list($jmlsoal1, $dijawab1, $smsscore1) = split("#", $arrOfSMSScore1[$csid]);
			if($smsscore1== "") {$smsscore1= 0;}
                     if($dijawab1== "") {$dijawab1= 0;}

			$totalsmscs1 += $smsscore1;

			list($jmlsoal2, $dijawab2, $smsscore2) = split("#", $arrOfSMSScore2[$csid]);
			if($smsscore2== "") {$smsscore2= 0;}
                     if($dijawab2== "") {$dijawab2= 0;}

			$totalsmscs2 += $smsscore2;


                     $nbcsr +=1;
		}
				
				if ($nbcsr > 0){
			$kpicsr = round($total/$nbcsr,2);
			if ($period >= 200912){
				$kpicsrsms1 = round($totalsmscs1/$nbcsr,2);
				$kpicsrsms2 = round($totalsmscs2/$nbcsr,2);
			}
			else{
				$kpicsrsms1 = "na";
				$kpicsrsms2 = "na";
				$nbcsrsmsna++; 
			}

		}
		else{
			$kpicsr = "na";
			$kpicsrsms1 = "na";
			$kpicsrsms2 = "na";
			$nbcsrsmsna++;
		}

				
		$totalcsrall+=$total;
		$totalcsrallsms1+=$totalsmscs1;
		$totalcsrallsms2+=$totalsmscs2;
		$nbcsrall+=$nbcsr;

		$nbgrap++;
				
		if ($kpicsr === "na" && $kpicsrsms1 === "na" && $kpicsrsms2 === "na"){
			$kpi= "na";
		}
		else{
			//echo $rowgrap[1].$kpibo;

			$kpi= round((0.8*$kpicsr + 0.2*($kpicsrsms1 + $kpicsrsms2)/2),2);
						
		}
				
		$arrOfplasaKPI[$rowgrap[0]] = $rowgrap[1]."#".$nbcsr."#".$kpicsrsms1."#".$kpicsrsms2."#".$kpicsr."#".$kpi;

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
  						<a href="report_per_plasa.php">Per Plasa</a><br />
  						<a href="report_per_regional_plasa.php">Per Regional</a><br />
  						Per Area<br />
  						<a href="report_nasional_plasa.php">Nasional</a>
		  			</div>
					  <div id="userscores">
  						<span style="text-decoration:underline;">Top 10 Frontliner : </span><br />
  						<a href="report_per_regional_plasa_top10.php">Regional</a> <br />
  						<a href="report_per_area_plasa_top10.php">Area</a> <br />
  						<a href="report_nasional_plasa_top10.php">Nasional</a>
		  			</div>	
				</div>
	  			<br class="clear"/>
	  			<?php
 			if (($area != "0") && ($area != "")){
				$i=0;
  				$total = 0;
				echo "<table style='margin-right:5px;'>
						<caption>area $area</caption>
                	<thead>	
				      <tr>
							<th width='35%'>Gerai</th>
							<th width='13%'>Peserta</th>
							<th width='13%'>PNP</th>
							<th width='13%'>SMS Quiz I</th>
							<th width='13%'>SMS Quiz II</th>
							<th width='13%'>KPI</th>
						</tr></thead><tbody>";
    		foreach ($arrOfplasaInRegional as $regname => $arrOfUnit){
    			echo "<tr><th colspan='6' class='regional'>".$arrOfRegional[$regname]."</th></tr>";
    			foreach ($arrOfUnit as $unitid => $unitids){
    				list($gname, $nbcsr, $kpicsrsms1,$kpicsrsms2, $kpicsr, $kpi) = split("#", $arrOfplasaKPI[$unitid]);
    				echo "<tr>
    							<th>$gname</th>
    							<td>$nbcsr</td>
    							<td>$kpicsr</td>
    							<td>$kpicsrsms1</td>
    							<td>$kpicsrsms2</td>
							<td>$kpi</td>
  							</tr>
							";
   				}
			}


				    ?>

					<tfoot>
				<tr align="right">
                            <td><b>Total </b></td>
				<td><?php echo $nbcsrall ;?></td>
				<td><?php if($nbcsrall > 0){$kpicsr = round($totalcsrall/$nbcsrall,2);}else{$kpicsr = "na";} echo $kpicsr;?></td>
				<td><?php if($nbcsrall > 0){if($nbgrap == $nbcsrsmsna){$kpicsrsms1 = "na";}else{$kpicsrsms1 = round($totalcsrallsms1/$nbcsrall,2);}}else{$kpicsrsms1 = "na";} echo $kpicsrsms1 ;?></td>
				<td><?php if($nbcsrall > 0){if($nbgrap == $nbcsrsmsna){$kpicsrsms2 = "na";}else{$kpicsrsms2 = round($totalcsrallsms2/$nbcsrall,2);}}else{$kpicsrsms2 = "na";} echo $kpicsrsms2 ;?></td>

				<td>

				<?php 
								
					if ($kpicsr === "na" && $kpicsrsms1 === "na" && $kpicsrsms2 === "na"){
						$kpi= "na";
					}
					else{
						$kpi= round((0.8*$kpicsr + 0.2*($kpicsrsms1 + $kpicsrsms2)/2),2);
					}
							
					echo $kpi;?></td>

                        </tr>
                        </tfoot>

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

	     		$logger->Logging(HistoryType::HISTORYTYPE_VIEW_REPORT_PLASA_AREA,$userid,"View Report Per Area Plasa $area Periode $selmonth - $selyear",$ipaddress);
	     		
					}
					?>   	
			</div><!--left column-->

			<div id="right_column"><!--right column-->
  				<div class="section"><!--latest news-->
  					<p class="highlight">Area</p>
  					<form action="report_per_area_plasa.php" method="post">
  					<?php
				try{
  					$channel = "SELECT DISTINCT
					  				area_code, 
					  				area_name 
					  			FROM 
								  csdm2.t_unit_area 
					  			WHERE
					  				area_code <> 0 
					  			ORDER BY 1 asc";
						    
				    $plasares = $gatecsdm->ExecuteQuery($channel,"csdm2");
				    
				    echo "<select name='area' style='margin-left:10px;padding:2px;font-family:\"Trebuchet MS\", Arial;font-size:13px;width:180px;'>\n";
            		
					while($row = mysql_fetch_row($plasares)){
                		if ($area == $row[0]){
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
				        for($i=2006;$i<=2012;$i++){
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
