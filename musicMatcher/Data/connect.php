<?php

$dbhost = 'localhost';			// database hostname

$dbuser = 'deb30295_MM';			// database username

$dbpass = 'project2011';			// database password

$dbname = 'deb30295_MM';			// databasename



$conn = mysql_connect($dbhost,$dbuser,$dbpass) or die (mysql_error());

mysql_select_db($dbname,$conn) or die (mysql_error());

?>