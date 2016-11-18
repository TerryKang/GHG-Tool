<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

$dbhost = "ec2-54-149-241-235.us-west-2.compute.amazonaws.com"; // hostname
$dbname = "ghgdb";   // database
$dbuser = "group11"; // username
$dbpass = "group11"; // password

$connectionInfo = array( "Database"=>$dbname, "UID"=>$dbuser, "PWD"=>$dbpass);
$con = sqlsrv_connect( $dbhost, $connectionInfo);

if(!$con) {
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}
?>