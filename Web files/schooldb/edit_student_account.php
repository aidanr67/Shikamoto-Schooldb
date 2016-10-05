<?php
include('session.php');
/*
 * Get student birth cert number and type of transactions from GET, Get an amount from the user 
 * aswell as a description, process entry
 */
if (isset($_GET['id'])) {
	$birthCertNumber = $_GET['id'];
} else {
	return 0;
	die;
}
if (isset($_GET['type'])) {
	$type = $_GET['type'];
} else {
	return 0;
	die;
}

$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	return 0;
	die;
}
mysqli_autocommit($conn, FALSE);

if (isset($_POST['submit'])) { //Form has been submitted
	//Evaluate inputs
	if (empty($_POST['amount'])) {
		$error = "No amount provided";
		header ("refresh: 0");
		return 0;
		die;
	} else if (!isFloatValid($_POST['amount'])) {
		$error = 'Invalid amount provided';
		header ("refresh: 0");
		return 0;
		die;
	}
	$date = date('Y-m-d');
	$amount = $_POST['amount'];
	$receiptNumber = $_POST['receiptnumber'];
	$description = $_POST['description'];
/*************************************SQL QUERY**************************************/
	$sql = "SELECT balance FROM student_balance WHERE `birth_certificate_number` = ?";
	$params = array($birthCertNumber);
	$result = mysqli_prepared_query($conn, $sql, "s", $params);
	if (!$result) {
		print_r(mysqli_error($conn));
		return 0;
		die;
	}
	$row = $result[0];
	if ($type == 'credit') {
		$balance = $row['balance'] + $amount;
	} else {
		$balance = $row['balance'] - $amount;
	}

/*************************************SQL QUERY**************************************/
	$sql = "UPDATE student_balance SET `balance` = ? WHERE `birth_certificate_number` = ?";
	$params = array($balance, $birthCertNumber);
	if (!mysqli_prepared_query($conn, $sql, "ss", $params)) {
		print_r(mysqli_error($conn));
		return 0;
		die;
	}
/*************************************SQL QUERY**************************************/
	$sql = "INSERT INTO student_transactions (`birth_certificate_number`, `receipt_number`, `type`, `date`, `amount`, `balance`, `description`)
			VALUES (?, ?, ?, ?, ?, ?, ?)";
	$params = array($birthCertNumber, $receiptNumber, $type, $date, $amount, $balance, $description);
	$result = mysqli_prepared_query($conn, $sql, "ssssdds", $params);
	if (!$result) {
		print_r(mysqli_error($conn));
		return 0;
		die;
	}
	mysqli_commit($conn);
	mysqli_close($conn);
	header("location: student_account.php?id=$birthCertNumber");
}

require_once('menu.php');

?>

<!DOCTYPE html>
<html>
	<head></head>
	<body>
		<div id='main'>
			<?php if (isset($error)) {
				echo "<p id='red'>'$error'</p>";
			} ?>
			<h2>Add <?php echo $type;?> to account <?php echo $birthCertNumber?></h2>
			<div id='adminform'>
				<h3>Add amount and description</h3>
				<form id='creditform' action='edit_student_account.php?id=<?php echo $birthCertNumber;?>&amp;type=<?php echo $type?>' method='post'>
					<p><label>Please provide a reciept number: </label>
					<input type='text' name='receiptnumber' class='forminput' value="<?php echo $_POST['receiptnumber'];?>"></p>
					<p><label>Please provide an amount: </label>
					<input type='number' step="0.01" class='forminput' pattern="(^\d+(\.|\,)\d{2}$)" placeholder='0.00' name='amount' value="<?php echo $_POST['amount'];?>"></p>
					<p><label>Please provide a description for this <?php echo $type;?>: </label>
					<input type='text' class='forminput' placeholder='optional' name='description' value="<?php echo $_POST['description'];?>"></p>
					<p><input type='submit' name='submit' value="Submit"></p>
				</form>
			</div>
		</div>
	</body>
</html>