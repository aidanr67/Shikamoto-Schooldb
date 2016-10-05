<?php
include('session.php');

if (isset($_POST['submit'])) { // Only process data if submit is pressed
/*
 * Get student ID number from GET
 */
	if (isset($_GET['id']) && isset($_GET['classid'])) { //This should be set
	    $birthCertNumber = $_GET['id']; //Student's birth certificate number
	    $classNumber = $_GET['classid']; //used to return to correct class
	} else { //if 'id' is not set there is a serious error
	    print_r("ERROR: communication error, please call an administrator");
	    die;
	}

	//Establish DB connection
	$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
	if (!conn) {
		print_r($DBerror);
		die;
	}
	//Variables to handle optionals
	$_SESSION['format'] = 'red';
	if (!empty($_SESSION['note'])) {
		$_SESSION['newNoteReturn'] = "Please enter a note.";
		header('location: add_student_note.php?id=' . $birthCertNumber); //Return to form with errors
		die;
	}

	if (!isNoteValid($_POST['note'])) {
		$_SESSION['newNoteReturn'] = "Notes must be between 1 and 500 characters in length.";
		header('location: add_student_note.php?id=' . $birthCertNumber); //Return to form with errors
	}
	//Assign data to variable
	$note = $_POST['note'];
/***********************************SQL QUERY**********************************/
	//Add student information to student table
	$sql = "INSERT INTO student_notes (`student_birth_certificate_number`, `note`) 
		VALUES (?, ?)";
	$params = array($birthCertNumber, $note);
	$result = mysqli_prepared_query($conn, $sql, "ss", $params); 
	if($result) { //if note is inserted report success and return
		mysqli_close($conn); //Close database and return success message
		$_SESSION['updateClassReturn'] = "Note successfully added";
		$_SESSION['format'] = "green";
		header("location: edit_class.php?classid=" . $classNumber);
		die;
	}
}