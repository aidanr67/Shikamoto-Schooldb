<?php
include('session.php');

$error = "";
/*
 * Receive data for staff sign ins from POST. Verify input for each 
 * staff member. Staff data recieved as id_numberField. eg. date123456789
 * First split the POST array into an n dimentional array for the n staff menbers.
 * The n dimaentional array can then be inserted directly into the DB.
 */

$staffSignIn = array(); //To hold the n dimentional array
// Get all the staff ID numbers from the date key fields of POST
foreach (array_keys($_POST) as $post) {
	if (strpos($post, 'date') !== false) { //All keys containing the word date
		$id = str_replace("date", "", $post);
		$staffSignIn[$id] = array();  
	}
}
//Now that we have an array of ID numbers we can add the date, time_in and time_out data to this
foreach (array_keys($staffSignIn) as $staff) {
	$staffSignIn[$staff]['date'] = $_POST['date' . $staff];
	$staffSignIn[$staff]['time_in'] = $_POST['time_in' . $staff];
	$staffSignIn[$staff]['time_out'] = $_POST['time_out' . $staff];
}
// We'll also need the array of id numbers
$ids = array_keys($staffSignIn);
//Now that we have the n dimentional array we can insert the data into the DB
//Connect DB
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
   print_r($DBerror);
   die;
}

foreach ($ids as $id) {
	if (isset($_POST[$id])) {
		//If time_out is less that time_in then we must report an error and return
		if(isset($staffSignIn['time_out']) && $staffSignIn[$id]['time_out'] < $staffSignIn[$id]['time_in']) {
			$error .= "Time out cannot be before time in for staff number: " . $id . "<br>";
		} elseif (!isset($staffSignIn[$id]['time_in']) && isset($staffSignIn[$id]['time_out'])) { 
			//Also, can't set time out id time in is not set for this staff member
			$error .= "Cannot set time out when time in has not been set for staff number: " . $id . "<br>";
		} else {
			/*****************SQL QUERY**********************/
			$sql = "INSERT INTO `staff_attendance` (`staff_id_number`, `date`, `time_in`, `time_out`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `time_in` = ?, `time_out` = ?;";
			$params = array($id, $staffSignIn[$id]['date'], $staffSignIn[$id]['time_in'], $staffSignIn[$id]['time_out'], $staffSignIn[$id]['time_in'], $staffSignIn[$id]['time_out']);
			$result = mysqli_prepared_query($conn, $sql, "ssssss", $params);
			if (!$result) {
				print_r("ERROR: FAILED TO ADD STAFF ATTENDANCE " . mysqli_error($conn));
		  		die;
			}
		}
	}
}

if ($error != "") {
	$_SESSION['staffSignInReturn'] = $error;
	$_SESSION['format'] = "red";
}

mysqli_close($conn);
header("location: staff_signin.php");
