<?php
include('session.php');
/*
 * This script creates a new entry in the class table
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
	$classNumber = $_POST['classnumber'];
	$teacher = $_POST['teacher'];
	$description = $_POST['description'];
	// Check for nullness on required fields
	if (!$classNumber) {
		$_SESSION['registerClassReturn'] .= "A class number must be provided to uniquely identify the class<br>";
		header ("location: create_class.php");
		die;
	}
	if (!$teacher) {
		$_SESSION['registerClassReturn'] .= "Please provide a teacher for this class <br>";
	}
/*
 * Class numbers at this point can really be anything, The school
 * has no standard for this so no we'll just insure that no special 
 * characters are used.
 */
	if (!isClassNumberValid($classNumber)) {
		$_SESSION['registerClassReturn'] = $_SESSION['registerClassReturn'] . "Class numbers can only contain numbers and letters";
	}
	if (!isIdNumberValid($teacher)) {
		$_SESSION['registerClassReturn'] = $_SESSION['registerClassReturn'] . "ID number must be a 13 digit number,<br>"
		. "hint: use the find option for the ID number";
	}
	// Return error messages if there are any
	if (!empty($_SESSION['registerClassReturn'])) {
		header('location: create_class.php');
		die;
	}
/*
 * Firstly we need to insure that the class number provided is unique
 * in the class table
 */
/**************************SQL query*******************************************/
	$sql = "SELECT `class_number` FROM `class` WHERE `class_number` = ?";
	$params = array($classNumber);
	$result = mysqli_prepared_query($conn, $sql, "s", $params);
	$count = count($result);
	if($count != 0) {
		$_SESSION['registerClassReturn'] = "The chosen class number is in use";
		header('location: create_class.php');
		die;
	} else {
			//We need to also insure that the ID number of the teacher is valid
/**************************SQL query*******************************************/
		$sql = "SELECT `id_number` FROM `staff` WHERE `id_number` = ?";
		$params = array($teacher);
		$result = mysqli_prepared_query($conn, $sql, "s", $params);
		$count = count($result);
		if ($count == 0) {
			$_SESSION['registerClassReturn'] = "A staff member with ID number '$teacher' "
			. "cannot be found in the system.";
			header('location: create_user.php');
			die;
		}
	//Insert data
/**************************SQL query*******************************************/
	$sql = "INSERT INTO `class` (`class_number`, `teacher`, `description`, `school_id`) 
		VALUES (?, ?, ?, ?)";
	$params = array($classNumber, $teacher, $description, $schoolId);
	$result = mysqli_prepared_query($conn, $sql, "sssi", $params);
	if ($result) {
		$_SESSION['format'] = 'green';
		$_SESSION['registerClassReturn'] = "Class created successfully";
		header('location: create_class.php');
		die;
	} else {
		$_SESSION['registerClassReturn'] = "Failed to create class: " . mysqli_error($conn);
		header('location: create_class.php');
		die;
		}
	}
}
mysqli_close($conn);