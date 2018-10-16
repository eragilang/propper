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
        	$unitid = trim($_POST["branch"]);
    	}
    	else{
        	if (($_REQUEST["bln"] != "") && ($_REQUEST["thn"] != "") && ($_REQUEST["uid"] != "")){
            	$selmonth = trim($_REQUEST["bln"]);
            	$selyear = trim($_REQUEST["thn"]);
            	$unitid = trim($_REQUEST["uid"]);
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
        
        if (($unitid != "0") && ($unitid != "")){
        	$period = $selyear.str_pad($selmonth,2,'0',STR_PAD_LEFT);
        	
        	if ($period < date("Ym")){
        		$cssql ="SELECT 
							employee_code, 
							employee_name 
						FROM 
							csdm2.t_employee_history_summary  
						WHERE 
							unit_id = ".$unitid." and 
							unit_type = 'grapari' and 
							period = '$period' and 
							employee_flag = 1 and
							job_function NOT IN ('Front_Office_Support') and
							job_function in (SELECT job_value FROM csdm2.t_employee_jobfunction WHERE pnp_assessee = '1') and 
							((end_contract >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or end_contract = '0000-00-00') and 
							(reg_datetime < '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-01' or start_contract = '0000-00-00') and 
							(unreg_datetime >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or unreg_datetime is null or unreg_datetime = '0000-00-00 00:00:00')) 
						ORDER BY 2";
				
				$bosql = "SELECT 
						login_id, 
						full_username, 
						employee_code 
					FROM 
						csdm2.t_bo_grapari_history_summary 
					WHERE 
						login_type = 'grapari' AND 
						job_functioncode in ('STAFF','SPV') AND 
						unit_id = $unitid and
						period = $period
					ORDER BY 2";
					
					#echo "$bosql";
       		}
        	else{
        		$cssql ="SELECT 
							employee_code, 
							employee_name 
						FROM 
							csdm2.t_employee  
						WHERE 
							unit_id = ".$unitid." and 
							unit_type = 'grapari' and 
							employee_flag = 1 and 
							job_function NOT IN ('Front_Office_Support') and
							job_function in (SELECT job_value FROM csdm2.t_employee_jobfunction WHERE pnp_assessee = '1') and 
							((end_contract >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or end_contract = '0000-00-00') and 
							(reg_datetime < '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-01' or start_contract = '0000-00-00') and 
							(unreg_datetime >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or unreg_datetime is null or unreg_datetime = '0000-00-00 00:00:00')) 
						ORDER BY 2";
				
				$bosql = "SELECT 
						login_id, 
						full_username, 
						employee_code 
					FROM 
						csdm2.t_user 
					WHERE 
						login_type = 'grapari' AND 
						job_functioncode in ('STAFF','SPV') AND 
						unit_id = $unitid
					ORDER BY 2";
        	}
			
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

			//$datemk = mktime(0,0,0,$selmonth,0,$selyear); 
			//$lastdate = date("Y-m-d", $datemk);

			$smsscoresql = "SELECT 
						employee_id, 
						SUM(IF(quiz_seq = 1, nb_question, 0)) AS nb_question_1,
						SUM(IF(quiz_seq = 1, nb_done, 0)) AS nb_done_1, 
						SUM(IF(quiz_seq = 1, score, 0)) AS score_1,
						SUM(IF(quiz_seq = 2, nb_question, 0)) AS nb_question_2,
						SUM(IF(quiz_seq = 2, nb_done, 0)) AS nb_done_2, 
						SUM(IF(quiz_seq = 2, score, 0)) AS score_2
					FROM sms.quiz_score 
					WHERE period = $period AND unit_id = $unitid and period >= 200912
					GROUP BY 1";
							
			//echo $smsscoresql ;
			$csres = $gatecsdm->ExecuteQuery($cssql,"csdm2");

			$arrOfCS = array();
            while($row = mysql_fetch_row($csres)){
            	$arrOfCS[$row[0]] = $row[1];
           	}
           	
           	//echo $bosql;
           	
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
  						Per Grapari<br />
  						<a href="report_per_regional_grapari.php">Per Regional</a> <br />
  						<a href="report_per_area_grapari.php">Per Area</a> <br />
  						<a href="report_nasional_grapari.php">Nasional</a>
		  			</div>
				  <div id="userscores">
  						<span style="text-decoration:underline;">Top 10 Frontliner : </span><br />
  						<a href="report_per_regional_grapari_top10.php">Regional</a> <br />
  						<a href="report_per_area_grapari_top10.php">Area</a> <br />
							<a href="report_nasional_grapari_top10.php">Nasional</a>
				  </div>	
					<div id="userscores">
  						<span style="text-decoration:underline;">Most Wrong Answer : </span><br />
  						<a href="report_most_wrong_callcenter.php">Per CallCenter</a> <br />
  						<a href="report_most_wrong_grapari.php">Per Grapari</a> <br />
  						<a href="report_most_wrong_gerai.php">Per Gerai</a>
		  		</div>
				
				 
				</div>
	  			<br class="clear"/>
	  			<?php
 			if (($unitid != "0") && ($unitid != "")){
				$i=0;
  				$total = 0;
				echo "<table style='margin-right:5px;'>
						<caption>Back Office</caption>
                	<thead>
					<tr>
						<th width='36%' rowspan='2'>Nama</th>
	  					<th width='8%' rowspan='2'>Nik</th>
		  				<th colspan='2'>PNP</th>
  					 	<th colspan='2'>SMS Quiz 1</th>
  					 	<th colspan='2'>SMS Quiz 2</th>
						<th width='8%' rowspan='2'>KPI</th>
					</tr>	
				      <tr>
					  	<th width='8%'>Nilai</th>
  					 	<th width='8%'>Soal</th>
						<th width='8%'>Nilai</th>
  					 	<th width='8%'>Soal</th>
						<th width='8%'>Nilai</th>
  					 	<th width='8%'>Soal</th>
		     		</tr></thead><tbody>";


				$nbnotassessbo= 0;
				$nbnotsmsbo1= 0;
				 $totalsms1 = 0;
				$nbnotsmsbo2= 0;
				 $totalsms2 = 0;

				$issms1done = 0;
				$issms2done = 0;

				$allkpibo = 0;
                foreach($arrOfBO as $csid => $csinfo)
                {
			   list($name, $nik) = split("#",$csinfo);
                        $name = ucwords(strtolower($name));
			   $name .= " ";
                        if(!array_key_exists($csid, $arrOfScore)){
                            $name .= "*";
                            $nbnotassessbo++;
                        }

                        if(!array_key_exists($nik, $arrOfSMSScore1)){
                            $nbnotsmsbo1++;
                        }

			   if(!array_key_exists($nik, $arrOfSMSScore2)){
                            $nbnotsmsbo2++;
                        }

                        list($score, $soal) = split("#",$arrOfScore[$csid]);
                        if($score == "") {$score = 0;}
                        if($soal == "") {$soal = 0;}
                        $total += $score;

			   list($jmlsoal1, $dijawab1, $smsscore1) = split("#", $arrOfSMSScore1[$nik]);
			   if($smsscore1== "") {$smsscore1= 0;}
                        if($dijawab1== "") {$dijawab1= 0;}

			   $totalsms1 += $smsscore1;

			   list($jmlsoal2, $dijawab2, $smsscore2) = split("#", $arrOfSMSScore2[$nik]);
			   if($smsscore2== "") {$smsscore2= 0;}
                        if($dijawab2== "") {$dijawab2= 0;}

			   $totalsms2 += $smsscore2;

			   $currentkpi = round($score*0.8 + 0.2*($smsscore2 + $smsscore1)/2,2);

			   $allkpibo += $currentkpi;

                        echo"<tr> ".
                            "   <th>$name</th> ".
                            "   <td>$nik</td> ".
                            "   <td>$score</td> ".
                            "   <td>$soal</td> ".
				"   <td>$smsscore1</td> ".
                            "   <td>$dijawab1</td> ".
				"   <td>$smsscore2</td> ".
                            "   <td>$dijawab2</td> ".
				"   <td>$currentkpi</td> ".
                            "</tr>  ";

                        $i= $i+1;
                    }
                    
                    $nbbo = $i;
			$period = $selyear.str_pad($selmonth,2,'0',STR_PAD_LEFT);
                    
                    if ($nbbo > 0){
						$kpibo = round($total/$nbbo,2);
						if ($period >= 200912){
							$kpibosms1 = round($totalsms1/$nbbo,2);
							$kpibosms2 = round($totalsms2/$nbbo,2);
							$kpiallbo = round($kpibo*0.8 + 0.2*($kpibosms1 + $kpibosms2)/2,2);
						}
						else{
							$kpibosms1 = "na";
							$kpibosms2 = "na";
							$kpiallbo = $kpibo;
						}

					}
					else{
						$kpibo = "na";
						$kpibosms1 = "na";
						$kpibosms2 = "na";
						$kpiallbo = "na";
					}
					
					
					if($nbbo - $nbnotassessbo > 0){
						$avgbo = round($total/($nbbo-$nbnotassessbo),2);
					}
					else{
						$avgbo = "na";

					}

					if($nbbo - $nbnotsmsbo1> 0){
						$avgsmsbo1 = round($totalsms1/($nbbo-$nbnotsmsbo1),2);
					}
					else{
						$avgsmsbo1 = "na";
					}

					if($nbbo - $nbnotsmsbo2> 0){
						$avgsmsbo2 = round($totalsms2/($nbbo-$nbnotsmsbo2),2);
					}
					else{
						$avgsmsbo2 = "na";
					}
                    
                    echo "</tbody>";

                    ?>

					<tfoot>
                        <tr align="right">
                            <td colspan="2"><b>Total Nilai</b></td>
                            <td colspan="2"><?php echo $total;?></td>
				<td colspan="2"><?php echo $totalsms1;?></td>
				<td colspan="2"><?php echo $totalsms2;?></td>
				<td><?php echo $allkpibo;?></td>
                        </tr>
                        <tr align="right">
                            <td colspan="2"><b>Jumlah BO</b></td>
                            <td colspan="2"><?php echo $i;?></td>
				<td colspan="2"><?php echo $i;?></td>
				<td colspan="2"><?php echo $i;?></td>
				<td><?php echo $i;?></td>
                        </tr>
                        <tr align="right">
                            <td colspan="2"><b>Jumlah BO Belum Assessment </b></td>
                            <td colspan="2"><?php echo $nbnotassessbo;?></td>
				<td colspan="2"><?php echo $nbnotsmsbo1;?></td>
				<td colspan="2"><?php echo $nbnotsmsbo2;?></td>
				<td><?php echo "&nbsp;";?></td>
                        </tr>
			   <tr align="right">
                            <td colspan="2"><b>Nilai Rata-rata</b></td>
                            <td colspan="2"><?php echo $avgbo; ?></td>
				<td colspan="2"><?php echo $avgsmsbo1 ; ?></td>
				<td colspan="2"><?php echo $avgsmsbo2 ; ?></td>
				<td><?php echo "&nbsp;";?></td>

                        </tr>
                        <tr align="right">
                            <td colspan="2"><b>Nilai KPI BO</b></td>
                            <td colspan="2"><?php echo $kpibo; ?></td>
				<td colspan="2"><?php echo $kpibosms1 ; ?></td>
				<td colspan="2"><?php echo $kpibosms2 ; ?></td>
				<td><?php echo $kpiallbo ;?></td>
                        </tr>
				</tfoot>
                    </table>

			<?php

                    $i=0;
                    $total = 0;
                    echo "<br class='spacer' />";
			        echo "<table>
			        		<caption>CSR </caption>
			        		<thead>
							<tr>
						<th width='36%' rowspan='2'>Nama</th>
	  					<th width='8%' rowspan='2'>Nik</th>
		  				<th colspan='2'>PNP</th>
  					 	<th colspan='2'>SMS Quiz 1</th>
  					 	<th colspan='2'>SMS Quiz 2</th>
						<th width='8%' rowspan='2'>KPI</th>
					</tr>	
				      <tr>
					  	<th width='8%'>Nilai</th>
  					 	<th width='8%'>Soal</th>
						<th width='8%'>Nilai</th>
  					 	<th width='8%'>Soal</th>
						<th width='8%'>Nilai</th>
  					 	<th width='8%'>Soal</th>
		     		</tr></thead><tbody>";


                    $nbnotassess = 0;
			$nbnotsms1 = 0;
			 $totalsmscs1 = 0;

			$nbnotsms2 = 0;
			 $totalsmscs2 = 0;

			 $allkpisms = 0;
                    foreach($arrOfCS as $csid => $csname)
                    {
                        $name = ucwords(strtolower($csname));
                        if(!array_key_exists($csid, $arrOfScore)){
                            $name .= " *";
                            $nbnotassess++;
                        }

			   if(!array_key_exists($csid, $arrOfSMSScore1)){
                            $nbnotsms1 ++;
                        }

			   if(!array_key_exists($csid, $arrOfSMSScore2)){
                            $nbnotsms2 ++;
                        }

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

			   $currentkpi = round($score*0.8 + 0.2*($smsscore2 + $smsscore1)/2,2);

			   $allkpisms += $currentkpi;



                        echo"<tr> 
								<th>$name</th> 
								<td>$csid</td>
								<td>$score</td>
								<td>$soal</td>
								<td>$smsscore1</td>
								<td>$dijawab1</td>
								<td>$smsscore2</td>
								<td>$dijawab2</td>
								<td>$currentkpi</td>
								</tr>  ";

                        $i= $i+1;
                    }
                    
                    $nbcsr = $i;
                    
                    if ($nbcsr > 0){
						$kpicsr = round($total/$nbcsr,2);

						if ($period >= 200912){
							$kpicsrsms1 = round($totalsmscs1 /$nbcsr,2);						
							$kpicsrsms2 = round($totalsmscs2 /$nbcsr,2);
							$kpiallcs = round($kpicsr *0.8 + 0.2*($kpicsrsms1 + $kpicsrsms2 )/2,2);
						}
						else{
							$kpicsrsms1 = "na";
							$kpicsrsms2 = "na";
							$kpiallcs = $kpicsr;
						}

						

					}
					else{
						$kpicsr = "na";
						$kpicsrsms = "na";
					}
					
					if($nbcsr-$nbnotassess > 0){
						$avgcsr = round($total /($nbcsr-$nbnotassess ),2);
					}
					else{
						$avgcsr = "na";

					}

					if($nbcsr-$nbnotsms1 > 0){
						$avgsms1 = round($totalsmscs1 /($nbcsr-$nbnotsms1 ),2);
					}
					else{
						$avgsms1 = "na";

					}

					if($nbcsr-$nbnotsms2 > 0){
						$avgsms2 = round($totalsmscs2 /($nbcsr-$nbnotsms2 ),2);
					}
					else{
						$avgsms2 = "na";

					}

                    
                    echo "</tbody>";

                    ?>

					<tfoot>
                        <tr align="right">
                            <td colspan="2"><b>Total Nilai</b></td>
                            <td colspan="2"><?php echo $total;?></td>
				<td colspan="2"><?php echo $totalsmscs1 ;?></td>
				<td colspan="2"><?php echo $totalsmscs2 ;?></td>
				<td><?php echo $allkpisms;?></td>
                        </tr>
                        <tr align="right">
                            <td colspan="2"><b>Jumlah CSR</b></td>
                            <td colspan="2"><?php echo $i;?></td>
				<td colspan="2"><?php echo $i;?></td>
				<td colspan="2"><?php echo $i;?></td>
				<td><?php echo "&nbsp;";?></td>
                        </tr>
                        <tr align="right">
                            <td colspan="2"><b>Jumlah CSR Belum Assessment </b></td>
                            <td colspan="2"><?php echo $nbnotassess;?></td>
				<td colspan="2"><?php echo $nbnotsms1 ;?></td>
				<td colspan="2"><?php echo $nbnotsms2 ;?></td>
				<td><?php echo "&nbsp;";?></td>
                        </tr>
			   <tr align="right">
                            <td colspan="2"><b>Nilai Rata-rata</b></td>
                            <td colspan="2"><?php echo $avgcsr; ?></td>
				<td colspan="2"><?php echo $avgsms1 ; ?></td>
				<td colspan="2"><?php echo $avgsms2 ; ?></td>
				<td><?php echo "&nbsp;";?></td>
                        </tr>
                        <tr align="right">
                            <td colspan="2"><b>Nilai KPI CSR</b></td>
                            <td colspan="2"><?php echo $kpicsr;?></td>
				<td colspan="2"><?php echo $kpicsrsms1 ;?></td>
				<td colspan="2"><?php echo $kpicsrsms2 ;?></td>
				<td><?php echo $kpiallcs;?></td>
                        </tr>
      </tfoot>
                    </table>
                    
                    
			<?php
				//if (($nbbo != 0) || ($nbcsr != 0)){
					//echo $kpicsr;
					if ($kpibo === "na" && $kpicsr === "na" && $kpicsrsms === "na" && $kpibosms === "na"){
						$kpigrap = "na";
					}
					else{
						//echo $rowgrap[1].$kpibo;

						$bobotbo = 100;
						$bobotcsr = 100;
						if ($kpibo === "na"){$bobotbo -= 80;}
						if ($kpibosms1 === "na"){$bobotbo -= 20;}
						
						if ($kpicsr === "na"){$bobotcsr -= 80;}
						if ($kpicsrsms === "na"){$bobotcsr -= 20;}

						$bobotoverall = 100;
						if ($bobotbo == 0){$bobotoverall -= 25;}
						if ($bobotcsr == 0){$bobotoverall -= 75;}

						if ($bobotoverall > 0){
							if (($bobotbo > 0) && ($bobotcsr > 0)){
								$kpigrap = round(0.75*(0.8*$kpicsr + 0.2*($kpicsrsms1 + $kpicsrsms2)/2)*100/$bobotcsr + 0.25*(0.8*$kpibo + 0.2*($kpibosms1 + $kpibosms2)/2)*100/$bobotbo,2);
							}
							else if ($bobotbo > 0){
								$kpigrap = round((0.8*$kpibo + 0.2*($kpibosms1 + $kpibosms2)/2)*100/$bobotbo,2);
							}
							else{
								$kpigrap = round((0.8*$kpicsr + 0.2*($kpicsrsms1 + $kpicsrsms2)/2)*100/$bobotcsr,2);
							}
						}
					}
					
					echo "<br class='spacer'/>";
					echo "<table>
							<caption> KPI Shop </caption>
							<thead>
								<tr>
									<th colspan='2'> KPI BO (25%)</th>
									<th colspan='2'> KPI CSR (75%)</th>
									<th rowspan='2'> KPI Shop </th>
								</tr>
								<tr>
									<th width='16%'> PNP </th>
									<th width='17%'> SMS Quiz </th>
									<th width='16%'> PNP </th>
									<th width='17%'> SMS Quiz </th>

								</tr>
							</thead>
							<tbody>
								<tr style='text-align:center;'>
									<td> 80% </td>
									<td> 20% </td>
									<td> 80% </td>
									<td> 20% </td>
									<td> 100%</td>
								</tr>
							</tbody>
							<tfoot>
								<tr style='text-align:center;'>
									<td> $kpibo </td>
									<td> ".round(($kpibosms1+$kpibosms2)/2,2) ."</td>
									<td> $kpicsr </td>
									<td> ".round(($kpicsrsms1+$kpicsrsms2)/2,2) ." </td>
									<td> $kpigrap </td>
								</tr>
							</tfoot>
							</table>
							";
				//}
				
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

	     		$logger->Logging(HistoryType::HISTORYTYPE_VIEW_REPORT_GRAPARI,$userid,"View Report GraPARI $unitid Periode $selmonth - $selyear",$ipaddress);
			
			}
			?>
			
                 <br class='spacer' />
                    
                    ket : * : belum melakukan PNP Assessment <br/> ^ : belum melakukan SMS Quiz Assessment  	
			</div><!--left column-->

			<div id="right_column"><!--right column-->
  				<div class="section"><!--latest news-->
  					<p class="highlight">GraPARI</p>
  					<form action="report_per_grapari.php" method="post">
  					<?php
				try{
  					$channel = "SELECT 
					  			unit_id, 
								unit_name 
					  		FROM 
							  	csdm2.t_unit_branch 
						  	WHERE 
							  	unit_type='grapari' 
						    ORDER BY 2 asc";
						    
				    $geraires = $gatecsdm->ExecuteQuery($channel,"csdm");
				    
				    echo "<select name='branch' style='margin-left:10px;padding:2px;font-family:\"Trebuchet MS\", Arial;font-size:13px;width:180px;'>\n";
            		
					while($row = mysql_fetch_row($geraires)){
                		if ($unitid == $row[0]){
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
				        for($i=2006;$i<=2020;$i++){
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
