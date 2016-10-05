<?php
include('session.php');
/* Create an entry in the user table */
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {  //DB connection failed
	print_r($DBerror);
	die;
}
//save input
$_SESSION['postinput'] = $_POST;
//If post submit is set, proceed
if (isset($_POST["createsubmit"])) {
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
		header('location: manage_users.php');
		die;
	} else {
		//Set variables
		$username = $_POST['username'];
		$password = $_POST['password'];
		$firstName = $_POST['firstname'];
		$lastName = $_POST['lastname'];
		$adminLevel = $_POST['adminlevel'];
	}
	//Check that user doesn't already exist
	$sql = "SELECT * FROM `users` WHERE `user`=?";
	$params = array($username);
	$result = mysqli_prepared_query($conn, $sql, 's', $params);
	$count = count($result);
	if ($count != 0) {
		$_SESSION['userReturn'] = "Username already in use";
		$_SESSION['format'] = "red";
		header("location: manage_users.php");
		die;
	}
	$sql = "INSERT INTO `users` (`user`, `password`, `first_name`, `last_name`, `administrator`) 
				VALUES (?, ?, ?, ?, ?)";
	$params = array($username, $password, $firstName, $lastName, $adminLevel);
	$result = mysqli_prepared_query($conn, $sql, 'ssssi', $params);
	if ($result) {
		$_SESSION['format'] = 'green';
		$_SESSION['userReturn'] = 'User creation successfull';
		header('location: manage_users.php');
		die;
	}
}
