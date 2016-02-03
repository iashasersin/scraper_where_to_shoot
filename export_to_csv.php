<?php
 
$host = 'localhost'; // MYSQL database host adress
$db = 'manoperacraft_ro'; // MYSQL database name
$user = 'ro_usr'; // Mysql Datbase user
$pass = 'mazafaka1983_ro'; // Mysql Datbase password
 
// Connect to the database
$link = mysql_connect($host, $user, $pass);
mysql_select_db($db);
 
require 'exportcsv.inc.php';
 
$table="data_container"; // this is the tablename that you want to export to csv from mysql.
 
exportMysqlToCsv($table);
 
?>
