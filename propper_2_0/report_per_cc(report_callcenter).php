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
        
        if ($unitid != "0"){
        	$period = $selyear.str_pad($selmonth,2,'0',STR_PAD_LEFT);
        	
        	if ($period < date("Ym")){
        		$cssql ="SELECT 
							employee_code, 
							employee_name 
						FROM 
							csdm2.t_employee_history_summary  
						WHERE 
							unit_id = ".$unitid." and 
							unit_type IN ('callcenter','webservice') and 
							period = '$period' and 
							job_function in (SELECT job_value FROM csdm2.t_employee_jobfunction WHERE pnp_assessee = '1') and 
							((end_contract >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or end_contract = '0000-00-00') and 
							(reg_datetime < '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-25' or start_contract = '0000-00-00') and 
							(unreg_datetime >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or unreg_datetime is null or unreg_datetime = '0000-00-00 00:00:00')) 
						ORDER BY 2";
       		}
        	else{
        		$cssql ="SELECT 
							employee_code, 
							employee_name 
						FROM 
							csdm2.t_employee  
						WHERE 
							unit_id = ".$unitid." and 
							unit_type IN ('callcenter','webservice') and 
							job_function in (SELECT job_value FROM csdm2.t_employee_jobfunction WHERE pnp_assessee = '1') and 
							((end_contract >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or end_contract = '0000-00-00') and 
							(reg_datetime < '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-25' or start_contract = '0000-00-00') and 
							(unreg_datetime >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or unreg_datetime is null or unreg_datetime = '0000-00-00 00:00:00')) 
						ORDER BY 2";
        	}
			
			$scoresql = "SELECT 
							employee_id, 
							score, 
							nb_done 
						FROM 
							pnp.cc_score 
						WHERE 
							period = '$selyear".str_pad($selmonth,2,'0',STR_PAD_LEFT)."' and 
							set_id = 2 and 
							assess_id = 'REG' ";
							
			
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
  						Per CallCenter<br />
  						<a href="report_per_segment_cc.php">Per Segment</a> <br />
  						<a href="report_nasional_cc.php">Nasional</a><br/>
						<a href="report_qao.php">QAO &amp; Webservice</a>
		  			</div>
					  <div id="userscores">
  						<span style="text-decoration:underline;">Top 10 Agent : </span><br />
  						<a href="report_per_cc_top10.php">Per CallCenter</a> <br />
  						<a href="report_per_segment_cc.php">Per Segment</a> <br />
  						<a href="report_nasional_cc_top10.php">Nasional</a>
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
 			if ($unitid != "0"){
				$i=0;
                    $total = 0;
                    echo "<br class='spacer' />";
			        echo "<table>
			        		<caption>CSR </caption>
			        		<thead>
							<tr>
								<th width='55%'>Nama</th>
								<th width='15%'>Nik</th>
								<th width='15%'>Nilai</th>
								<th width='15%'>Soal</th>
							</tr></thead><tbody>";


                    $nbnotassess = 0;
                    foreach($arrOfCS as $csid => $csname)
                    {
                        $name = ucwords(strtolower($csname));
                        if(!array_key_exists($csid, $arrOfScore)){
                            $name .= " *";
                            $nbnotassess++;
                        }
                        list($score, $soal) = split("#",$arrOfScore[$csid]);
                        if($score == "") {$score = 0;}
                        if($soal == "") {$soal = 0;}
                        $total += $score;

                        echo"<tr> 
								<th>$name</th> 
								<td>$csid</td>
								<td>$score</td>
								<td>$soal</td>
								</tr>  ";

                        $i= $i+1;
                    }
					
				/* else ($unitid == "50001"){
				$i=0;
                    $total = 0;
                    echo "<br class='spacer' />";
			        "<table>
			        		<caption>CSR </caption>
			        		<thead>
							<tr>
						<th width='36%' rowspan='2'>Nama</th>
	  					<th width='8%' rowspan='2'>Nik</th>
  					 	<th colspan='2'>PnP 1</th>
  					 	<th colspan='2'>Pnp 2</th>
						<th width='8%' rowspan='2'>KPI</th>
					</tr>	
				      <tr>
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

			   $currentkpi = round(smsscore1+ smsscore2/2,2);

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

					} */

                    
                    echo "</tbody>";

                    ?>

					<tfoot>
                        <tr align="right">
                            <td colspan="2"><b>Total Nilai</b></td>
                            <td colspan="2"><?php echo $total;?></td>
                        </tr>
                        <tr align="right">
                            <td colspan="2"><b>Jumlah CSR</b></td>
                            <td colspan="2"><?php echo $i;?></td>
                        </tr>
                        <tr align="right">
                            <td colspan="2"><b>Jumlah CSR Belum Assessment PNP </b></td>
                            <td colspan="2"><?php echo $nbnotassess;?></td>
                        </tr>
			   <tr align="right">
                            <td colspan="2"><b>Nilai Rata-rata</b></td>
                            <td colspan="2"><?php if ($i - $nbnotassess > 0){echo round($total/($i - $nbnotassess),2);}else{echo "N/A";}?></td>
                        </tr>
                        <tr align="right">
                            <td colspan="2"><b>Nilai KPI CSR</b></td>
                            <td colspan="2"><?php if ($i>0){$kpicsr = round($total/$i,2);echo $kpicsr;}else{echo "N/A";}?></td>
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

	     		$logger->Logging(HistoryType::HISTORYTYPE_VIEW_REPORT_CALLCENTER,$userid,"View Report Call Center $unitid Periode $selmonth - $selyear",$ipaddress);
					}
					?>   	
			</div><!--left column-->

			<div id="right_column"><!--right column-->
  				<div class="section"><!--latest news-->
  					<p class="highlight">CallCenter</p>
					<?php
					//$unitid ='report_per_cc.php';
					echo $unitid;
					/*if ($unitid=='50001'){
						$page ='report_per_cc_jakarta.php';
					}
					else {
						$page ='report_per_cc.php';
					}*/
					?>
					
  					<form action="report_per_cc.php" method="post">
  					<?php
				try{
  					$channel = "SELECT 
					  			unit_id, 
								unit_name 
					  		FROM 
							  	csdm2.t_unit_branch 
						  	WHERE 
							  	unit_type IN ('callcenter','webservice')
								AND unit_flag = 1
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
				        for($i=2010;$i<=2018;$i++){
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
  		<p id="copyright">Copyright � 2009 Developed by Tools and Data Management Departement</p>
	</div><!--footer-->

</div><!--page container-->

</body></html>
