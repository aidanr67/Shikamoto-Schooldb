<?php
include('session.php');
require_once('menu.php');
/*
 * Get class number from user. Build a table with student first name, last
 * name and a checkbox for attendence.
 */
$error = ''; //String to store error messages

//Connect DB
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	die;
}
$schoolId = $_SESSION['school'];
/*******************************SQL QUERY***************************************/
$sql = "SELECT class_number FROM class";
if ($schoolId != "0") {
	$sql = $sql . " WHERE `school_id` = ?";
	$params = array($schoolId);
	$result = mysqli_prepared_query($conn, $sql, "i", $params);
} else {
	$result = mysqli_prepared_query($conn, $sql);
}
if (!$result) {
	$error = "No result for class numbers " . mysqli_error($conn);
	die;
}
$i = 0;
//First get a list of classes
foreach ($result as $row) {
	$classList[$i] = $row['class_number'];
	$i += 1;
}
$date = date('Y-m-d'); //todays date
?>
<!DOCTYPE html>
<html>
	<body>
		<div id='main'>
			<?php
			//Report errors
			$out = $_SESSION['registerReturn']; //return messages from the register_student.php script
			$format = $_SESSION['format']; //Formatting for the return messages
			echo "<span id=$format>$out</span>";
			$_SESSION['registerReturn'] = "";
			$classNumber = $_POST['selectclass'];
			?>
			<h2>Class register for <?php echo $date;?></h2>
				<div id='adminform'>
					<h3>Please select a class</h3>
					<form id='seclectclassform' action='today_register.php' method='post'>
						<select name='selectclass'>
							<?php $i = 0;
							while ($classList[$i]) {
								echo "<option value='$classList[$i]'>$classList[$i]</option>";
								$i += 1;
							}
							?>
						</select><br>
						<input type='submit' value='Fetch register' name='fetchsubmit'/>
					</form>
				</div>
				<?php 
				if (isset($_POST['fetchsubmit'])) {
					?>
					<div id='adminform' class="tablecontainer">
						<h2>Register for class: <?php echo $_POST['selectclass'];?></h2>
						<?php
	/*
	 * Get the students belonging to the class, and check if a register for this class has
	 * been taken for today
	 */
	/************************************SQL QUERY*********************************/
						//Check if a register has been taken for today
						$date = date('Y-m-d');
						$sql = "SELECT s.birth_certificate_number, s.first_name, s.last_name, a.date, a.attended
						FROM students s INNER JOIN student_attendance a  ON s.`birth_certificate_number` = a.`birth_certificate_number` 
						WHERE s.class = ? AND a.date = ?";
						$params = array($classNumber, $date);
						$result = mysqli_prepared_query($conn, $sql, "ss", $params);
						$count = count($result);
						$isRegisterTaken = ($count != 0); //A register for today has or hasn't been taken
						if (!$isRegisterTaken) { //A register has not been taken today
							//If register is not taken we need to do a regular query to the student table
							$sql = "SELECT birth_certificate_number, first_name, last_name
							FROM students
							WHERE class = ?";
							$params = array($classNumber);
							$result = mysqli_prepared_query($conn, $sql, "s", $params);
						}
						$_SESSION['registerTaken'] = $isRegisterTaken; //Store bool for register taken
						$_SESSION['currentclass'] = $classNumber; //Store the class number for the POST page
						?> 
						<form name='attendance' action='attendance.php' method='post'>
							<table class="findtable">
								<tr>
									<th>Birth certificate number</th>
									<th>Last name</th>
									<th>First name</th>
									<th>Present</th>
								</tr>
								<?php
								foreach ($result as $row) {
									$firstName = $row['first_name'];
									$lastName = $row['last_name'];
									$birthCertNumber = $row['birth_certificate_number'];
									if ($isRegisterTaken) {
										$attended = ($row['attended'] == 'Y');
									}
									echo "<tr>"
										. "<td>$birthCertNumber</td>"
										. "<td>$lastName</td>"
										. "<td>$firstName</td>"
										. "<td><input type=checkbox name=$birthCertNumber value='present' ";
										if ($isRegisterTaken) {
											if ($attended) {
												echo "checked";
											}
										}
										echo " /></td>"
									."</tr>";
								}
								?>
							</table>
							<input type='submit' name='submit' value="Submit"/>
						</form>
					</div>
				<?php
			}
			?>
		</div>
	</body>
</html>