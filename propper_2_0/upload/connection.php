<?php
//$hostname = "10.1.96.237";
$hostname = "10.2.130.251";
$database = "service_outage";
$username = "qmatic";
$password = "qmatic123";

$koneksi = mysql_connect($hostname, $username, $password) or trigger_error(mysql_error(),E_USER_ERROR);
 
if ($koneksi){
	//echo "berhasil : ";
} else {
	?><script language="javascript">alert("Gagal Koneksi Database MySql !!")</script><?
}

mysql_select_db($database) or die("DBERROR");

//echo $_SERVER['DOCUMENT_ROOT'];

//$mysql = "SELECT * FROM score WHERE period = '201003';";

//$res = mysql_query($mysql) or die (mysql_error());

//if ($res) {
//	while ($row = mysql_fetch_row($res)) {
//		echo "1".$row[0];
//	}
//} else {
//	echo "gagal select";
//} 
?>