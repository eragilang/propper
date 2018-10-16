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
        require_once ("class/HTMLGenerator.php");
        require_once ("class/Logger.php");
    	require_once ("class/DBgateway.php");
    	require_once ("class/ConfigReader.php");
    
    	$conf = new ConfigReader();

        MenuBuilder::printReportMenuSOP(1);
?>
<br/>
<div class="clear"></div>

<!-- start main -->
<div id="mainwrap">

    <div id="content">
    <a href="report_sop_cc.php" >Per Call Center </a>&bull;
    <a href="report_sop_cc_all.php" >All Call Center </a>&bull;
    Question per Call Center &bull;
    <a href="report_sop_cc_question_all.php" >Question all Call Center</a> &bull;
    <h1>Laporan SOP Call Center</h1>
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
        try {
            //$gatepnp = new DBGateway(DBConf96237::GetDBHost(), DBConf96237::GetDBUser(), DBConf96237::GetDBPassword());
            //$gatepnp->OpenConnection();

            $gatecsdm = new DBGateway($conf->Host251,$conf->User251, $conf->Pass251);
        	$gatecsdm->OpenConnection();

            if ($unitid != 0){
                $repsql =	"SELECT ".
				"	ca.cat_id, ".
				"	description, ".
				"	sum(is_correct) as nbcorrect, ".
				"	sum(if(is_correct=0,1,0)) as nbwrong ".
				"FROM ".
				"	pnp.category ca, ".
				"	pnp.score_detail_archive sda, ".
				"	pnp.question_archive qa ".
				"WHERE ".
				"	ca.cat_id = qa.cat_id and ".
				"	sda.question_id = qa.question_id and ".
				"	unit_id = $unitid and ".
				"	sda.set_id = 2 and ".
				"	cat_group = '3.0' ".
				"GROUP BY ".
				"cat_id";

			//echo $repsql;

                    $scoreres = $gatecsdm->ExecuteQuery($repsql,"pnp");

                    $i=0;
                    $total = 0;
                    echo "<br class='spacer' />";
                    echo "<table class='report' cellpadding='0' cellspacing='0'>".
                        "<tr>".
                        "<td class='header params'>Judul SOP</td>".
                        "<td class='header month'>Kode SOP</td>".
                        "<td class='header month'>Benar</td>".
                        "<td class='header month'>Salah</td>".
                        "</tr>";

			$sumbenar = 0;
			$sumsalah = 0;

                    while($row = mysql_fetch_row($scoreres)){
                        echo "<tr>";
				echo "<td class='valuel params'>".$row[1]."</td>";
				echo "<td class='valuer month'>".$row[0]."</td>";
				echo "<td class='valuer month'>".$row[2]."</td>";
				echo "<td class='valuer month'>".$row[3]."</td>";
				echo "</tr>";
				$sumbenar += $row[2];
				$sumsalah += $row[3];
                    }

                    
                    echo "<tr>";
                    echo "<td class='valuel params' style=background-color:#CCC;' colspan='2'>Total</td>";
                    echo "<td class='valuer month' style='background-color:#CCC;'>$sumbenar</td>";
			echo "<td class='valuer month' style='background-color:#CCC;'>$sumsalah</td>";
			echo "</tr>";

                    ?>

                    </table>
                    <br class='spacer' />
                    <br class='spacer' />
                    <?

            }

         $ipaddress = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else{
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            }

            $logger = new Logger();
            $logger->setDBConnection($gatecsdm);
         $userid = $_SESSION['uSeRiD'];
         if(trim($userid) == ""){$userid = "{Anonymous}";}

         $logger->Logging(HISTORYTYPE_VIEW_REPORT_GRAPARI,$userid,"View Report GraPARI $unitid Periode $selmonth - $selyear",$ipaddress);

            //$gatecsdm->CloseConnection();
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
    //}
    ?>

</div><!-- ende content -->

<!-- start contentbar -->
  <div id="reportcontentbar">

<!-- start Hotline -->
<div class="newsletterbox">
    <form name='frmQuiz' action='report_sop_cc_question.php' method='post' id='frmQuiz'>
<h3> <a href="#" title="Score">Call Center</a></h3>
  <p style="margin: 8px 0pt 6px 50px;" id='numberCountdown'>
  <?
        try{

            $gerai = "SELECT unit_id, unit_name FROM csdm2.t_unit_branch where unit_type = 'callcenter' order by 2 asc";
            $geraires = $gatecsdm->ExecuteQuery($gerai,"csdm");

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

            $gatecsdm->CloseConnection();

  ?>

  </p>
  <?
        }
        catch(Exception $e){

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