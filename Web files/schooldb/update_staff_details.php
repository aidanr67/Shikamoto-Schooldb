<?php
include('session.php');

$_SESSION['format'] = 'red';
if (!isset($_POST)) { //Post is set proceed
	print_r("ERROR: No data recieved");
	die;
}
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	die;
}
/* First we need to check which section was edited, staff or nok
 * We also need to get the current ID number from the SESSION variable
 * set in Staff_details.php
 */
$section = $_GET['section'];
$idNumber = $_SESSION['currentidnumber'];
if ($section == 'staff') { //Staff section was edited
	// We have the ID number, get all the other info
	$schoolId = $_POST['school']; //If 0 - Set NULL
	if ($schoolId == 0) {
		$schoolId = NULL;
	}
	$firstName = $_POST['firstname'];
	$lastName = $_POST['lastname'];
	$gender = $_POST['gender'];
	$employmentDate = $_POST['dateofemployment'];
	$homeNumber = $_POST['homenumber'];
	$cellNumber = $_POST['cellnumber'];
	$address = $_POST['address'];
	//Evaluate input
	if (!isNameValid($firstName)) {
		$_SESSION['updateStaffReturn'] = $_SESSION['updateStaffReturn'] . "First name for the staff member can only contain alphabeticles";
	}
	if (!isNameValid($lastName)) {
		$_SESSION['updateStaffReturn'] = $_SESSION['updateStaffReturn'] . "Last name for the staff member can only contain alphabeticles";
	}
	if (!isDateValid($employmentDate)) {
		$_SESSION['updateStaffReturn'] = $_SESSION['updateStaffReturn'] . "Date of employment of the staff member must be provided in dd/mm/yyyy format";
	}
	if ($homeNumber != "") {
		if (!isPhoneNumberValid($homeNumber)) {
			$_SESSION['updateStaffReturn'] = $_SESSION['updateStaffReturn'] . "Home phone number for the staff member must be a 10 digit number";
		}
	}
	if (!isPhoneNumberValid($cellNumber)) {
		$_SESSION['updateStaffReturn'] = $_SESSION['updateStaffReturn'] . "Cellphone number for the staff member must be a 10 digit number";
	}
	if ($_SESSION['updateStaffReturn'] != "") {
		header("location: staff_details.php?id=$idNumber");
		die;
	}
/*************************************SQL Query********************************/
	$sql = "UPDATE `staff` SET `first_name` = ?, `last_name` = ?, `gender` = ?, `date_of_employment` = ?, `home_number` = ?, `cell_number` = ?, `address` = ? 
	, `school_id` = ? WHERE `id_number` = ?";
	$params = array($firstName, $lastName, $gender, $employmentDate, $homeNumber, $cellNumber, $address, $schoolId, $idNumber);
	$result = mysqli_prepared_query($conn, $sql, "sssssssis", $params);
	if (!$result) {
		$_SESSION['updateStaffReturn'] .= "Failed to update staff member's details " . mysqli_error($conn) . "<br>";
		header ("location: staff_details.php?id=$idNumber");
		die;
	} else {
		$_SESSION['format'] = 'green';
		$_SESSION['updateStaffReturn'] = "Staff member's details updated successfully";
		header ("location: staff_details.php?id=$idNumber");
	}
} else { //nok section was edited
	$nokFirstName = $_POST['nokfirstname'];
	$nokLastName = $_POST['noklastname'];
	$nokHomeNumber = $_POST['nokhomenumber'];
	$nokWorkNumber = $_POST['nokworknumber'];
	$nokCellNumber = $_POST['nokcellnumber'];
	$nokAddress = $_POST['nokaddress'];	
	//Evaluate input
	if (!isNameValid($nokFirstName)) {
		$_SESSION['updateNokReturn'] = $_SESSION['updateNokReturn'] . "First name for the next of kin can only contain alphabeticles";
	}
	if (!isNameValid($nokLastName)) {
		$_SESSION['updateNokReturn'] = $_SESSION['updateNokReturn'] . "Last name for the next of kin can only contain alphabeticles";
	}
	if (!isNameValid($nokHomeNumber)) {
		$_SESSION['updateNokReturn'] = $_SESSION['updateNokReturn'] . "Home phone number for the next of kin must be a 10 digit number";
	}
	if (!isNameValid($nokWorkNumber)) {
		$_SESSION['updateNokReturn'] = $_SESSION['updateNokReturn'] . "Work phone number for the next of kin must be a 10 digit number";
	}
	if (!isNameValid($nokcellNumber)) {
		$_SESSION['updateNokReturn'] = $_SESSION['updateNokReturn'] . "Cellphone number for the next of kin must be a 10 digit number";
	}
	if (isset($_SESSION['updateNokReturn'])) {
		header("location: staff_details.php?id=$idNumber");
	}	
/*************************************SQL Query********************************/
	$sql = "UPDATE `staff` SET `nok_first_name` = ?, `nok_last_name` = ?, `nok_home_number` = ?, `nok_work_number` = ?, `nok_cell_number` = ?, `nok_address` = ?
	WHERE `id_number` = ?";
	$params = array($nokFirstName, $nokLastName, $nokHomeNumber, $nokWorkNumber, $nokCellNumber, $nokAddress, $idNumber);
	$result = mysqli_prepared_query($conn, $sql, "sssssss", $params);
	if (!$result) {
		$_SESSION['updateNokReturn'] .= "Failed to update staff member's details " . mysqli_error($conn) . "<br>";
		header ("location: staff_details.php?id=$idNumber");
	} else {
		$_SESSION['format'] = 'green';
		$_SESSION['updateNokReturn'] = "Next of kin details updated successfully";
		header ("location: staff_details.php?id=$idNumber");
	}
}
mysqli_close($conn); //Close DBs