<?php
include('session.php');

$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	die;
}
if (!isset($_GET['user'])) {
	print_r("Communication error, call administrator");
	die;
}
$oldUsername = $_GET['user'];
//If post submit is set, proceed
if (isset($_POST["submitedituser"])) {
	//Get posted data, check it and return error messages if neccissary
	if (empty($_POST['username'])) {
		$_SESSION['userReturn'] .= 'Please provide a username <br>';
	}
	if (empty($_POST['password'])) {
		$_SESSION['userReturn'] .= 'Please provide a password <br>';
	}
	if (empty($_POST['passwordconfirm'])) {
		$_SESSION['userReturn'] .= 'Please confirm your password <br>';
	}
	if (!isUsernameValid($_POST['username'])) {
		$_SESSION['userReturn'] .= 'Username cannot contain special characters <br>';
	}
	if (!isNameValid($_POST['firstname'])) {
		$_SESSION['userReturn'] .= 'Please provide a valid first name or leave blank <br>';
	}
	if (!isNameValid($_POST['lastname'])) {
		$_SESSION['userReturn'] .= 'Please provide a valid last name or leave blank <br>';
	}
	if ($_POST['password'] != $_POST['passwordconfirm']) {
		$_SESSION['userReturn'] .= 'Passwords do not march <br>';
	}
	if (!empty($_SESSION['userReturn'])) {
		$_SESSION['format'] = "red";
		header("location: edit_user.php?user=$oldUsername");
		die;
	}
	//Set variables
	$username = $_POST['username'];
	$password = $_POST['password'];
	$firstName = $_POST['firstname'];
	$lastName = $_POST['lastname'];
	$adminLevel = $_POST['adminlevel'];
	$schoolId = $_POST['school'];
	if ($schoolId == "0") {
		$schoolId = NULL;
	}
	//Check that user doesn't already exist
	if ($username != $oldUsername) { //if username has been changed check that new username is not taken
		$sql = "SELECT * FROM `users` WHERE `user`=?";
		$params = array($username);
		$result = mysqli_prepared_query($conn, $sql, 's', $params);
		$count = count($result);
		if ($count != 0) {
			$_SESSION['userReturn'] = "Username already in use";
			$_SESSION['format'] = "red";
			header("location: edit_user.php?user=$oldUsername");
			die;
		}
	}
	/****************************************************SQL QUERY**************************************************/
	$sql = "UPDATE `users` SET `user`=?, `password`=?, `first_name`=?, `last_name`=?, `administrator`=?, `school_id`=?
			WHERE `user`=?";
	$params = array($username, $password, $firstName, $lastName, $adminLevel, $schoolId, $oldUsername);
	$result = mysqli_prepared_query($conn, $sql, "ssssiis", $params);
	if ($result) {
		$_SESSION['format'] = 'green';
		$_SESSION['userReturn'] = 'User update successfull';
		header("location: edit_user.php?user=$username");
		die;
	}
}
