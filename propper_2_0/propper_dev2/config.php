<?php
/* define("HOST", "10.250.191.137"); // Host database
define("USER", "pnp"); // Usernama database
define("PASSWORD", "pnp123"); // Password database
define("DATABASE", "pnp"); // Nama database */

define("HOST", "10.250.193.238"); // Host database
define("USER", "apps"); // Usernama database
define("PASSWORD", "B4nC1@K35uRuP4nkwoK"); // Password database
define("DATABASE", "pnp"); // Nama database

$condb = mysql_connect(HOST,USER,PASSWORD);
$mysql = mysql_select_db(DATABASE,$condb);

?>