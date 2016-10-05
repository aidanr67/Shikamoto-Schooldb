<?php
include('session.php');

$_SESSION['format'] = 'red';
if (!isset($_POST)) {
	print_r("ERROR: No data recieved");
	die;
}
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	die;
}
/* First we need to ensure that the birth cert number was recieved
 * Then perform the sql UPDATEs
 */
if (!isset($_GET['id'])) { //birth cert number not recieved, abbort
	print_r("ERROR: The child's birth certificate number was "
	. "not recieved");
	die;
} else { //Birth cert number was recieved, proceed
	//Set birth cert variable
	$birthCertNumber = $_GET['id'];
	$schoolId = $_POST['school']; //if 0 - set to NULL
	if ($schoolId == 0) {
		$schoolId = NULL;
	}
	$firstName = $_POST['firstname'];
	$lastName = $_POST['lastname'];
	$birthDate = $_POST['dateofbirth'];
	$gender = $_POST['gender'];
	$admissionDate = $_POST['admissiondate'];
/*Evaluate input - birth cert number is not editable so evaluation is
 * not necissary
 */
	if (!isNameValid($firstName)) {
		$_SESSION['updateStudentReturn'] = $_SESSION['updateStudentReturn'] . "First name for the child can only contain alphabeticles";
	}
	if (!isNameValid($lastName)) {
		$_SESSION['updateStudentReturn'] = $_SESSION['updateStudentReturn'] . "Last name for the child can only contain alphabeticles";
	}
	if (!isDateValid($birthDate)) {
		$_SESSION['updateStudentReturn'] = $_SESSION['updateStudentReturn'] . "Date of birth of the child must be provided in dd/mm/yyyy format";
	}
	if (!isDateValid($admissionDate)) {
		$_SESSION['updateStudentReturn'] = $_SESSION['updateStudentReturn'] . "Date of admission of the child must be provided in dd/mm/yyyy format";
	}
	if (isset($_SESSION['updateStudentReturn'])) {
		header("location: student_details.php?id='$birthCertNumber'");
	}
/*********************** Student SQL Queries***********************************/
	$sql = "UPDATE students SET `first_name`=?, `last_name`=?, `birth_date`=?,`gender`=?, `date_of_admission`=?, `school_id`=?  
	WHERE `birth_certificate_number` = ?";
	$params = array($firstName, $lastName, $birthDate, $gender, $admissionDate, $schoolId, $birthCertNumber);
	if (mysqli_prepared_query($conn, $sql, "sssssss", $params)) {
		$_SESSION['format'] = 'green';
		$_SESSION['updateStudentReturn'] = "Student details updated successfully";
		header("location: student_details.php?id=$birthCertNumber");
	} else {
		$_SESSION['updateStudentReturn'] = "ERROR: Failed to update student information" . mysqli_error($conn);
		header("location: student_details.php?id='$birthCertNumber'");
	}
}
mysqli_close($conn); //Close DB
