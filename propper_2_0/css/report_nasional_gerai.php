<?php // PHP4 clone & exceptions emulation (must be hidden behind PHP5 parser)
if (PHP_VERSION < 5) require_once('php4compat.php4');
?>
<?
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de"><head>

    <link rel="stylesheet" href="css/main-styles.css" type="text/css" />
    <title>:: KPI Service Performance Report Nasional</title>

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
<br>
<div class="clear"></div>

<!-- start main -->
<div id="mainwrap">

  <a href="report_per_gerai.php" >KPI Gerai </a>&bull;
	<a href="report_per_regional_gerai.php" >Report Regional </a>&bull;
	Report Area &bull;
	<a href="#" >Report Nasional</a>
    <h1>Laporan Service Performance Bulan Juni 2008</h1>
    <?
    $area = 0;
    $tanggal =date("d-F-Y");
    $bulan=date('M');
    $bulan_a=date('m');
    $tahun=date('Y');


        /* try */{

            $gateqm = new DBGateway(DBConf130251qm::GetDBHost(), DBConf130251qm::GetDBUser(), DBConf130251qm::GetDBPassword());
            $gateqm->OpenConnection();

		  $grapsql = "
      select b.regional,b.unit_name, a.* 
from xmatic_backup.summary_kpi_geraiHALO a
inner join csdm2.t_unit_branch b on a.unit_id=b.unit_id
order by b.regional,b.unit_name
      ";
		  $grapres = $gateqm->ExecuteQuery($grapsql,"qmatic");

		  echo "<br class='spacer' />";
                echo "<table class='report' style='font-size:11px;' cellpadding='0' cellspacing='0'>".
                     "<tr>".
                     "<td class='header month' colspan=2 rowspan=2 align='center' valign='center'>GeraiHALO</td>".
                     "<td class='header month' colspan=4 rowspan=2 align='center' valign='center'>Product and Procedure Knowledge (30%)</td>".
                     "<td class='header month' colspan=13 align='center'>Customer Handling (15%)</td>".
                     "</tr>". 
                     "<tr>".
                     "<td class='header month' colspan=3 align='center'>Recorded Transaction (RT) (50%)</td>".
                     "<td class='header month' colspan=10 align='center'>Service Ticket Performance (STP) (50%)</td>".
                     "</tr>"
                     ;
                echo "<tr>".
                     "<td class='header month' align='center'>Regional</td>".
                     "<td class='header month' align='center'>GeraiHALO</td>".
                     "<td class='header month' align='center'>CSR Aktif</td>".
                     "<td class='header month' align='center'>CSR PNP</td>".
                     "<td class='header month' align='center'>Average</td>".
                     "<td class='header month' align='center'>KPI ((CSR PNP / Aktif) * Average)</td>".
                     "<td class='header month' align='center'>Visit</td>".
                     "<td class='header month' align='center'>Serve</td>".
                     "<td class='header month' align='center'>Serve/Visit</td>".
                     "<td class='header month' align='center'>Eskalasi</td>".
                     "<td class='header month' align='center'>Produce</td>".
                     "<td class='header month' align='center'>Produce/Eskalasi (a)</td>".
                     "<td class='header month' align='center'>Open Past(OP)</td>".
                     "<td class='header month' align='center'>Close out Past(COP)</td>".
                     "<td class='header month' align='center'>Jatuh Tempo(JT)</td>".
                     "<td class='header month' align='center'>Total Close (TC)</td>".
                     "<td class='header month' align='center'> (OP+COP+JT)/TC (b)</td>".                   
                     "<td class='header month' align='center'> STP ((a+b)/2)</td>".                   
                     "<td class='header month' align='center'> Customer Handling ((RT+STP)/2)</td>".                   
                     "</tr>";                     

		  while($rowgrap = mysql_fetch_row($grapres)){
      #PNP
      $kpi_pnp=0;
      if ($rowgrap[9] > $rowgrap[8]) {
        $kpi_pnp=$rowgrap[10];
      } 
      else if ($rowgrap[9] == 0)
      {
        $kpi_pnp=0;
      } 
      else if ($rowgrap[9] == $rowgrap[8])
      {
        $kpi_pnp=$rowgrap[10];
      } 
      else if ($rowgrap[8] > $rowgrap[9])
      {
        $kpi_pnp=round(($rowgrap[9]/$rowgrap[8])* $rowgrap[10],2);
      } 
      #Serve
      $kpi_serve=0;
      if ($rowgrap[4] == 0 || $rowgrap[5] == 0)
      {
        $kpi_serve=0;
      } 
      else if ($rowgrap[5] > $rowgrap[4])
      {
        $kpi_serve=100;
      } 
      else
      {
        $kpi_serve=round(($rowgrap[5]/$rowgrap[4])* 100,2);
      }      
      #Produce
      $kpi_produce=0;
      if ($rowgrap[6] == 0 || $rowgrap[7] == 0)
      {
        $kpi_produce=0;
      } 
      else if ($rowgrap[7] > $rowgrap[6])
      {
        $kpi_produce=100;
      } 
      else
      {
        $kpi_produce=round(($rowgrap[7]/$rowgrap[6])* 100,2);
      } 
      
      #Closing
      #$totaltiket=$openpast+$closeoutpast;+$jatuhtempo;
      #$persenclose=round(($totalclose/$totaltiket)*100,2);      
      $kpi_closing=0;
      $totaltiket=0;
      $totaltiket=$rowgrap[11]+$rowgrap[12]+$rowgrap[13];    
      if ($rowgrap[14] == 0)
      {
        $kpi_closing=0;
      } 
      else if ($totaltiket==0)
      {
        $kpi_closing=0;
      } 
      else
      {
        $kpi_closing=round(($rowgrap[14]/$totaltiket)* 100,2);
      } 
      $stp=round(($kpi_produce+$kpi_closing)/2,2);
      $ch=round(($stp+$kpi_serve)/2,2);      
			echo 	"<tr>".
                     	"<td class='valuel month'>$rowgrap[0]</td>".
                     	"<td class='valuer month'>$rowgrap[1]</td>".
                     	"<td class='valuer month'>$rowgrap[8]</td>".
                     	"<td class='valuer month'>$rowgrap[9]</td>".
                     	"<td class='valuer month'>$rowgrap[10]</td>".
                     	"<td class='valuer month' bgcolor='cyan'><b>$kpi_pnp</b></td>".
                     	"<td class='valuer month'>$rowgrap[4]</td>".
                     	"<td class='valuer month'>$rowgrap[5]</td>".
                     	"<td class='valuer month'><b>$kpi_serve %</b></td>".
                     	"<td class='valuer month'>$rowgrap[6]</td>".
                     	"<td class='valuer month'>$rowgrap[7]</td>".
                     	"<td class='valuer month'><b>$kpi_produce %</b></td>".
                     	"<td class='valuer month'>$rowgrap[11]</td>".
                     	"<td class='valuer month'>$rowgrap[12]</td>".
                     	"<td class='valuer month'>$rowgrap[13]</td>".
                     	"<td class='valuer month'>$rowgrap[14]</td>".
                     	"<td class='valuer month'><b>$kpi_closing %</b></td>".
                     	"<td class='valuer month'><b>$stp %</b></td>".
                     	"<td class='valuer month' bgcolor='cyan'><b>$ch %</b></td>".
                     	"</tr>";


      }
                     echo "<tr>".
                     "<td class='header month' colspan=19 style='font-size:12px;' align='left'>".
                     "<br><b><u>KETERANGAN</b></u>".
                     "<br>1. <u>Visit</u> : Data Kedatangan pelanggan, dihitung dari tiket yang dikeluarkan mesin antrian dalam 1 bulan.".
                     "<br>2. <u>Serve</u> : Data Pelanggan terlayani, direkam ke dalam aplikasi log transaksi (<a href='http://192.1.1.131/xmatic' title='Log Transaksi'>http://192.1.1.131/xmatic</a>) untuk setiap tiket antrian yang dilayani.".
                     "<br>3. <u>Eskalasi</u> : Jumlah data di dalam log transaksi yang membutuhkan koordinasi dengan GraPARI, dan disimpan dengan status <b>ESKALASI</b>".
                     "<br>4. <u>Produce</u> : Data Tiket GT-X yang dibuat terkait dengan status eskalasi. Jumlah Tiket GT-X harus seimbang dengan Jumlah Eskalasi.".
                     "<br>5. <u>CSR Aktif</u> : Jumlah CS dan Kasir yang masih aktif di CSDM per tanggal 15 - 25 bulan berjalan.".
                     "<br>6. <u>CSR PNP</u> : Jumlah CS dan Kasir yang melaksanakan PNP di Bulan Berjalan.".
                     "<br>7. <u>Average</u> : Nilai Rata - rata PNP dari CS dan Kasir yang melaksanakan.".
                     "<br>8. <u>Open Past</u> : Tiket di bulan sebelum bulan berjalan yang masih Open. Tiket Open ini harus diclose".
                     "<br>9. <u>Close Out past</u> : Tiket GTX di bulan sebelum bulan berjalan yang sudah diclose".
                     "<br>10. <u>Jatuh Tempo</u> : Tiket GT-X yang harud diclose oleh GeraiHALO di bulan berjalan. User SPV dan Back Office bisa melakukan closing tiket.".
                     "<br>11. <u>Total Close</u> : Total tiket GTX yang sudah diclose di GeraiHALO, baik tiket Close Out Past maupun tiket Jatuh Tempo. Kondisi ideal adalah Tidak ada tiket Open Past, dan tiket jatuh Tempo diselesaikan di bulan berjalan.".
                     "<br>12. <u>KPI Tiket</u> : Rata-rata rasio ticket produce (koordinasi dengan GraPARI), dan Tiket GTX untuk geraiHALO sudah diclose.".
                     "<br>13. <u>KPI Customer Handling</u> : Rata-rata Recorded Transaction dan Service Ticket performance.".                  
                     "</td>".
                     	"</tr>";

	     $ipaddress = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else{
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            }

            $logger = new Logger();
            $logger->setDBConnection($gateqm);
	     $userid = $_SESSION['uSeRiD'];
	     if(trim($userid) == ""){$userid = "{Anonymous}";}

	     $logger->Logging(HISTORYTYPE_VIEW_REPORT_GRAPARI_AREA,$userid,"View Report Area GraPARI $area Periode $selmonth - $selyear",$ipaddress);

            $gateqm->CloseConnection();
        }
        if (catch('Exception', $e)){
            echo $e->getMessage();
        }
    ?>

</div><!-- ende main wrap -->


</body></html>
