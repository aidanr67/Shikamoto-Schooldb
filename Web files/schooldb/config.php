<?php
/******************************************************
 * config info for connecting DB
 ******************************************************/
$file = $_SERVER['DOCUMENT_ROOT'] . "/../dbpass.txt";
if (file_exists($file)) {
	$fh = fopen($file, r);
	$DBpassword = fgets($fh);
	fclose($handle);
} else {
	die ("No DB password provided");
}
$DBpassword = rtrim($DBpassword);
$DBhost = 'localhost';
$DBuser = 'schooladmin';
$DBdatabase = "School";
$DBerror = "ERROR: FAILED TO ESTABLISH A CONNECTION TO THE DATABASE";
//String for use on this site
$adminLevel1 = "Teacher: Can view student details and take register, can't edit student details";
$adminLevel2 = "Manager: Can create, edit, delete students";
$adminLevel3 = "Administrator: Can do everything, including creating users";
?>
