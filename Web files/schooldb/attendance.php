<?php
include('session.php');
/*
 * Get attendace info from POST and edit the student_attendance table
 * accordingly.
 */
$_SESSION['format'] = "red"; // set error msg format
$isRegisterTaken = $_SESSION['registerTaken']; //Whether todays register has been taken

$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
}

//Get the class number from the session variable set in today_register.php
$classNumber = $_SESSION['currentclass'];
//Get all the students in that class from the DB
/*************************************SQL QUERY********************************/
$sql = "SELECT birth_certificate_number FROM students WHERE `class` = ?";
$params = array($classNumber);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
if (!$result) {
	$_SESSION['registerReturn'] = "No result for SELECT birth cert numbers from "
	. "students table <br>" . mysqli_error($conn) . "<br";
	header("location: today_register.php");
	die;
} else {
/*
 * Now that we have the student numbers in the class we can use those numbers 
 * to access the POSTs for each student and check for 'present' in the POST and 
 * edit the DB accordingly
 */
	$date = date('Y-m-d');
	foreach ($result as $row) {
		$birthCertNumber = $row['birth_certificate_number'];
		if ($_POST[$birthCertNumber] == 'present') {
			$attendance = 'Y';
		} else {
			$attendance = 'N';
		}
/*************************************SQL QUERY********************************/
		//Protect against mysql injection
		$birthCertNumber = stripslashes($birthCertNumber);
		$birthCertNumber = mysqli_real_escape_string($conn, $birthCertNumber);
		$date = stripslashes($date);
		$date = mysqli_real_escape_string($conn, $date);
		$attendance = stripslashes($attendance);
		if (!$isRegisterTaken) { //Register has not been taken, INSERT
			$sql = "INSERT INTO student_attendance (birth_certificate_number, `date`, attended) "
			. "VALUES (?, ?, ?)";
			$params = array($birthCertNumber, $date, $attendance);
		} else { //Register has been taken, UPDATE
			$sql = "UPDATE student_attendance SET `attended` = ?
			WHERE `birth_certificate_number` = ? AND `date` = ?";
			$params = array($attendance, $birthCertNumber, $date);
		}
		if (!mysqli_prepared_query($conn, $sql, "sss", $params)) { //query student_attendance table
			$_SESSION['registerReturn'] = "No result inserting into student_attendance "
			. "<br> " . mysqli_error($conn); "<br>";
			header('location: today_register.php');
			die;
		}
	}
	$_SESSION['format'] = "green";
	$_SESSION['registerReturn'] = "Register submitted successfully <br>";
	header('location: today_register.php');
}