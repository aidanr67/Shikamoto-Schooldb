<?php
include('session.php');

/* Get the birth certificate number. Add student to student history table.
 * Remove student from student table. Mark parent as non-current parent.
 */
/*******************************Variables*************************************/
$birthCertNumber = ""; //Host childs birth certificate number
 
//Get birth cert number
if (isset($_GET['id'])) {
	$birthCertNumber = $_GET['id'];
} else {
	header("location: find_student.php"); //We should probably handle this situation but
//it's extremely unlikely
}
 //Connect DB
 $conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
 if (!$conn) {
 	print_r($DBerror);
 	die;
 }
 
 /*****************************SQL Queries************************************/
 //Get student info
 $sql = "SELECT * FROM students WHERE birth_certificate_number = ?";
 $params = array($birthCertNumber);
 $result = mysqli_prepared_query($conn, $sql, "s", $params);
 if (!$result) {
 	print_r("ERROR: FAILED TO GET STUDENT INFORMATION " . mysqli_error($conn));
 	die;
 }
 
 $row = $result[0];
 //Create varibles for the data we want to save in student_history
 $firstName = $row['first_name'];
 $lastName = $row['last_name'];
 $birthDate = $row['birth_date'];
 $gender = $row['gender'];
 $admissionDate = $row['date_of_admission'];
 $removalDate = date('Y-m-d');
 
 //Delete this entry in students
 $sql = "DELETE FROM `students` WHERE `birth_certificate_number` = ?";
 $params = array($birthCertNumber);
 mysqli_prepared_query($conn, $sql, "s", $params);
 
 //Add into student_history
 $sql = "INSERT INTO student_history (`birth_certificate_number`, `first_name`, `last_name`, 
 		`birth_date`, `gender`, `admission_date`, `removal_date`) VALUES(?, ?, ?, ?, ?, ?, ?)";
 $params = array($birthCertNumber, $firstName, $lastName, $birthDate, $gender, $admissionDate, $removalDate);
 if (!mysqli_prepared_query($conn, $sql, "sssssss", $params)) {
 	print_r("ERROR: FAILED TO ADD STUDENT TO HISTORY " . mysqli_error($conn));
 	die;
 }
 
 //Mark parent as non-current
 $sql = "UPDATE parents SET `current` = 0 WHERE `childs_birth_certificate` = ?";
 $params = array($birthCertNumber);
 if (!mysqli_prepared_query($conn, $sql, "s", $params)) {
 	print_r("ERROR: FAILED TO MARK PARENT AS NON_CURRENT " . mysqli_error($conn));
 }
 
 mysqli_commit($conn); //Commit transaction
 mysqli_close($conn);
 header('location: find_student.php');
 
 