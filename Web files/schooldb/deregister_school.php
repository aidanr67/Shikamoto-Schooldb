<?php
include('session.php');
/*
 * This script creates a new entry in the school table
 */

//Connect DB and check connection
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	die;
}
if (isset($_POST)) { //Post is set, proceed
	//Get the variables
	$schoolId = $_POST['school'];
	// Check for nullness on required fields
	if (!$schoolId) {
		$_SESSION['removeSchoolReturn'] .= "A school must be provided<br>";
		header ("location: remove_school.php");
		die;
	}
	// Return error messages if there are any
	if (!empty($_SESSION['removeSchoolReturn'])) {
		header('location: remove_school.php');
		die;
	}
/*
 * Firstly we need to insure that the school name provided is unique
 * in the class table
 */
/**************************SQL query*******************************************/
	$sql = "SELECT `school_id` FROM `school` WHERE `school_id` = ?";
	$params = array($schoolId);
	$result = mysqli_prepared_query($conn, $sql, "i", $params);
	$count = count($result);
	if($count == 0) {
		$_SESSION['removeschoolReturn'] = "The chosen school does not exist";
		header('location: remove_school.php');
		die;
	}
	//Remove school
/**************************SQL query*******************************************/
	$sql = "DELETE FROM `school` WHERE `school_id` = ?";
	$params = array($schoolId);
	$result = mysqli_prepared_query($conn, $sql, "i", $params);
	if ($result) {
		$_SESSION['format'] = 'green';
		$_SESSION['removeSchoolReturn'] = "School removed successfully";
		header('location: remove_school.php');
		die;
	} else {
		$_SESSION['removeSchoolReturn'] = "Failed to remove school: " . mysqli_error($conn);
		header('location: remove_school.php');
		die;
	}
}
mysqli_close($conn);