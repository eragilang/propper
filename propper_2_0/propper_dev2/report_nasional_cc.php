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
    	}
    	else{
        	if (($_REQUEST["bln"] != "") && ($_REQUEST["thn"] != "") && ($_REQUEST["uid"] != "")){
            	$selmonth = trim($_REQUEST["bln"]);
            	$selyear = trim($_REQUEST["thn"]);
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
        
        if (isset($_POST["btnReport"])){
        	
        	$grapsql = "SELECT 
							unit_id, 
							unit_name,
							reg_code,
							reg_name 
						FROM 
							csdm2.t_unit_branch u,
							csdm2.t_unit_area a
						WHERE 
							unit_type = 'callcenter' and
							u.regional = reg_code 
						ORDER BY 3,2 ";
						
		  	$grapres = $gatecsdm->ExecuteQuery($grapsql,"csdm2");
        	
        	$period = $selyear.str_pad($selmonth,2,'0',STR_PAD_LEFT);
        	
        	$nbgrap = 0;
		  	$totalcsrall = 0;
		  	$totalboall = 0;
			$totalcsrallsms1 = 0;
		  	$totalboallsms1 = 0;
			$totalcsrallsms2 = 0;
		  	$totalboallsms2 = 0;
		  	$nbcsrall = 0;
		  	$nbboall = 0;
		  	$nbnotassessboall = 0;
		  	$nbnotassesscsrall = 0;
			$nbnotassessboallsms = 0;
		  	$nbnotassesscsrallsms = 0;
			$nbbosmsna = 0;
			$nbcsrsmsna = 0;
		  	
		  	$arrOfShopKPI = array();
		  	$arrOfShopInRegional = array();
		  	$arrOfRegional = array();
		  	
	  	 	while($rowgrap = mysql_fetch_row($grapres)){
	  	 		
	  	 		if (!array_key_exists($rowgrap[2],$arrOfShopInRegional)){
	  	 			$arrOfShopInRegional[$rowgrap[2]] = array();
	  	 			$arrOfRegional[$rowgrap[2]] = $rowgrap[3];
	  	 		}
	  	 		
	  	 		$arrOfShopInRegional[$rowgrap[2]][$rowgrap[0]] = $rowgrap[0];
        	
	        	if ($period < date("Ym")){
	        		$cssql ="SELECT 
								employee_code, 
								employee_name 
							FROM 
								csdm2.t_employee_history_summary  
							WHERE 
								unit_id = ".$rowgrap[0]." and 
								unit_type = 'callcenter' and 
								period = '$period' and 
								job_function in (SELECT job_value FROM csdm2.t_employee_jobfunction WHERE pnp_assessee = '1') and 
								((end_contract >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or end_contract = '0000-00-00') and 
								(reg_datetime < '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-01' or start_contract = '0000-00-00') and 
								(unreg_datetime >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or unreg_datetime is null or unreg_datetime = '0000-00-00 00:00:00'))";
								
					$bosql = "SELECT 
						login_id, 
						full_username, 
						employee_code 
					FROM 
						csdm2.t_bo_grapari_history_summary 
					WHERE 
						login_type = 'callcenter' AND 
						job_functioncode in ('STAFF','SPV') AND 
						unit_id = ".$rowgrap[0]." and
						period = $period
					ORDER BY 2";
	       		}
	        	else{
	        		$cssql ="SELECT 
								employee_code, 
								employee_name 
							FROM 
								csdm2.t_employee  
							WHERE 
								unit_id = ".$rowgrap[0]." and 
								unit_type = 'callcenter' and 
								job_function in (SELECT job_value FROM csdm2.t_employee_jobfunction WHERE pnp_assessee = '1') and 
								((end_contract >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or end_contract = '0000-00-00') and 
								(reg_datetime < '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-01' or start_contract = '0000-00-00') and 
								(unreg_datetime >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or unreg_datetime is null or unreg_datetime = '0000-00-00 00:00:00'))";
								
									
				$bosql = "SELECT 
							login_id, 
							full_username, 
							employee_code 
						FROM 
							t_user 
						WHERE 
							login_type = 'callcenter' AND 
							job_functioncode in ('STAFF','SPV') AND 
							unit_id = ".$rowgrap[0];
	        	}
				
				$scoresql = "SELECT 
								employee_id, 
								score, 
								nb_done 
							FROM 
								pnp.score 
							WHERE 
								period = '$selyear".str_pad($selmonth,2,'0',STR_PAD_LEFT)."' and 
								set_id = 2 and 
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
	           	
	           	$bores = $gatecsdm->ExecuteQuery($bosql,"csdm2");
	
				$arrOfBO = array();
				while($row = mysql_fetch_row($bores)){
					$arrOfBO[strtolower($row[0])] = $row[1]."#".$row[2];
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

		$period = $selyear.str_pad($selmonth,2,'0',STR_PAD_LEFT);
	             
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
				
		$nbnotassessbo = 0;
		$nbnotassessbosms = 0;

		$total = 0;
		$totalsmsbo1 = 0;
		$totalsmsbo2 = 0;
		$nbbo = 0;
		$i=0;
				
		foreach($arrOfBO as $boid => $boname)
                {
			list($bofullname, $bonik) = split("#", $boname);

                     list($score, $soal) = split("#",$arrOfScore[$boid]);
                     if($score == "") {$score = 0;}
                     if($soal == "") {$soal = 0;}
                     $total += $score;

			list($jmlsoal1, $dijawab1, $smsscore1) = split("#", $arrOfSMSScore1[$bonik]);
			if($smsscore1== "") {$smsscore1= 0;}
                     if($dijawab1== "") {$dijawab1= 0;}

			$totalsmsbo1 += $smsscore1;

			list($jmlsoal2, $dijawab2, $smsscore2) = split("#", $arrOfSMSScore2[$bonik]);
			if($smsscore2== "") {$smsscore2= 0;}
                     if($dijawab2== "") {$dijawab2= 0;}

			$totalsmsbo2 += $smsscore2;


                     $nbbo +=1;
		}
				
				if ($nbbo > 0){
					$kpibo = round($total/$nbbo,2);
					if ($period >= 200912){
						$kpibosms1 = round($totalsmsbo1 /$nbbo,2);						
						$kpibosms2 = round($totalsmsbo2 /$nbbo,2);
					}
						else{
							$kpibosms1 = "na";
							$kpibosms2 = "na";
							$nbbosmsna++; 
						}


				}
				else{
					$kpibo = "na";
					$kpibosms1 = "na";
					$kpibosms2 = "na";
					$nbbosmsna++;
				}


				$nbboall+=$nbbo;
				$totalboall+=$total;
				$totalboallsms1+=$totalsmsbo1;
				$totalboallsms2+=$totalsmsbo2;

				$nbgrap++;
				
				if ($kpibo === "na" && $kpicsr === "na" && $kpicsrsms1 === "na" && $kpibosms1 === "na"){

						$kpi= "na";
					}
					else{
						//echo $rowgrap[1].$kpibo;

						$bobotbo = 100;
						$bobotcsr = 100;
						if ($kpibo === "na"){$bobotbo -= 80;}
						if ($kpibosms1 === "na"){$bobotbo -= 20;}
						
						if ($kpicsr === "na"){$bobotcsr -= 80;}
						if ($kpicsrsms1 === "na"){$bobotcsr -= 20;}

						$bobotoverall = 100;
						if ($bobotbo == 0){$bobotoverall -= 25;}
						if ($bobotcsr == 0){$bobotoverall -= 75;}

						if ($bobotoverall > 0){
							if (($bobotbo > 0) && ($bobotcsr > 0)){
								$kpi= round(0.75*(0.8*$kpicsr + 0.2*($kpicsrsms1 + $kpicsrsms2)/2)*100/$bobotcsr + 0.25*(0.8*$kpibo + 0.2*($kpibosms1 + $kpibosms2)/2)*100/$bobotbo,2);
							}
							else if ($bobotbo > 0){
								$kpi= round((0.8*$kpibo + 0.2*($kpibosms1 + $kpibosms2)/2)*100/$bobotbo,2);
							}
							else{
								$kpi= round((0.8*$kpicsr + 0.2*($kpicsrsms1 + $kpicsrsms2)/2)*100/$bobotcsr,2);
							}
						}
					}				//echo "<br/>";
				
				$arrOfShopKPI[$rowgrap[0]] = $rowgrap[1]."#".$nbcsr."#".$kpicsrsms1."#".$kpicsrsms2."#".$kpicsr."#".$nbbo."#".$kpibosms1."#".$kpibosms2."#".$kpibo."#".$kpi;
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
  						<a href="report_per_area_grapari.php">Per Area</a><br />
  						Nasional
		  			</div>
					  <div id="userscores">
  						<span style="text-decoration:underline;">Top 10 Frontliner : </span><br />
  						<a href="report_per_regional_grapari_top10.php">Regional</a> <br />
  						<a href="report_per_area_grapari_top10.php">Area</a> <br />
  						<a href="report_nasional_grapari_top10.php">Nasional</a>
		  			</div>	
				</div>
	  			<br class="clear"/>
	  			<?php
	  			
	  			if (isset($_POST["btnReport"])){
				$i=0;
  				$total = 0;
				echo "<table style='margin-right:5px;'>
						<caption>area $area</caption>
                	<thead>
						<tr>
							<th width='28%' rowspan='2'>Shop</th>
							<th colspan='2'>Peserta</th>
							<th colspan='2'>PNP</th>
							<th colspan='2'>SMS Quiz I</th>
							<th colspan='2'>SMS Quiz II</th>
							<th rowspan='2' width='8%'>KPI</th>
						</tr>	
				      <tr>
					  	<th width='8%'>BO</th>
	  					<th width='8%'>CSR</th>
		  				<th width='8%'>BO</th>
  					 	<th width='8%'>CSR</th>
						<th width='8%'>BO</th>
  					 	<th width='8%'>CSR</th>
	  					<th width='8%'>BO</th>
		  				<th width='8%'>CSR</th>
		     		</tr></thead><tbody>";
    		foreach ($arrOfShopInRegional as $regname => $arrOfUnit){
    			echo "<tr><th colspan='10' class='regional'>".$arrOfRegional[$regname]."</th></tr>";
    			foreach ($arrOfUnit as $unitid => $unitids){
    				list($gname, $nbcsr, $kpicsrsms1, $kpicsrsms2, $kpicsr, $nbbo, $kpibosms1, $kpibosms2, $kpibo, $kpi) = split("#", $arrOfShopKPI[$unitid]);
    				echo "<tr>
    							<th>$gname</th>
    							<td>$nbbo</td>
    							<td>$nbcsr</td>
    							<td>$kpibo</td>
    							<td>$kpicsr</td>
    							<td>$kpibosms1</td>
    							<td>$kpicsrsms1</td>
							<td>$kpibosms2</td>
    							<td>$kpicsrsms2</td>
    							<td>$kpi</td>
  							</tr>
							";
    			}
   			}
   			
   			
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

	     		$logger->Logging(HistoryType::HISTORYTYPE_VIEW_REPORT_GRAPARI_NASIONAL,$userid,"View Report Nasional Callcenter Periode $selmonth - $selyear",$ipaddress);


				    ?>

					<tfoot>
				<tr align="right">
                            <td><b>Total </b></td>
				<td><?php echo $nbboall ;?></td>
                            <td><?php echo $nbcsrall ;?></td>
				<td><?php if($nbboall > 0){$kpibo = round($totalboall/$nbboall,2);}else{$kpibo = "na";} echo $kpibo;?></td>
				<td><?php if($nbcsrall > 0){$kpicsr = round($totalcsrall/$nbcsrall,2);}else{$kpicsr = "na";} echo $kpicsr;?></td>
				<td><?php if($nbboall > 0){if($nbgrap == $nbbosmsna){$kpibosms1 = "na";}else{$kpibosms1 = round($totalboallsms1/$nbboall,2);}}else{$kpibosms1 = "na";} echo $kpibosms1 ;?></td>
				<td><?php if($nbcsrall > 0){if($nbgrap == $nbcsrsmsna){$kpicsrsms1 = "na";}else{$kpicsrsms1 = round($totalcsrallsms1/$nbcsrall,2);}}else{$kpicsrsms1 = "na";} echo $kpicsrsms1 ;?></td>
				<td><?php if($nbboall > 0){if($nbgrap == $nbbosmsna){$kpibosms2 = "na";}else{$kpibosms2 = round($totalboallsms2/$nbboall,2);}}else{$kpibosms2 = "na";} echo $kpibosms2 ;?></td>
				<td><?php if($nbcsrall > 0){if($nbgrap == $nbcsrsmsna){$kpicsrsms2 = "na";}else{$kpicsrsms2 = round($totalcsrallsms2/$nbcsrall,2);}}else{$kpicsrsms2 = "na";} echo $kpicsrsms2 ;?></td>
				<td>
							<?php 
								
								if ($kpibo === "na" && $kpicsr === "na" && $kpicsrsms1 === "na" && $kpibosms1 === "na"){

						$kpi= "na";
					}
					else{
						//echo $rowgrap[1].$kpibo;

						$bobotbo = 100;
						$bobotcsr = 100;
						if ($kpibo === "na"){$bobotbo -= 80;}
						if ($kpibosms1 === "na"){$bobotbo -= 20;}
						
						if ($kpicsr === "na"){$bobotcsr -= 80;}
						if ($kpicsrsms1 === "na"){$bobotcsr -= 20;}

						$bobotoverall = 100;
						if ($bobotbo == 0){$bobotoverall -= 25;}
						if ($bobotcsr == 0){$bobotoverall -= 75;}

						if ($bobotoverall > 0){
							if (($bobotbo > 0) && ($bobotcsr > 0)){
								$kpi= round(0.75*(0.8*$kpicsr + 0.2*($kpicsrsms1 + $kpicsrsms2)/2)*100/$bobotcsr + 0.25*(0.8*$kpibo + 0.2*($kpibosms1 + $kpibosms2)/2)*100/$bobotbo,2);
							}
							else if ($bobotbo > 0){
								$kpi= round((0.8*$kpibo + 0.2*($kpibosms1 + $kpibosms2)/2)*100/$bobotbo,2);
							}
							else{
								$kpi= round((0.8*$kpicsr + 0.2*($kpicsrsms1 + $kpicsrsms2)/2)*100/$bobotcsr,2);
							}
						}
					}							
							echo $kpi;?></td>

                        </tr>
                        				</tfoot>
                    </table>

			     <br class='spacer' />
                    
                    ket : * : belum melakukan assessment 
					
					<?php
					}
					?> 	
			</div><!--left column-->

			<div id="right_column"><!--right column-->
  				<div class="section"><!--latest news-->
  					<form action="report_nasional_cc.php" method="post">
  					
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

<?php
function lastday($month = '', $year = '') {
   if (empty($month)) {
      $month = date('m');
   }
   if (empty($year)) {
      $year = date('Y');
   }

   if ($month < 12){
	$month = $month + 1;
   }
   else{
	$month = 1;
	$year = $year + 1;
   }
   return date('Y-m-d', mktime(0, 0, 0, $month, 0, $year));
}
?>
