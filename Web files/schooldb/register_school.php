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
	$schoolName = $_POST['schoolname'];
	// Check for nullness on required fields
	if (!$schoolName) {
		$_SESSION['registerSchoolReturn'] .= "A school name must be provided to uniquely identify the school<br>";
		header ("location: create_school.php");
		die;
	}
	// Return error messages if there are any
	if (!empty($_SESSION['registerSchoolReturn'])) {
		header('location: create_school.php');
		die;
	}
/*
 * Firstly we need to insure that the school name provided is unique
 * in the class table
 */
/**************************SQL query*******************************************/
	$sql = "SELECT `school_name` FROM `school` WHERE `school_name` = ?";
	$params = array($schoolName);
	$result = mysqli_prepared_query($conn, $sql, "s", $params);
	$count = count($result);
	if($count != 0) {
		$_SESSION['registerschoolReturn'] = "The chosen school name is in use";
		header('location: create_school.php');
		die;
	}
	//Insert data
/**************************SQL query*******************************************/
	$sql = "INSERT INTO `school` (`school_name`) VALUES (?)";
	$params = array($schoolName);
	$result = mysqli_prepared_query($conn, $sql, "s", $params);
	if ($result) {
		$_SESSION['format'] = 'green';
		$_SESSION['registerSchoolReturn'] = "Class created successfully";
		header('location: create_school.php');
		die;
	} else {
		$_SESSION['registerSchoolReturn'] = "Failed to create school: " . mysqli_error($conn);
		header('location: create_school.php');
		die;
	}
}
mysqli_close($conn);