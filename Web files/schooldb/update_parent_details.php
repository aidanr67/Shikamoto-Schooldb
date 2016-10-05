<?php
include('session.php');
//Reset error strings
$_SESSION['updatePrimaryReturn'] = "";
$_SESSION['updateSecondaryReturn'] = "";
$_SESSION['format'] = 'red';
if (!isset($_POST)) { //Post is not set, abort
	print_r("ERROR: No data recieved");
	die;
}
//Get childs birth cert number from session variable
$birthCertNumber = $_SESSION['currentbirthcertnumber'];
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) { //No DB connection, abort
	print_r($DBerror);
	die;
}
/* If an ID number was not recieved, abort
 * Else, check if this ID number can be matched in the DB
 * if no then it is a new secondary, else update as normal
 * The ID number is recieved through POST if this is a new parent entry
 * otherwise it's received through GET, if POST['idnumber'] is set GET['idnumber']
 * will be NULL
 */
if (!isset($_GET['idnumber']) && !isset($_POST['idnumber'])) { //id number not recieved, abort
	print_r("ERROR: The parents ID number was not recieved");
	die;
} else { //ID number was recieved, proceed
	//Set variables
	if (isset($_POST['idnumber'])) {
		$idNumber = $_POST['idnumber'];
	} else {
		$idNumber = $_GET['idnumber'];
	}
	$firstName = $_POST['firstname'];
	$lastName = $_POST['lastname'];
	$homeNumber = $_POST['homenumber'];
	$workNumber = $_POST['worknumber'];
	$cellNumber = $_POST['cellnumber'];
	$address = $_POST['address'];
	//First determine if this ID number exists or if we are creating a new parent
/***************************SQL query******************************************/
	$sql = "SELECT * FROM parents WHERE `id_number` = ?";
	$params = array($idNumber);
	$result = mysqli_prepared_query($conn, $sql, 's', $params);
	$count = count($result);
	if ($count == 0) { //Parent does not exist, create
		//This also means we are working with a secondary so set $error accordingly
		$error = 'updateSecondaryReturn';
		//Evaluate ID number
		if (!isIdNumberValid($idNumber)) {
			$_SESSION[$error] = $_SESSION[$error] . "ID number for the secondary guardian must be a 13 digit number";
			header('location: student_details.php');
			die;
		} else {
			//Create a skeleton entry, the rest will be filled in next
			$sql = "INSERT INTO parents (`id_number`, `childs_birth_certificate`, `primary_guardian`, `current`) 
			VALUES (?, ?, ?, ?)";
			$params = array($idNumber, $birthCertNumber, 0, 1);
			$result = mysqli_prepared_query($conn, $sql, "ssii", $params);
			if (!$result) {
				$_SESSION[$error] .= "Failed to create new parent" . mysqli_error($conn);
				header('location: student_details.php');
				die;
			}
		}
	}
	//Determine if parent is primary or secondary - This is purely for return errors
	$sql = "SELECT `primary_guardian` FROM parents WHERE id_number = ?";
	$params = array($idNumber);
	$result = mysqli_prepared_query($conn, $sql, "s", $params);
	$value =$result[0]['primary_guardian'];
	if ($value == 1 && $error == "") { //If not set above, set error string
		$error = 'updatePrimaryReturn';
	} else {
		$error = 'updateSecondaryReturn';
	}	
/*Evaluate input - ID number is not editable so evaluation is
 * not necissary
 */
	if (!isNameValid($firstName)) {
		$_SESSION[$error] = $_SESSION[$error] . "First name for the parent can only contain alphabeticles";
	}
	if (!isNameValid($lastName)) {
		$_SESSION[$error] = $_SESSION[$error] . "Last name for the parent can only contain alphabeticles";
	}
	if ($homeNumber != "") {
		if (!isPhoneNumberValid($homeNumber)) {
			$_SESSION[$error] = $_SESSION[$error] . "Home phone number for the primary guardian must be a 10 digit number";
		}
	}
	if ($workNumber != "") {
		if (!isPhoneNumberValid($workNumber)) {
			$_SESSION[$error] = $_SESSION[$error] . "Work phone number for the primary guardian must be a 10 digit number";
		}
	}
	if (!isPhoneNumberValid($cellNumber)) {
		$_SESSION[$error] = $_SESSION[$error] . "Cellphone number for the primary guardian must be a 10 digit number";
	}
	if ($_SESSION[$error] != "") {
		header("location: student_details.php?id=$birthCertNumber");
		die;
	}
/***********************SQL Queries***********************************/
	$sql = "UPDATE parents SET `first_name`=?, `last_name`=?, `home_number`=?, `work_number`=?, `cell_number`=?, `address`=? 
	WHERE `id_number` = ?";
	$params = array($firstName, $lastName, $homeNumber, $workNumber, $cellNumber, $address, $idNumber);
	if (mysqli_prepared_query($conn, $sql, "sssssss", $params)) {
		$_SESSION['format'] = 'green';
		$_SESSION[$error] = "Guardian details updated successfully";
		header("location: student_details.php?id=$birthCertNumber");
		die;
	} else {
		$_SESSION[$error] = "ERROR: Failed to update Guardian information" . mysqli_error($conn);
		header("location: student_details.php?id=$birthCertNumber");
		die;
	}
}