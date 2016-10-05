<?php
include('data_check.php');
session_start();
// Set a session timeout
if (!isset($_SESSION['login_user'])) {
	header('location: logout.php');
	die;
}
if ($_SESSION['timeout'] + 10 * 60 < time()) { //10 min timeout
	header('location: logout.php');
	die;
}
$_SESSION['timeout'] = time();
include('config.php'); //DB login details
//Connect to mySQL db
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	die;
} else {
	//Store the session
	$user_check=$_SESSION['login_user'];
	// SQL query to fetch users info
	$sql = "SELECT * FROM users WHERE user=?";
	$params = array($user_check);
	$ses_sql = mysqli_prepared_query($conn, $sql, "s", $params);
	$row = $ses_sql[0];
	$login_session = $row['user'];
	$access_level = $row['administrator'];
	if (isset($login_session)) {
		mysqli_close($conn);
	}
}
?>



