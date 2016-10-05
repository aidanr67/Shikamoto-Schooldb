<?php
include('session.php');
/*
 * This script updates a classes information based on the info passed to it
 * Class numbers can be changed to so there are 2 class number variables
 * one via get and one via post. If they are the same then the class number
 * was not changed. The database should cascade this change.
 */

$classNumber = $_GET['classid'];
$newClassNumber = $_POST['newclassnumber'];
$teacher = $_POST['teacher'];
$description = $_POST['description'];

$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	die;
}

if ($newClassNumber == "") {
	$_SESSION['updateClassReturn'] = "Class number cannot be blank <br>";
}
if ($teacher == "") {
	$_SESSION['updateClassReturn'] = "Teacher cannot be blank <br>";
}
if (!isClassNumberValid($classNumber)) {
	$_SESSION['updateClassReturn'] = $_SESSION['updateClassReturn'] . "Class numbers can only contain numbers and letters<br>";
}
if (!isIdNumberValid($teacher)) {
	$_SESSION['updateClassReturn'] = $_SESSION['updateClassReturn'] . "ID number must be a 13 digit number<br>";
}
if (!empty($_SESSION['updateClassReturn'])) {
	header ('location: edit_class.php');
}
if ($newClassNumber != $classNumber) { //If this is a new number check that it doesn't exist in the table
/************************************************SQL QUERY**********************/
	$sql = "SELECT `class_number` FROM class WHERE `class_number` = ?";
	$params = array($newClassNumber);
	$result = mysqli_prepared_query($conn, $sql, "s", $params);
	if (!$result) {
		$_SESSION['updateClassReturn'] = "No result for new class number " . mysqli_error($conn);
	}
	$count = count($result);
	if ($count != 0) {
		$_SESSION['updateClassReturn'] = "A class with this number already exists";
		header ('location: edit_class.php');
		die;
	}
}
//Chech that teacher exists
$sql = "SELECT * FROM staff WHERE `id_number` = ?";
$params = array($teacher);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
$count = count($result);
if ($count != 1) {
	$_SESSION['updateClassReturn'] = "The teacher's ID number does not exist on the system";
	header ('location: edit_class.php');
	die;
}

/**************************************SQL QUERY********************************/
$sql = "UPDATE class SET `class_number`='$newClassNumber', `teacher`='$teacher', `description`='$description'
WHERE class_number = ?";
$params = array($classNumber);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
if (!$result) {
	$_SESSION['updateClassReturn'] = "No result for update class " .mysqli_error($conn);
	header ('location: edit_class.php');
	die;
} else {
	$_SESSION['currentclass'] = $newClassNumber;
	$_SESSION['updateClassReturn'] = "Class updated";
	$_SESSION['format'] = 'green';
	header ('location: edit_class.php');
}
mysqli_commit($conn);
mysqli_close($conn);