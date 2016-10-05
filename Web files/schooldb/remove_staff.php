<?php
include('config.php');
/* 
 * Determine what the staff members current status is and change if
 */

//Get ID number from GET
$idNumber = $_GET['id'];

//Connect DB
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);

if (!$conn) {
	print_r($DBerror);
	return 0;
	die;
}

/************************************SQL QUERY***************************************************/
$sql = "SELECT current FROM staff WHERE `id_number` = ?";
$params = array($idNumber);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
if (!$result) {
	print_r(mysqli_error($conn));
	return 0;
	die;
}

$row = $result[0];

$isCurrent = ($row['current'] == 1);
if ($isCurrent) {
	$value = 0; // change to not current
} else {
	$value = 1; //change to current
}
/************************************SQL QUERY***************************************************/
$sql = "UPDATE staff SET `current` = ? WHERE id_number = ?";
$params = array($value, $idNumber);
$result = mysqli_prepared_query($conn, $sql, "ns", $params);
if (!$result) {
	print_r(mysqli_error($conn));
	return 0;
	die;
} else {
	header ("location: staff_details.php?id=$idNumber");
	return 1;
	die;
}

//Disconnect DB
mysqli_close($conn);
