<?php
include('session.php');
/*
 * List all students by birth cert number, first name and last name
 * as well as current balances, last payment dates and amounts
 * Clicking on a students birth cert number should link to student_account.php
 */
$error = '';
//Connect DB
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);

if (!$conn) {
	print_r($DBerror);
	return 0;
	die;
}

$resultBirthCerts = array();
$resultArray = array(); //Hold the sql results
$schoolId = $_SESSION['school'];
/***************************************SQL QUERY**********************************************/
$sql = "SELECT s.`birth_certificate_number`, s.`first_name`, s.`last_name`, b.`balance`
		FROM `students` s LEFT JOIN `student_balance` b
		ON s.`birth_certificate_number` = b.`birth_certificate_number`";

setlocale(LC_MONETORY, 'en_ZA'); //for currency
require_once('menu.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<script type='text/javascript'>
			function showHide() {
				var show = "";
				var hide1 = "";
				var hide2 = "";
				var ddl = document.getElementById('searchby');
				var selected = ddl.options[ddl.selectedIndex].value;
				if (selected == "birth_certificate_number") {
					show = 'hidebirthcertnumber';
					hide1 = 'hidelastname';
					hide2 = 'hidefirstname';
				} else if (selected == "last_name") {
					show = 'hidelastname';
					hide1 = 'hidebirthcertnumber';
					hide2 = 'hidefirstname';
				} else {
					show = 'hidefirstname';
					hide1 = 'hidebirthcertnumber';
					hide2 = 'hidelastname';
				}
				document.getElementById(hide1).style.display = "none";
				document.getElementById(hide2).style.display = "none";
				document.getElementById(show).style.display = "block";
			};
		</script>
	</head>
	<body>
		<div id='main'>
			<h2>Find a student's account</h2>
			<?php
            if ($access_level < 3) {
                ?>
                    <h3>Only administrators can view student accounts</h3>
                <?php
            } else {
            	?>
				<div id='adminform'>
					<form id="findstudentaccountform" action='student_accounts_list.php' method='post'>
						<h3>Please select a search option</h3>
						<select id="searchby" name="searchby" onchange="showHide()">
			    			<option value='birth_certificate_number'>Birth certificate number</option>
			    			<option value="last_name">Last name</option>
			    			<option value="first_name">First name</option>
						</select>
						<p id='hidebirthcertnumber'><label>Please enter whole or partial birth certificate number</label>
			    			<input name='birth_certificate_number' type='text' class="otherinput" pattern='^\d*$' placeholder='Whole or part of 13 digit number' value=""></p>
						<p id='hidelastname' style="display: none"><label>Please enter a whole or partial last name<br></label>
			    			<input name='last_name' type='text' class="otherinput" pattern='^[a-zA-Z\s]*$' placeholder="last name" value=""></p>
						<p id='hidefirstname' style="display: none"><label>Please enter a whole or partial first name<br></label>
			    			<input name='first_name' type='text' class="otherinput" pattern='^[a-zA-Z\s]*$' placeholder="first name" value=""></p>
						<input type="submit" name="submit" value="Search">
					</form>
				</div>
				<?php
				if (isset($_POST['submit'])) { //Check if the submit button has been clicked
					$searchBy = $_POST['searchby']; //Which field to search in
					$searchString = ""; //To hold the search string entered by the user
					if ($searchBy == "birth_certificate_number") { //Seach birth cert numbers
						$searchString = $_POST['birth_certificate_number'];
					}
					if ($searchBy == "last_name") { //Search last names
						$searchString = $_POST['last_name'];
					}
					if ($searchBy == "first_name") { //Search first names
						$searchString = $_POST['first_name'];
					}
					if (!empty($searchString)) { //a valid input was entered
						//Get data from DB
						/*******************************************SQL QUERY***************************************************/
						$searchby = "s.`" . $searchBy . "`";
						if ($schoolId != "0") {
							$sqlSearch = $sql . " WHERE s.`school_id` = ? AND " . $searchby . " LIKE CONCAT('%', ?, '%')";
							$params = array($schoolId, $searchString);
							$result = mysqli_prepared_query($conn, $sqlSearch, "is", $params);
						} else {
							$sqlSearch = $sql . " WHERE " . $searchby . " LIKE CONCAT('%', ?, '%')";
							$params = array($searchString);
							$result = mysqli_prepared_query($conn, $sqlSearch, "s", $params);
						}
						if (!$result) {
							?>
							<div id="adminform">
				                <h3>No results found</h3>
				            </div>
				            <?php
						} else {
							//Store values in arrays
							storeValues($result, $resultArray, $resultBirthCerts, $conn);
							?> 
							<div id="adminform" class="tablecontainer">
								<h3>Results found</h3>
								<table class="findtable">
									<tr>
										<th>Birth certificate number</th>
										<th>First name</th>
										<th>Last name</th>
										<th>Current balance</th>
										<th>Most recent payment date</th>
										<th>Most recent payment amount</th>
										<th class='editcolumn'>Account</th>
									</tr>
									<?php
									while ($birthCertNumber = array_pop($resultBirthCerts)) { 
										$firstName = $resultArray[$birthCertNumber . "firstname"];
										$lastName = $resultArray[$birthCertNumber . "lastname"];
										$balance = $resultArray[$birthCertNumber . "balance"];
										$paymentAmount = $resultArray[$birthCertNumber . "amount"];
										$paymentDate = $resultArray[$birthCertNumber . "date"];?>
										<tr>
											<td><?php echo $birthCertNumber;?></a></td>
											<td><?php echo $firstName;?></td>
											<td><?php echo $lastName;?></td>
											<td><?php echo 'R' . money_format('%i', $balance);?></td>
											<td><?php echo $paymentDate;?></td>
											<td><?php echo 'R' . money_format('%i', $paymentAmount);?></td>
											<td><a href="student_account.php?id=<?php echo $birthCertNumber;?>"><button id="smalleditbutton">Account</button></a></td>
										</tr>
										<?php
									}?>
								</table>
							</div>
							<?php
						}
						//Unset and reset arrays
						unset($resultArray);
						unset($resultBirthCerts);
						$resultArray = array();
						$resultBirthCerts = array();
					}
				}?>
				<?php
				//Get data from DB
				if ($schoolId != "0") {
					$sql = $sql . " WHERE `school_id` = ?";
					$params = array($schoolId);
					$result = mysqli_prepared_query($conn, $sql, "i", $params);
				} else {
					$result = mysqli_prepared_query($conn, $sql);
				}
				if (!$result) {
					print_r(mysqli_error($conn));
					return 0;
					die;
				}
				storeValues($result, $resultArray, $resultBirthCerts, $conn);
				mysqli_close($conn);?>
				<div id='adminform' class="tablecontainer">
					<h3>Full student list</h3>
					<table class="findtable">
						<tr>
							<th>Birth certificate number</th>
							<th>First name</th>
							<th>Last name</th>
							<th>Current balance</th>
							<th>Most recent payment date</th>
							<th>Most recent payment amount</th>
							<th class='editcolumn'>Account</th>
						</tr>
						<?php while ($birthCertNumber = array_pop($resultBirthCerts)) { 
							$firstName = $resultArray[$birthCertNumber . "firstname"];
							$lastName = $resultArray[$birthCertNumber . "lastname"];
							$balance = $resultArray[$birthCertNumber . "balance"];
							$paymentAmount = $resultArray[$birthCertNumber . "amount"];
							$paymentDate = $resultArray[$birthCertNumber . "date"];?>
							<tr>
								<td><?php echo $birthCertNumber;?></a></td>
								<td><?php echo $firstName;?></td>
								<td><?php echo $lastName;?></td>
								<td><?php echo 'R' . money_format('%i', $balance);?></td>
								<td><?php echo $paymentDate;?></td>
								<td><?php echo 'R' . money_format('%i', $paymentAmount);?></td>
								<td><a href="student_account.php?id=<?php echo $birthCertNumber;?>"><button id="smalleditbutton">Account</button></a></td>
							</tr>
							<?php
						}?>
					</table>
				</div>
				<?php
			}
			?>
		</div>
	</body>
</html>

<?php
function storeValues($sqlResult, &$results, &$birthCerts, &$dbConn) { //Function to build arrays of values
	foreach ($sqlResult as $row) { //Fill an array with all the details
		$birthCertNumber = $row['birth_certificate_number'];
		array_push($birthCerts, $birthCertNumber); //Store birth cert number in seperate array
		$results[$birthCertNumber . "firstname"] = $row['first_name'];
		$results[$birthCertNumber . "lastname"] = $row['last_name'];
		$results[$birthCertNumber . "balance"] = $row['balance'];
		//Get credit info for each student
		$sqlCredits = "SELECT `birth_certificate_number`,`date`, `amount`
						FROM (SELECT * FROM `student_transactions` WHERE `type` = 'credit'
								AND `birth_certificate_number` = '$birthCertNumber') credit
						WHERE `timestamp` = (SELECT `timestamp` FROM
									(SELECT * FROM `student_transactions` WHERE `type` = 'credit'
									AND `birth_certificate_number` = '$birthCertNumber') credit
						ORDER BY `timestamp` DESC LIMIT 1)";
		$resultCredits = mysqli_query($dbConn, $sqlCredits);
		$rowCredits = mysqli_fetch_array($resultCredits);
		$birthCertNumberCredits = $rowCredits['birth_certificate_number'];
		$results[$birthCertNumberCredits . "amount"] = $rowCredits['amount'];
		$results[$birthCertNumberCredits . "date"] = $rowCredits['date'];
	}
}