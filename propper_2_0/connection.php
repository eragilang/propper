<?php
//$hostname = "10.1.96.237";
/* $hostname = "10.250.191.137";
$database = "pnp";
$username = "pnp";
$password = "pnp123"; */

$hostname = "10.250.193.238";
$database = "pnp";
$username = "apps";
$password = "B4nC1@K35uRuP4nkwoK";

$koneksi = mysql_connect($hostname, $username, $password) or trigger_error(mysql_error(),E_USER_ERROR);
 
if ($koneksi){
	//echo "berhasil : ";
} else {
	?><script language="javascript">alert("Gagal Koneksi Database MySql !!")</script><?
}

mysql_select_db($database) or die("DBERROR");
 
?>
