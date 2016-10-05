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
	//Variables to handle optionals
	$isSecondGuardian = FALSE;
	$isSecondAddressSame = TRUE;
	$isPartialEntry = FALSE; //This will be used to determine if a rollback is neccesary

	//Set a session array for return
	$_SESSION['inputparams'] = $_POST;

	//Set session variable format to red for errors
	$_SESSION['format'] = 'red';
	$_SESSION['registerStudentReturn'] = "";
	if(isset($_POST)) { //Post is set, save data
		//Check all required fields are set
		if (empty($_POST['firstname'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "First name for the child must be provided <br>";
		}
		if (empty($_POST['lastname'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Last name for the child must be provided <br>";
		}
		if (empty($_POST['birthcertnumber'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "A valid 13 digit birth certificate number for the child must be provided <br>";
		}
		if (empty($_POST['dateofbirth'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "A date of birth for the child must be provided <br>";
		}
		if (empty($_POST['guardian1firstname'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "First name for the primary guardian must be provided <br>";
		}
		if (empty($_POST['guardian1lastname'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Last name for the primary guardian must be provided <br>";
		}
		if (empty($_POST['guardian1idnumber'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "ID number for the primary guardian must be provided <br>";
		}
		if (empty($_POST['guardian1cellnumber'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Cellphone number for the primary guardian must be provided <br>";
		}
		if (empty($_POST['guardian1address'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Address for the primary guardian must be provided <br>";
		}
		//If second guardian checkbox is checked varify second guardian data
		if ($_POST['isguardian2'] == "yes") {
			$isSecondGuardian = TRUE;
			if (empty($_POST['guardian2firstname'])) {
				$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "First name for the secondary guardian must be provided <br>";
			}
			if (empty($_POST['guardian2lastname'])) {
				$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Last name for the secondary guardian must be provided <br>";
			}
			if (empty($_POST['guardian2idnumber'])) {
				$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "ID number for the secondary guardian must be provided <br>";
			}
			if (empty($_POST['guardian1cellnumber'])) {
				$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Cellphone number for the secondary guardian must be provided <br>";
			}
			if ($_POST['guardian2addresssame'] != "yes") {
				$isSecondAddressSame = FALSE;
				if (empty($_POST['guardian2address'])) {
					$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Address for the secondary guardian must be provided <br>";
				}
			}
		}
		if (!empty($_SESSION['registerStudentReturn'])) {
			header('location: add_student.php'); //Return to form with errors
			die;
		}
		//Check validity of entered fields using functions in data_check.php
		$_SESSION['registerStudentReturn'] = "";
		if (!isNameValid($_POST['firstname'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "First name for the child can only contain alphabeticles <br>";
		}
		if (!isNameValid($_POST['lastname'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Last name for the child can only contain alphabeticles <br>";
		}
		if (!isIdNumberValid($_POST['birthcertnumber'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Birth certificate number for the child must be a 13 digit number <br>";
		}
		if (!isDateValid($_POST['dateofbirth'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Date of birth of the child must be provided in dd/mm/yyyy format <br>";
		}
		if (!isNameValid($_POST['guardian1firstname'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "First name for the primary guardian can only contain alphabeticles <br>";
		}
		if (!isNameValid($_POST['guardian1lastname'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Last name for the primary guardian can only contain alphabeticles <br>";
		}
		if (!isIdNumberValid($_POST['guardian1idnumber'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "ID number for the primary guardian must be a 13 digit number <br>";
		}
		if (!isPhoneNumberValid($_POST['guardian1cellnumber'])) {
			$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Cellphone number for the primary guardian must be a 10 digit number <br>";
		}
		if ($isSecondGuardian) {
			if (!isNameValid($_POST['guardian2firstname'])) {
				$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "First name for the secondary guardian can only contain alphabeticles <br>";
			}
			if (!isNameValid($_POST['guardian2lastname'])) {
				$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Last name for the secondary guardian can only contain alphabeticles <br>";
			}
			if (!isIdNumberValid($_POST['guardian2idnumber'])) {
				$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "ID number for the secondary guardian must be a 13 digit number <br>";
			}
			if (!isPhoneNumberValid($_POST['guardian2cellnumber'])) {
				$_SESSION['registerStudentReturn'] = $_SESSION['registerStudentReturn'] . "Cellphone number for the secondary guardian must be a 10 digit number <br>";
			}
		}
		if (!empty($_SESSION['registerStudentReturn'])) {
			header('location: add_student.php'); //Return to form with errors
			die;
		}
		//Assign data to variables -- This makes the SQL queries much easier to read
		$schoolId = $_POST['school'];
		$dateOfAdmission = $_POST['dateofadmission'];
		$childFirstName = $_POST['firstname'];
		$childLastName = $_POST['lastname'];
		$childGender = $_POST['gender'];
		$childBirthCertNumber = $_POST['birthcertnumber'];
		$childDateOfBirth = $_POST['dateofbirth'];
		$primaryGuardianFirstName = $_POST['guardian1firstname'];
		$primaryGuardianLastName = $_POST['guardian1lastname'];
		$primaryGuardianIdNumber = $_POST['guardian1idnumber'];
		$primaryGuardianCellNumber = $_POST['guardian1cellnumber'];
/*Home and work numbers may be empty but assigning them anyway takes 
 * no more effort than checking if they are empty or not and adding 
 * them to the query is easier than having multiple queries 
 */
		$primaryGuardianHomeNumber = $_POST['guardian1homenumber'];
		$primaryGuardianWorkNumber = $_POST['guardian1worknumber'];
		$primaryGuardianAddress = $_POST['guardian1address'];
		if ($isSecondGuardian) {
			$secondaryGuardianFirstName = $_POST['guardian2firstname'];
			$secondaryGuardianLastName = $_POST['guardian2lastname'];
			$secondaryGuardianIdNumber = $_POST['guardian2idnumber'];
			$secondaryGuardianCellNumber = $_POST['guardian2cellnumber'];
			if ($isSecondAddressSame) {
				$secondaryGuardianHomeNumber = $_POST['guardian1homenumber'];
			} else {
				$secondaryGuardianHomeNumber = $_POST['guardian2homenumber'];
			}
			$secondaryGuardianWorkNumber = $_POST['guardian2worknumber'];
			if (!$isSecondAddressSame){
				$secondaryGuardianAddress = $_POST['guardian2address'];
			}
		}
/***********************************SQL QUERUES*********************************************************************************/
		//Add student information to student table
		$sql = "INSERT INTO students (`birth_certificate_number`, `first_name`, `last_name`, `birth_date`, `gender`, `date_of_admission`, `school_id`) 
			VALUES (?, ?, ?, ?, ?, ?, ?)";
		$params = array($childBirthCertNumber, $childFirstName, $childLastName, $childDateOfBirth, $childGender, $dateOfAdmission, $schoolId);
		$result = mysqli_prepared_query($conn, $sql, "ssssssi", $params); 
		if($result) { //if child is inserted proceed with guardians
			//add primary guardian to parents table
			$sql = "INSERT INTO parents (`id_number`, `childs_birth_certificate`, `first_name`, `last_name`, `home_number`, `work_number`, 
					`cell_number`, `address`, `primary_guardian`, `current`) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$params = array($primaryGuardianIdNumber, $childBirthCertNumber, $primaryGuardianFirstName, $primaryGuardianLastName, $primaryGuardianHomeNumber,
				$primaryGuardianWorkNumber, $primaryGuardianCellNumber, $primaryGuardianAddress, 1, 1);
			$result = mysqli_prepared_query($conn, $sql, "ssssssssii", $params); 
			if(!$result) { //If guardian failed mark entry as partial report error
				$isPartialEntry = TRUE;
				$_SESSION['registerStudentReturn'] = "ERROR: " . "<br>" . mysqli_error($conn);
			}
			if ($isSecondGuardian) { //If a secondary guardian exists insert them
				if (!$isSecondAddressSame) { //Create entry with secondary address, else create with primary address
					//Add secondary guardian to parents table
					$sql = "INSERT INTO parents (`id_number`, `childs_birth_certificate`, `first_name`, `last_name`, `home_number`, `work_number`, 
					`cell_number`, `address`, `primary_guardian`, `current`) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
					$addressUsed = $secondaryGuardianAddress;
				} else {
					$sql = "INSERT INTO parents (`id_number`, `childs_birth_certificate`, `first_name`, `last_name`, `home_number`, `work_number`,
					`cell_number`, `address`, `primary_guardian`, `current`) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
					$addressUsed = $primaryGuardianAddress;
				}
				$params = array($secondaryGuardianIdNumber, $childBirthCertNumber, $secondaryGuardianFirstName,
					$secondaryGuardianLastName, $secondaryGuardianHomeNumber, $secondaryGuardianWorkNumber, $secondaryGuardianCellNumber,
					$addressUsed, 0, 1);
				$result = mysqli_prepared_query($conn, $sql, "ssssssssii", $params); 
				if(!$result) {//If secondary guardian insert fails mark partial entry and report error
					$isPartialEntry = TRUE;
					$_SESSION['registerStudentReturn'] = "ERROR: " . $sql . "<br>" .  mysqli_error($conn);
				}
			}// No secondary guardian
					//Create a balance for the student
			$sql = "INSERT INTO student_balance (`birth_certificate_number`, `balance`) 
			VALUES (?, ?)";
			$params = array($childBirthCertNumber, 0);
			$result = mysqli_prepared_query($conn, $sql, "si", $params);
		if (!$result) {
			$isPartialEntry = TRUE;
			$_SESSION['registerStudentReturn'] = "ERROR: " . "<br>" .mysqli_error($conn);
		}
		} else { //If insert into student fails
			$isPartialEntry = TRUE;
			$_SESSION['registerStudentReturn'] = "ERROR: " . $sql . "<br>" .mysqli_error($conn);
		}
		//Check that all entries succeeded and return success
		if ($isPartialEntry) { //One or more queries failed
			mysqli_rollback($conn); /*If this command query fails them it's probable that 
 * all queries have failed and a ROLLBACK is therefore not neccesary
 */
			mysqli_close($conn); //close DB
			header("location: add_student.php");
			die;
		} else { //all queries were successfull
			if (mysqli_commit($conn)) { //Commit transaction
				mysqli_close($conn); //Close database and return success message
				$_SESSION['registerStudentReturn'] = "Student information captured successfully";
				$_SESSION['format'] = "green";
				$_SESSION['formdata'] = '';
				header("location: add_student.php");
				die;
			}
		}
	}
}