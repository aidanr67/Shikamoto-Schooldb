<?php
include('session.php');
/*
 * Get the students birth cert number from get. Find all the account data from 
 * various account tables and put it in a table. Create buttons for debits and 
 * credits.
 */
setlocale(LC_MONETORY, 'en_ZA'); //for currency

$birthCertNumber = $_GET['id'];
if (empty($birthCertNumber)) {
	print_r("No birth cert number recieved");
	return 0;
	die;
}

$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	return 0;
	die;
}
//Retrieve student name and surname 
$sql = "SELECT first_name, last_name, school_id FROM students WHERE `birth_certificate_number` = ?";
$params = array($birthCertNumber);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
if ($result) {
	$row = $result[0];
	$firstName = $row['first_name'];
	$lastName = $row['last_name'];
	$schoolId = $row['school_id'];
}
//Retrieve school name
if ($schoolId > 0) {
	$sql = "SELECT school_name FROM school WHERE `school_id` = ?";
	$params = array($schoolId);
	$result = mysqli_prepared_query($conn, $sql, "i", $params);
	$schoolName = $result[0]['school_name'];
} else {
	$schoolName = "None";
}
//Retrieve balance 
/************************************SQL QUERY**********************************/
$sql = "SELECT balance FROM student_balance WHERE `birth_certificate_number` = ?";
$params = array($birthCertNumber);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
$row = $result[0];
$balance = $row['balance'];

// Retrieve all debits and credits, ordered by date
/**********************SQL QUERY************************************************/
$sql = "SELECT `date`, receipt_number, type, amount, balance, description
		FROM student_transactions
		WHERE `birth_certificate_number` = ?
		ORDER BY `timestamp` DESC";
//No need to redifine $params since we're still only using $birthCertNumber
$result = mysqli_prepared_query($conn, $sql, "s", $params);
require_once('menu.php');
?>

<!DOCTYPE html>
<html>
	<body>
		<div id='main'>
			<h3>School name: <?php echo $schoolName; ?></h3>
			<div id='adminform'>
				<h3>Personal details</h3>
				<p>Student account: <?php echo $birthCertNumber;?></p>
				<p>First name: <?php echo $firstName; ?> </p>
				<p>Last name: <?php echo $lastName; ?> </p>
			</div>
			<div id='adminform' class='actions'>
				<h3>Account actions</h3>
				<input id="accountbutton" type="button" value="Credit" onclick="location.href='edit_student_account.php?id=<?php echo $birthCertNumber;?>&amp;type=credit'" />
				<input id="accountbutton" type="button" value='Debit' onclick="location.href='edit_student_account.php?id=<?php echo $birthCertNumber;?>&amp;type=debit'" />
			</div>
			<div id=adminform class="tablecontainer">
				<h3>Current balance: <?php echo 'R' . money_format('%i',$balance);?></h3>
				<table class="accounttable">
					<tr>
						<th>Date</th>
						<th>Receipt number</th>
						<th>Credit</th>
						<th>Debit</th>
						<th>Balance</th>
						<th>Description</th>
					</tr>
					<?php foreach ($result as $row) { ?>
					<tr>
						<td><?php echo $row['date'];?></td>
						<td><?php echo $row['receipt_number'];?></td>
						<td><?php if ($row['type'] == 'credit') {
							echo 'R' . money_format('%i',$row['amount']);
						}?></td>
						<td><?php if ($row['type'] == 'debit') {
							echo 'R' . money_format('%i',$row['amount']);
						}?></td>
						<td><?php echo 'R' . money_format('%i',$row['balance']);?></td>
						<td><?php echo $row['description'];?></td>
					</tr>
				<?php } ?>
				</table>
			</div>
		</div>
	</body>
</html>


