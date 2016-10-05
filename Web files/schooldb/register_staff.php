<?php
include('session.php');

if (isset($_POST['submit'])) { // Only process data if submit is pressed
	//Establish DB connection
	$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
	if (!conn) {
		print_r($DBerror);
		die;
	}
	mysqli_autocommit($conn, FALSE);
	//Set a session array for return
	$_SESSION['inputparams'] = $_POST;
	//Set session variable format to red for errors
	$_SESSION['format'] = 'red';
	$_SESSION['registerStaffReturn'] = "";
	if(isset($_POST)) { //Post is set, save data
	//Check all required fields are set
		if (empty($_POST['firstname'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "First name must be provided <br>";
		}
		if (empty($_POST['lastname'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "Last name must be provided <br>";
		}
		if (empty($_POST['idnumber'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "A valid 13 digit ID number must be provided <br>";
		}
		if (empty($_POST['nokfirstname'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "First name for the next of kin must be provided <br>";
		}
		if (empty($_POST['noklastname'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "Last name for the next of kin must be provided <br>";
		}
		if (empty($_POST['nokcellnumber'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "Cellphone number for the next of kin must be provided <br>";
		}
		if (!empty($_SESSION['registerStaffReturn'])) {
			header('location: add_staff.php'); //Return to form with errors
			die;
		}
		//Check validity of entered fields
		$_SESSION['registerStaffReturn'] = "";
		if (!isDateValid($_POST['employmentdate'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "Employment must be provided in dd/mm/yyyy format";
		}
		if (!isNameValid($_POST['firstname'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "First name can only contain alphabeticles";
		}
		if (!isNameValid($_POST['lastname'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "Last name can only contain alphabeticles";
		}
		if (!isIdNumberValid($_POST['idnumber'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "ID number must be a 13 digit number";
		}
		if (!isNameValid($_POST['nokfirstname'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "First name for the next of kin can only contain alphabeticles";
		}
		if (!isNameValid($_POST['noklastname'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "Last name for the next of kin can only contain alphabeticles";
		}
		if (!isPhoneNumberValid($_POST['nokcellnumber'])) {
			$_SESSION['registerStaffReturn'] = $_SESSION['registerStaffReturn'] . "Cellphone number for the next of kin must be a 10 digit number";
		}
		if (!empty($_SESSION['registerStaffReturn'])) {
			header('location: add_student.php'); //Return to form with errors
			die;
		}	
		//Assign data to variables -- This makes the SQL queries much easier to read
		$schoolId = $_POST['school'];
		$employmentDate = $_POST['employmentdate'];
		$firstName = $_POST['firstname'];
		$lastName = $_POST['lastname'];
		$gender = $_POST['gender'];
		$idNumber = $_POST['idnumber'];
		$homeNumber = $_POST['homenumber'];
		$cellNumber = $_POST['cellnumber'];
		$address = $_POST['address'];
		$nokFirstName = $_POST['nokfirstname'];
		$nokLastName = $_POST['noklastname'];
		$nokCellNumber = $_POST['nokcellnumber'];
/*Home and work numbers may be empty but assigning them anyway takes 
 * no more effort than checking if they are empty or not and adding 
 * them to the query is easier than having multiple queries 
 */
		$nokHomeNumber = $_POST['nokhomenumber'];
		$nokWorkNumber = $_POST['nokworknumber'];
		$nokAddress = $_POST['nokaddress'];
/***********************************SQL QUERUES*********************************************************************************/
//Add staff member information to staff table
		$sql = "INSERT INTO staff (`id_number`, `first_name`, `last_name`, `gender`, `date_of_employment`, `home_number`,
		`cell_number`, `address`, `current`, `nok_first_name`, `nok_last_name`, `nok_home_number`, `nok_work_number`, `nok_cell_number`,
		`nok_address`, `school_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$params = array($idNumber, $firstName, $lastName, $gender, $employmentDate, $homeNumber, $cellNumber, $address, 1, $nokFirstName,
			$nokLastName, $nokHomeNumber, $nokWorkNumber, $nokCellNumber, $nokAddress, $schoolId);
		$result = mysqli_prepared_query($conn, $sql, "ssssssssissssssi", $params);
		if(!$result) { //if entry failed, report
			$_SESSION['registerStaffReturn'] = "ERROR: " . $sql . "<br>" .  mysqli_error($conn);
			header('location: add_staff.php');
			die;
		}
	}
	if (mysqli_commit($conn)) { //Commit transaction
		mysqli_close($conn); //Close database and return success message
		$_SESSION['registerStaffReturn'] = "Staff information captured successfully";
		$_SESSION['format'] = "green";
		$_SESSION['formdata'] = '';
		header("location: add_staff.php");
		die;
	}
}