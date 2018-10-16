<?php // PHP4 clone & exceptions emulation (must be hidden behind PHP5 parser)
if (PHP_VERSION < 5) require_once('php4compat.php4');
?><?php
    session_start();
    if($_SESSION['uSeRiD'] == ""){
        $homelink = "index.php";
    }
    else{
        $homelink = "main.php";
    }

    $hak = $_SESSION['aCCeSSRiGHT'];
    $allowedToAccess = array('ADMN','GSPV','CSPV','CSAG','T1RQ','ND2Q','BOGH','GRPV','H4JQ','BCRQ','HAJQ','PH0Q');

    if (!in_array($hak,$allowedToAccess)){
        session_destroy();
        echo "<script language=\"javascript\">location.href=\"index.php?err=75\"</script>";
    }

    require_once("class/HTMLGenerator.php");
    require_once("class/DBgateway.php");
    require_once("conf/DBConf.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de"><head>

    <link rel="stylesheet" href="css/main-styles.css" type="text/css" />
    <script type="text/javascript" src="script/keinspam.js"></script>
    <title>:: PNP Assessment :: Main</title>

</head>
<body id="home">
<!--[if lt IE 7.]>
    <script defer type='text/javascript' src='script/fixpng.js''></script>
<![endif]-->;

<!-- wrap -->
<div id="wrap">
<!-- start logo -->
    <div id="logo"><a href=<?php echo "\"$homelink\"";?> title="PNP Assessment">
    <img src="image/logo2.png" alt="PNP Assessment" height="58" width="146"/></a>
</div><!-- ende logo -->

<div class="clear"></div>

<?php
MenuBuilder::printTopMenu($hak,"HOME");
?>
<br/>
<div class="clear"></div>

<!-- start main -->
<div id="mainwrap">

    <div id="content">

    <?
    $infosql =  "SELECT ".
                "   nb_done, ".
                "   score, ".
                "   date_format(end_assess,'%d/%m/%Y %H:%i:%S') as fdate ".
                "FROM ".
                "   score ".
                "WHERE ".
                "   employee_id = '".$_SESSION["uSeRiD"]."' and ".
                "   period = date_format(now(),'%Y%m') and assess_id = '".$_SESSION["aSSeSSTYPe"]."'";

    /* try */{
        $gate = new DBGateway(DBConf96237::GetDBHost(), DBConf96237::GetDBUser(), DBConf96237::GetDBPassword());
        $gate->OpenConnection();

        $infores = $gate->ExecuteQuery($infosql,"pnp");
        $arrOfInfo = mysql_fetch_array($infores);
?>

<!-- start statusnavigation -->
<div id="statusnav"><a href="main.php" title="PNP Assessment Home" id="firststatus">Home</a>
</div><!-- ende statusnavigation -->

      <h1>Product Knowledge and Procedure</h1>

      <p><img src="image/header.jpg" alt="Product Knowledge and Procedure" style="margin: 0px 0pt 0pt;" height="150" width="459"/></p>
      <?
        if ($_SESSION["assessStatus"] == "done"){
            echo "<p style='margin: 8px 0pt 6px 10px; font-size: 21px;'>Terima kasih, <br class='clear'/> ".
                "Anda telah menyelesaikan PNP Assessment pada bulan ini. ".
                "Nilai akhir Anda bulan ini adalah <strong style='color:red;font-size:40px'>".$arrOfInfo[1]."</strong></p> ";
	    /*echo "<p style='margin: 8px 0pt 6px 10px; font-size: 21px;'>Terima kasih, <br class='clear'/> ".
                "Anda telah menyelesaikan PNP Assessment pada bulan ini. </p> ";*/
        }
        else if ($_SESSION["assessStatus"] == "end") {
            echo "<p style='margin: 8px 0pt 6px 10px; font-size: 21px;'>Terima kasih, <br class='clear'/> ".
                "Anda telah menyelesaikan <strong style='color:red;font-size:40px'>".$arrOfInfo[0]."</strong> pertanyaan. ".
                "Nilai Anda sampai saat ini adalah <strong style='color:red;font-size:40px'>".$arrOfInfo[1]."</strong></p> ";
		/*echo "<p style='margin: 8px 0pt 6px 10px; font-size: 21px;'>Terima kasih, <br class='clear'/> ".
                "Anda telah menyelesaikan <strong style='color:red;font-size:40px'>".$arrOfInfo[0]."</strong> pertanyaan. </p> "; */
        }
        
        $gate->CloseConnection();
    }
    if (catch('Exception', $e)){
    }
?>

</div><!-- ende content -->

<!-- start contentbar -->
  <div id="contentbar">

<!-- start Hotline -->
<div class="newsletterbox">

  <h3> <a href="#" title="Profile">Profil</a></h3>

  <p><span class="leftalign"><img src="image/people.png" alt="<? echo $_SESSION["uSeRNaMe"];?>"/></span></p>
  <p style="margin: 8px 0pt 6px 50px; font-size: 15px;"><strong><?= $_SESSION["uSeRNaMe"]; ?></strong></p>
  <br class='clear' />
<!-- ende Hotline --> <!-- start Newsletter -->
</div><!-- ende Newsletter --> <!-- start Aktion -->


</div><!-- ende contentbar -->
</div><!-- ende mainwrap -->

<div class="clear"></div>

<!-- start footer -->
  <div id="footer">
    <a href="www.telkomsel.co.id" title="PT Telkomsel">Telkomsel</a> ·
    <a href="#" title="Service Quality Management">Service Quality Management</a> ·
<a href="mailto:Andika_A_Pramono@telkomsel.co.id" title="Andika A Pramono">Andika A Pramono</a> ·
    <a href="mailto:Irfan_Sampe@telkomsel.co.id" title="Irfan Sampe">Irfan Sampe</a> ·
    <p>&copy; 2008 <strong><a href="main.php" title="PNP Assessment">PNP Assessment</a></strong> </p>

</div><!-- ende footer -->
</div><!-- ende wrap -->
</body></html>