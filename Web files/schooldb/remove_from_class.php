<?php
include('session.php');
/*
 * Get students birth cert number from $_GET['id'] and 
 * nullify the class column in the student table for that student
 */
$birthCertNumber = $_GET['id'];

$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);

if (!$conn) {
	print_r($DBerror);
	die;
}

/**************************************SQL QUERY******************************/
$sql = "UPDATE students SET class=NULL WHERE birth_certificate_number = ?";
$params = array($birthCertNumber);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
if (!$result) {
	print_r("No result" . mysqli_error($conn));
	die;
}

mysqli_commit($conn);
mysqli_close($conn);

header('location: edit_class.php');
