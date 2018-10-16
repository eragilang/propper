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

	$unitid = 90001;        

        if ($unitid != "0"){
        	$period = $selyear.str_pad($selmonth,2,'0',STR_PAD_LEFT);
        	
        	if ($period < date("Ym")){
        		$cssql ="SELECT 
							employee_code, 
							employee_name 
						FROM 
							csdm2.t_employee_history_summary  
						WHERE 
							unit_id in (90001,90002) and 
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
							unit_id in (90001,90002) and 
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
							pnp.score 
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
  						<a href="report_per_cc.php">Per CallCenter</a><br />
  						<a href="report_per_segment_cc.php">Per Segment</a> <br />
  						<a href="report_nasional_cc.php">Nasional</a><br/>
						QAO &amp; Webservice
		  			</div>
					  <div id="userscores">
  						<span style="text-decoration:underline;">Top 10 Agent : </span><br />
  						<a href="report_per_cc_top10.php">Per CallCenter</a> <br />
  						<a href="report_per_segment_cc_top10.php">Per Segment</a> <br />
  						<a href="report_nasional_cc_top10.php">Nasional</a>
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
                    
                    $nbcsr = $i;
                    
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
  					<form action="report_qao.php" method="post">
  					
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
