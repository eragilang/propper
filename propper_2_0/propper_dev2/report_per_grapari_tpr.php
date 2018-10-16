<?php // PHP4 clone & exceptions emulation (must be hidden behind PHP5 parser)
if (PHP_VERSION < 5) require_once('php4compat.php4');
?>
<?
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de"><head>

    <link rel="stylesheet" href="css/main-styles.css" type="text/css" />
    <title>:: PNP Assessment :: Report Per GraPARI</title>

</head>
<body id="home">
<!--[if lt IE 7.]>
    <script defer type='text/javascript' src='script/fixpng.js''></script>
<![endif]-->;

<!-- wrap -->
<div id="wrap">
<!-- start logo -->
    <div id="logo"><a href="#" title="PNP Assessment">
    <img src="image/logo2.png" alt="PNP Assessment" height="58" width="146"/></a>
</div><!-- ende logo -->

<div class="clear"></div>
<?
        require_once("class/HTMLGenerator.php");
        require_once("class/DBgateway.php");
        require_once("class/Logger.php");
        require_once("conf/DBConf.php");

        MenuBuilder::printReportMenu(2);
?>
<br/>
<div class="clear"></div>

<!-- start main -->
<div id="mainwrap">

    <div id="content">
       <a href="report_per_regional_grapari.php" >KPI GraPARI </a>&bull;
	<a href="report_per_regional_grapari.php" >Report Regional</a> &bull;
	<a href="report_per_area_grapari.php" >Report Area</a> &bull;
	<a href="report_nasional_grapari.php" >Report Nasional</a>
       Report TPR
    <h1>Laporan Bulanan GraPARI</h1>
    <?
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

    //if(isset($_POST["btnReport"])){
        /* try */{
            $gatepnp = new DBGateway(DBConf96237::GetDBHost(), DBConf96237::GetDBUser(), DBConf96237::GetDBPassword());
            $gatepnp->OpenConnection();

            $gatecsdm = new DBGateway(DBConf130251::GetDBHost(), DBConf130251::GetDBUser(), DBConf130251::GetDBPassword());
            $gatecsdm->OpenConnection();

            if ($unitid != 0){
                $cssql ="SELECT employee_code, employee_name ".
                        "FROM ".
                        "   csdm.csdm_employee ".
                        "WHERE ".
                        "   channel_service_id = ".$unitid." and ".
                        "   channel_service_type = 'grapari' and ".
                        "   job_function in ('TPR') and ".
                        "   ((end_contract >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or end_contract = '0000-00-00') and ".
                        "    (reg_datetime < '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-01' or start_contract = '0000-00-00') and ".
                        "    (unreg_datetime >= '".$selyear."-".str_pad($selmonth,2,'0',STR_PAD_LEFT)."-15' or unreg_datetime is null)) ".
                        "ORDER BY 2";

                $scoresql = "SELECT employee_id, score, nb_done FROM pnp.score WHERE period = '$selyear".str_pad($selmonth,2,'0',STR_PAD_LEFT)."' and set_id = 1 and assess_id = 'REG' ";

                $repsqlold = "SELECT ".
                             "  b.nik, b.name, a.score ".
                             "FROM score a LEFT JOIN users b ON (a.employee_id = b.id) LEFT JOIN branch c ".
                             "  ON (a.unit_id = c.cloc) ".
                            "WHERE ".
                            "   a.unit_id='". $unitid ."' and ".
                            "   period = '$selyear".str_pad($selmonth,2,'0',STR_PAD_LEFT)."' and ".
                            "   b.counter between '1' and '3' ORDER BY 2";

                $isJoinCSDM = false;

                if ($selyear <= 2007){
                    if ($selmonth == 12){
                        $isJoinCSDM = true;
                    }
                    else{
                        $isJoinCSDM = false;
                    }
                }
                else{
                    //echo $repsqlnew;
                    $isJoinCSDM = true;
                }

                if(!$isJoinCSDM){
                    $reportres = $gatepnp->ExecuteQuery($repsqlold,"pnp");

                    $i=0;
                    $total = 0;
                    echo "<br class='spacer' />";
                    echo "<table class='report' cellpadding='0' cellspacing='0'>".
                        "<tr>".
                        "<td class='header params'>Nama</td>".
                        "<td class='header month'>Nik</td>".
                        "<td class='header month'>Nilai</td>".
                        "</tr>";

                    while($row_data=mysql_fetch_row($reportres))
                    {
                        $name = ucwords(strtolower($row_data[1]));
                        $nik = $row_data[0];
                        $score = $row_data[2];
                        $total += $score;

                        echo"<tr> ".
                            "   <td class='params valuel'>$name</td> ".
                            "   <td class='month valuer'>$nik</td> ".
                            "   <td class='month valuer'>$score</td> ".
                            "</tr>  ";

                        $i= $i+1;
                    }
    ?>


                        <tr align="center">
                            <td colspan="2"><b>Total Nilai</b></td>
                            <td><?php echo $total;?></td>
                        </tr>
                        <tr align="center">
                            <td colspan="2"><b>Jumlah CSR</b></td>
                            <td><?php echo $i;?></td>
                        </tr>
                        <tr align="center">
                            <td colspan="2"><b>Nilai KPI</b></td>
                            <td><?php if ($i>0){echo round($total/$i,2);}else{echo "N/A";}?></td>
                        </tr>

                    </table>
                    <br class='spacer' />
                    <br class='spacer' />
                    ket : * : belum melakukan assessment
                    <?
                }
                else{
                    $csres = $gatecsdm->ExecuteQuery($cssql,"csdm");

                    $arrOfCS = array();
                    while($row = mysql_fetch_row($csres)){
                        $arrOfCS[$row[0]] = $row[1];
                    }

                    $scoreres = $gatepnp->ExecuteQuery($scoresql,"pnp");

                    $arrOfScore = array();
                    while($row = mysql_fetch_row($scoreres)){
                        $arrOfScore[$row[0]] = $row[1]."#".$row[2];
                    }

                    $i=0;
                    $total = 0;
                    echo "<br class='spacer' />";
                    echo "<table class='report' cellpadding='0' cellspacing='0'>".
                        "<tr>".
                        "<td class='header params'>Nama</td>".
                        "<td class='header month'>Nik</td>".
                        "<td class='header month'>Nilai</td>".
                        "<td class='header month'>Soal</td>".
                        "</tr>";


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

                        echo"<tr> ".
                            "   <td class='params valuel'>$name</td> ".
                            "   <td class='month valuer'>$csid</td> ".
                            "   <td class='month valuer' style='background-color:#ccc;'>$score</td> ".
                            "   <td class='month valuer'>$soal</td> ".
                            "</tr>  ";

                        $i= $i+1;
                    }

                    ?>


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
                            <td colspan="2"><b>Nilai KPI</b></td>
                            <td colspan="2"><?php if ($i>0){echo round($total/$i,2);}else{echo "N/A";}?></td>
                        </tr>

                    </table>
                    <br class='spacer' />
                    <br class='spacer' />
                    ket : * : belum melakukan assessment
                    <?
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

	     $logger->Logging(HISTORYTYPE_VIEW_REPORT_GRAPARI,$userid,"View Report GraPARI $unitid Periode $selmonth - $selyear",$ipaddress);

            $gatepnp->CloseConnection();
            $gatecsdm->CloseConnection();
        }
        if (catch('Exception', $e)){
            echo $e->getMessage();
        }
    //}
    ?>

</div><!-- ende content -->

<!-- start contentbar -->
  <div id="reportcontentbar">

<!-- start Hotline -->
<div class="newsletterbox">
    <form name='frmQuiz' action='report_per_grapari_tpr.php' method='post' id='frmQuiz'>
  <h3> <a href="#" title="Score">GraPARI</a></h3>
  <p style="margin: 8px 0pt 6px 50px;" id='numberCountdown'>
  <?
        /* try */{
            $gate = new DBGateway(DBConf130251::GetDBHost(), DBConf130251::GetDBUser(), DBConf130251::GetDBPassword());
            $gate->OpenConnection();

            $gerai = "SELECT channel_service_id, channel_name FROM csdm.csdm_channel_grapari order by 2 asc";
            $geraires = $gate->ExecuteQuery($gerai,"csdm");

            echo "<select name='branch' style='padding:2px;font-family:\"Trebuchet MS\", Arial;font-size:13px;width:180px;'>\n";
            while($row = mysql_fetch_row($geraires)){
                if ($unitid == $row[0]){
                    echo "<option value='".$row[0]."' style='margin:3px;' selected>".$row[1]."</option>\n";
                }
                else{
                    echo "<option value='".$row[0]."' style='margin:3px;'>".$row[1]."</option>\n";
                }
            }
            echo "</select>\n";

            $gate->CloseConnection();

  ?>

  </p>
  <?
        }
        if (catch('Exception', $e)){

        }
  ?>
  <br class='clear' />
  <h3> <a href="#" title="Profile">Bulan</a></h3>
  <p style="margin: 8px 0pt 6px 50px; font-size: 15px;">
    <select name = 'bulan' style='padding:2px;font-family:"Trebuchet MS", Arial;font-size:13px;width:180px;'>
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
  </p>
  <br class='clear' />
<!-- ende Hotline --> <!-- start Newsletter -->
  <h3> <a href="#" title="Score">Tahun</a></h3>
  <p style="margin: 8px 0pt 6px 50px; font-size: 25px;">
    <select name='tahun' style='padding:2px;font-family:"Trebuchet MS", Arial;font-size:13px;width:180px;'>
    <?
        for($i=2006;$i<=2011;$i++){
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
  </p>
  <p style="margin: 8px 0pt 6px 50px;"><input type="submit" class='submit' name='btnReport' id='btnReport' value='Submit'/></p>
  </form>
</div><!-- ende Newsletter --> <!-- start Aktion -->

<!-- start Newsbox 1 -->
<br class='clear'/>
</div><!-- ende contentbar -->
</div><!-- ende mainwrap -->

<div class="clear"></div>

<!-- start footer -->
  <div id="footer">
    <a href="#" title="PT Telkomsel">Telkomsel</a> ·
    <a href="#" title="Service Quality Management">Service Quality Management</a> ·
    <a href="mailto:Andika_A_Pramono@telkomsel.co.id" title="Andika A Pramono">Andika A Pramono</a> ·
    <a href="mailto:Irfan_Sampe@telkomsel.co.id" title="Irfan Sampe">Irfan Sampe</a> ·
    <p>&copy; 2008 <strong><a href="main.php" title="PNP Assessment">PNP Assessment</a></strong> </p>

</div><!-- ende footer -->
</div><!-- ende wrap -->
</body></html>