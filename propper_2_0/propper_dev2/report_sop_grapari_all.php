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

        MenuBuilder::printReportMenuSOP(2);
?>
<br/>
<div class="clear"></div>

<!-- start main -->
<div id="mainwrap">

    <div id="content">
    <a href="report_sop.php" >Per GraPARI </a>&bull;
    All GraPARI &bull;
    <a href="report_sop_grapari_question.php" >Question per GraPARI</a> &bull;
    <a href="report_sop_grapari_question_all.php" >Question all GraPARI</a> &bull;
    <h1>Laporan SOP GraPARI</h1>
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

    if(isset($_POST["btnReport"])){
        try {
            //$gatepnp = new DBGateway(DBConf96237::GetDBHost(), DBConf96237::GetDBUser(), DBConf96237::GetDBPassword());
            //$gatepnp->OpenConnection();

            $gatecsdm = new DBGateway($conf->Host251,$conf->User251, $conf->Pass251);
        	$gatecsdm->OpenConnection();

            //if ($unitid != 0){
                $repsql ="SELECT ".
                         "  unit_name, ".
                         "  product, ".
                         "  count(*) nbsoal, ".
                         "  sum(is_correct) nbcorrect, ".
                         "  count(*) - sum(is_correct) nbwrong, ".
                         "  cg.unit_id, ".
			    		" regional ".
                         "FROM ".
                         "  pnp.score_detail_archive s, ".
                         "  pnp.category ca, ".
                         "  pnp.question_archive qa, ".
                         "  csdm2.t_unit_branch cg ".
                         "WHERE ".
                         "	unit_type = 'grapari' and ".
                         "  period = $selyear".str_pad($selmonth,2,'0',STR_PAD_LEFT)." and ".
                         "  s.assess_id = 'REG' and ".
                         "  s.question_id = qa.question_id and ".
                         "  s.assess_id = qa.assess_id and ".
                         "  ca.cat_id = qa.cat_id and ".
                         "  cg.unit_id = s.unit_id and ".
                         "  ca.cat_group = '3.0' and s.set_id = 1 and qa.set_id = 1 ".
                         "GROUP BY ".
                         "  cg.unit_id,".
                         "  product ".
                         "order by regional,1,2";

			//echo $repsql;

                    $scoreres = $gatecsdm->ExecuteQuery($repsql,"pnp");

                    $i=0;
                    $total = 0;
                    echo "<br class='spacer' />";
                    echo "<table class='report' cellpadding='0' cellspacing='0'>".
                        "<tr>".
                        "<td class='header params' rowspan='2'>GraPARI</td>".
                        "<td class='header month' colspan='2'>HALO</td>".
                        "<td class='header month' colspan='2'>simPATI</td>".
                        "<td class='header month' colspan='2'>AS</td>".
                        "</tr>".
                        "<tr>".
                        "<td class='header month'>B</td>".
                        "<td class='header month'>S</td>".
                        "<td class='header month'>B</td>".
                        "<td class='header month'>S</td>".
                        "<td class='header month'>B</td>".
                        "<td class='header month'>S</td>".
                        "</tr>";

                    $currcsr = "";
                    $prevcsr = "";
                    $idx = 0;

                    $arrOfCSR = array();
                    $arrOfScore = array();
                    $arrOfProduct = array();

                    $arrOfProduct[1] = "HALO";
                    $arrOfProduct[2] = "simPATI";
                    $arrOfProduct[3] = "AS";
		      
                    while($row = mysql_fetch_row($scoreres)){
                        $currcsr = $row[0];
                        if(!array_key_exists($row[6]."#".$row[5],$arrOfCSR)){
                            $arrOfCSR[$row[6]."#".$row[5]] = $row[0];
                            $arrOfScore[$row[6]."#".$row[5]] = array();
                        }

                        $arrOfScore[$row[6]."#".$row[5]][$row[1]] = $row[3]."#".$row[4];
                    }

                    $arrOfTotal = array();
                    $arrOfTotal[1] = array();
                    $arrOfTotal[1]["B"] = 0;
                    $arrOfTotal[1]["S"] = 0;
                    $arrOfTotal[2] = array();
                    $arrOfTotal[2]["B"] = 0;
                    $arrOfTotal[2]["S"] = 0;
                    $arrOfTotal[3] = array();
                    $arrOfTotal[3]["B"] = 0;
                    $arrOfTotal[3]["S"] = 0;
			$currregion = "";$prevregion = "";
                    foreach($arrOfCSR as $csid => $csname){
			   list($currregion, $currunitid) = split("#",$csid);
				if($currregion != $prevregion){
					echo "<tr><td colspan='7' style='border:1px solid #000;border-top:none;background-color:#CCC;'>Region $currregion </td></tr>";
					$prevregion = $currregion;
				}
                        echo "<tr>";
                        echo "<td class='valuel params'><a href='report_sop.php?uid=$currunitid&thn=$selyear&bln=$selmonth'>$csname</a></td>";
                        foreach($arrOfProduct as $pid => $pname){
                            list($correct, $wrong) = split("#",$arrOfScore[$csid][$pid]);
                            if($wrong == NULL){
                                 $wrong=0;   };
                            if($correct == NULL){
                                 $correct=0;   };     
                                 
                            
				echo "<td class='valuer month'><a href='report_sop_grapari_question.php?uid=$currunitid&thn=$selyear&bln=$selmonth'>$correct</a></td>";
				echo "<td class='valuer month'><a href='report_sop_grapari_question.php?uid=$currunitid&thn=$selyear&bln=$selmonth'>$wrong</a></td>";
				$arrOfTotal[$pid]["B"] += $correct;
                            $arrOfTotal[$pid]["S"] += $wrong;
                        }
                        echo "</tr>";
                    }

                    echo "<tr>";
                    echo "<td class='valuel params' style=background-color:#CCC;'>Total</td>";
                    echo "<td class='valuer month' style='background-color:#CCC;'> <a href='report_sop_grapari_question_all.php?thn=$selyear&bln=$selmonth'>".$arrOfTotal[1]["B"]."</a></td>";
		      echo "<td class='valuer month' style='background-color:#CCC;'> <a href='report_sop_grapari_question_all.php?thn=$selyear&bln=$selmonth'>".$arrOfTotal[1]["S"]."</a></td>";
                    echo "<td class='valuer month' style='background-color:#CCC;'> <a href='report_sop_grapari_question_all.php?thn=$selyear&bln=$selmonth'>".$arrOfTotal[2]["B"]."</a></td>";
		      echo "<td class='valuer month' style='background-color:#CCC;'> <a href='report_sop_grapari_question_all.php?thn=$selyear&bln=$selmonth'>".$arrOfTotal[2]["S"]."</a></td>";
                    echo "<td class='valuer month' style='background-color:#CCC;'> <a href='report_sop_grapari_question_all.php?thn=$selyear&bln=$selmonth'>".$arrOfTotal[3]["B"]."</a></td>";
                    echo "<td class='valuer month' style='background-color:#CCC;'> <a href='report_sop_grapari_question_all.php?thn=$selyear&bln=$selmonth'>".$arrOfTotal[3]["S"]."</a></td>";
			echo "</tr>";

                    ?>

                    </table>
                    <br class='spacer' />
                    <br class='spacer' />
                    <?

            //}

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

         $logger->Logging(HistoryType::HISTORYTYPE_VIEW_REPORT_GRAPARI,$userid,"View Report GraPARI $unitid Periode $selmonth - $selyear",$ipaddress);

            //$gatecsdm->CloseConnection();
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
    }
    ?>

</div><!-- ende content -->

<!-- start contentbar -->
  <div id="reportcontentbar">

<!-- start Hotline -->
<div class="newsletterbox">
    <form name='frmQuiz' action='report_sop_grapari_all.php' method='post' id='frmQuiz'>
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