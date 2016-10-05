<?php
include('config.php');
include('data_check.php');
session_start();
$error = ''; //string to store error messages
//Execute query to get schools before submit
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	die;
} else {
	//Get school list from DB
	$sql = "SELECT * FROM school";
	$result = mysqli_prepared_query($conn, $sql);
	if (!$result) {
	   	print_r(mysqli_error($conn));
    	return 0;
	    die;
    }
	$schools = $result;
	mysqli_close($conn);
}

if (isset($_POST['submit'])) {
	if (empty($_POST['username']) || empty($_POST['password'])) {
		$error = "Username or password invalid \n";
	} else {
		//Define username and passord
		$schoolId = $_POST['schoolid'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		//Connect to DB
		$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
		if (!$conn) {
			print_r($DBerror);
			die;
		} else {
			//Protect against mysql injection
			$username = stripslashes($username);
			$password = stripcslashes($password);
			$username = mysqli_real_escape_string($conn, $username);
			$password = mysqli_real_escape_string($conn, $password);
			//Query to fetch username and password from DB
			$sql = "SELECT * FROM users WHERE user = ? AND password = ?";
			$params = array($username, $password);
			$result = mysqli_prepared_query($conn, $sql, "ss", $params);
			$row = $result[0];
			$admin = $row['administrator'];
			//Check if user has acces to this school
			$school = $row['school_id'];
			if (!is_null($school) AND $school != $schoolId) { //school restriction
					$error = "You are not permitted to access this school.\n";
			} else {
				if ($admin < 1) {
					$error = "Only administrators can access this site.\n";
				} else {
					$rowCount = count($result);
					if ($rowCount == 1) {
						$_SESSION['login_user'] = $username;
						// Refers to the school selected, 0 is all schools
						$_SESSION['school'] = $schoolId;
						$_SESSION['timeout'] = time();
						$_SESSION['admin_level'] = $admin;
						header("location: admin.php");
					} else {
						$error = "Username or password invalid.\n";
					}
				}
			}
		}
	mysqli_close($conn); //Close DB
	}
}
?>
