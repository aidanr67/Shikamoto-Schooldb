<?php
include('session.php');
require_once('menu.php');
/* Get birth certificate number from find_student.php via get
 * use this to print out student information
 */
/**********Variables***********************************************************/
$error = ""; //hold error messages for return
$isSecondGuardian = FALSE; //true if student has secondary guardian
$isSecondAddressSame = FALSE; //true if primary and secondary guardian share address
if (isset($_GET['id'])) { //This should be set
	$birthCertNumber = $_GET['id']; //Student's birth certificate number
/* We need to store this in a session variable to get back to this page
 * after editting parents
 */
	$_SESSION['currentbirthcertnumber'] = $birthCertNumber;
} else { //if 'id' is not set there is a serious error
	print_r("ERROR: communication error, please call an administrator");
	die;
}
/* We'll fetch all the information and store it in varialbles, then output it 
 * in HTML. This it to create some seperation between the php and html code.
 */
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) { //connection to DB failed, print error and exit
	print_r("ERROR: CONNECTION TO DATABASE FAILED" . "<br>" . mysqli_error($conn));
	die;
}
/**********************************SQL QUERIES*********************************/
//Get school list
$sql = "SELECT * FROM school";
$schools = mysqli_prepared_query($conn, $sql);
if (!$schools) {
	$error = "No schools found. <br>";
}
/* This will no doubt require maintanance as the DB grows and more info is
 * required. For now we'll SELECT * and only save what is currently required
 */
//First get the date from the student table
$sql = "SELECT * FROM students WHERE birth_certificate_number = ?";
$params = array($birthCertNumber);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
if (!$result) {
	$error = "No results found for student. <br>";
}
//There should only be one row since we searched on the primary key
$row = $result[0];
//We have the birth certificate number so we can ignore the first value in the row
$schoolId = $row['school_id'];
$childsFirstName = $row['first_name']; //first name
$childsLastName = $row['last_name']; //Last name
$birthDate = $row['birth_date']; //Birth date
$gender = $row['gender']; //gender
$admissionDate = $row['date_of_admission']; //date of admission
//Now get the guardians from the parents table
$sql = "SELECT * FROM parents WHERE childs_birth_certificate = '$birthCertNumber'";
// No need to redefine params
$result = mysqli_prepared_query($conn, $sql, "s", $params);
if (!$result) {
	$error .= "No results found for guardians. <br>";
}
//There are either 1 or 2 results here. Primary and secondary guardian
$count = count($result);
if ($count == 2) { //Student has secondary guardian
	$isSecondGuardian = TRUE;
}
foreach ($result as $row) { //if there is only one row this will only run once
	if ($row['primary_guardian'] == 1) { // This is the primary guardian
		$isPrimaryGuardian = TRUE; //Test for primary guardian
	} else { //THis is the secondary guardian
		$isPrimaryGuardian = FALSE;
	}
	if ($isPrimaryGuardian) {
		$primaryGuardianIdNumber = $row['id_number']; //primary guardian ID numebr
		//We have the childs birth certificate so ignore row[1]
		$primaryGuardianFirstName = $row['first_name'];
		$primaryGuardianLastName = $row['last_name'];
		$primaryGuardianHomeNumber = $row['home_number'];
		$primaryGuardianWorkNumber = $row['work_number'];
		$primaryGuardianCellNumber = $row['cell_number'];
		$primaryGuardianAddress = $row['address'];
	} else {
		$secondaryGuardianIdNumber = $row['id_number'];
		$secondaryGuardianFirstName = $row['first_name'];
		$secondaryGuardianLastName = $row['last_name'];
		$secondaryGuardianHomeNumber = $row['home_number'];
		$secondaryGuardianWorkNumber = $row['work_number'];
		$secondaryGuardianCellNumber = $row['cell_number'];
		$secondaryGuardianAddress = $row['address'];
	}
}
if (!$isSecondGuardian) { //There is no secondary guardian, set values to ""
	$secondaryGuardianIdNumber = "";
	$secondaryGuardianFirstName = $secondaryGuardianLastName = "";
	$secondaryGuardianHomeNumber = $secondaryGuardianWorkNumber = "";
	$secondaryGuardianCellNumber = "";
	$secondaryGuardianAddress = "";
}
//Retrieve student attendance
/***********************************SQL QUERY**********************************/
$sql = "SELECT COUNT(birth_certificate_number) FROM student_attendance 
		WHERE `birth_certificate_number` = ? AND `attended` = ?";
//No need to redefine params
$params = array($birthCertNumber, 'Y');
$result = mysqli_prepared_query($conn, $sql, "ss", $params);
if (!$result) {
	$error = "No result for days attended <br> " . mysqli_error($conn); 
} else {
	$row = $result[0];
	$daysAttended = $row['COUNT(birth_certificate_number)'];
}
/***********************************SQL QUERY**********************************/
$sql = "SELECT COUNT(birth_certificate_number) FROM student_attendance 
		WHERE `birth_certificate_number` = ? AND `attended` = ?";
$params = array($birthCertNumber, 'N');
//No need to redefine params
$result = mysqli_prepared_query($conn, $sql, "ss", $params);
if (!$result) {
	$error = "No result for days absent <br> " . mysqli_error($conn); 
} else {
	$row = $result[0];
	$daysAbsent = $row['COUNT(birth_certificate_number)'];
}
mysqli_close($conn); //Close DB
?>
<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript">
			function removeStudent() {
			    setTimeout('window.location.href="remove_student.php?id=<?php echo $birthCertNumber;?>"', 0);
			}
			function confirmBox() {
				var choice = false;
				if (confirm("Are you sure you want to remove the student with birth certificate number: <?php echo $birthCertNumber;?> from the student registry?") === true) {
					choice = true;
				}
				if (choice) {
					removeStudent();
				}
			}
			function edit(button, section) {
				var fields = document.getElementsByClassName(section);
				var i;
				if (button.value == 'Edit') {
					for (i = 0; i < fields.length; i++) {
						fields[i].disabled = false;
					}
					document.getElementById(section + 'submit').disabled = false;
					document.getElementById(section + 'submit').hidden = false;
					button.value = "Cancel";
				} else {
					for (i = 0; i < fields.length; i++) {
						fields[i].disabled = true;
					}
					document.getElementById(section + 'submit').disabled = true;
					document.getElementById(section + 'submit').hidden = true;
					button.value = "Edit";
				}
			}
		</script>
	</head>
	<body>
		<div id="main">
			<?php
			if ($access_level < 2) {
				?>
				<h3>Node: only managers and administrator can edit these details</h3>
				<?php
			}
			$out = $_SESSION['updateSecondaryReturn']; //return messages from the register_student.php script
			$format = $_SESSION['format']; //Formatting for the return messages
			echo "<span id=$format>$out</span>";
			$_SESSION['updateSecondaryReturn'] = $out = "";
			$out = $_SESSION['updatePrimaryReturn']; //return messages from the register_student.php script
			$format = $_SESSION['format']; //Formatting for the return messages
			echo "<span id=$format>$out</span>";
			$_SESSION['updatePrimaryReturn'] = $out = "";
			$out = $_SESSION['updateStudentReturn']; //return messages from the register_student.php script
			$format = $_SESSION['format']; //Formatting for the return messages
			echo "<span id=$format>$out</span>";
			$_SESSION['updateStudentReturn'] = $out = "";
			if ($access_level > 1) {
			?>
				<input id='removebutton' type="button" class='removebutton' onclick="confirmBox()" value='Remove this student'>
				<?php
			}
			?>
			<p id='red'><?php echo $error;?></p>
			<!--Student section -->
			<div id="adminform">
				<form id='editstudent' method="post" action='update_student_details.php?id=<?php echo $birthCertNumber;?>'>
					<?php
					if ($access_level > 1) {
						?>
						<input id="editbutton" type="button" onclick="edit(this, 'student')" value="Edit">
						<?php
					}
					?>
					<h2>Student Information</h2>
					<p><label>School: </label>
						<select id="schoolselect" name="school" class="student" disabled>
							<option value='0'>None</option>
							<?php foreach ($schools as $school) { ?>
								<option value=<?php echo $school['school_id']; 
									if ($schoolId == $school['school_id']) { echo ' selected'; }?>><?php echo $school['school_name'];?></option>
								<?php
							} ?>
						</select></p>
					<p><label>Birth certificate number: </label>
						<input type="text" disabled class="noteditable" name='birthcertnumber' 
					       	value="<?php echo $birthCertNumber?>"/></p>
					<p><label>First name: </label>
						<input type='text' class="student" disabled pattern='<?php echo $validName; ?>' name='firstname' 
					        	value="<?php echo $childsFirstName;?>"/></label>
					<p><label>Last name: </label>
						<input type='text' class="student" disabled pattern='<?php echo $validName; ?>' name='lastname' 
					        value="<?php echo $childsLastName;?>"/></p>
					<p><label>Birth date: </label>
						<input type='text' class="student" disabled pattern='<?php echo $validDate; ?>' name='dateofbirth' 
			           		value="<?php echo $birthDate; ?>"/></p>
					<p><label>Gender: </label>
						<select class="student" disabled name='gender'>
							<option value="Male" <?php  if ($gender == 'Male') { echo 'selected'; }?>>Male</option>
							<option value="Female" <?php if ($gender == 'Female') { echo 'selected'; }?>>Female</option>
						</select></p>
					<p><label>Date of admission: </label>
						<input type='date' disabled class="student" pattern='<?php echo $validDate; ?>' name='admissiondate' 
           					value="<?php echo $admissionDate;?>"/></p>
					<?php 
					if ($access_level > 1) {
						?>
						<input type="submit" name="submit" id="studentsubmit" disabled hidden value="Save"/>
						<?php
					}
					?>
				</form>
			</div>
			<!--Primary section -->
			<div id="adminform">
				<form id='editprimary' method="post" action='update_parent_details.php?idnumber=<?php echo $primaryGuardianIdNumber;?>'>
					<?php 
					if ($access_level > 1) {
						?>
						<input id='editbutton' type="button" onclick="edit(this, 'primary')" value="Edit">
						<?php
					}
					?>
					<h2>Primary guardian's information</h2>
					<p><label>ID number: </label>
						<input type='text' disabled class="noteditable" name='idnumber' 
		           			value="<?php echo $primaryGuardianIdNumber;?>"/></p>
					<p><label>First name: </label>
						<input type='text' disabled class="primary" pattern='<?php echo $validName; ?>' name='firstname'
			           		value='<?php echo $primaryGuardianFirstName;?>'/></p>
					<p><label>Last name: </label>
						<input type='text' disabled class="primary" pattern='<?php echo $validName; ?>' name='lastname'
						        value="<?php echo $primaryGuardianLastName;?>"/></p>
					<p><label>Home number: </label>
						<input type='text' disabled class="primary" pattern='<?php echo $validPhone; ?>' name='homenumber'
           						value='<?php echo $primaryGuardianHomeNumber;?>'/></p>
					<p><label>Work number</label>
						<input type='text' disabled class="primary" pattern='<?php echo $validPhone; ?>'name='worknumber'
            					value="<?php echo $primaryGuardianWorkNumber;?>"/></p>
					<p><label>Cell number:</label>
						<input type="text" disabled class="primary" pattern="<?php echo $validPhone;?>" name='cellnumber'
			        			value="<?php echo $primaryGuardianCellNumber;?>"/></p>
						<p><label>Address: </label>
							<input type="text" class="primary" disabled name="address"
								value="<?php echo $primaryGuardianAddress;?>"/></p>
						<?php
						if ($access_level > 1) {
							?>
							<input type="submit" name="submit" id="primarysubmit" disabled hidden value="Save"/>
							<?php
						}
						?>
				</form>
			</div>
			<!--Secondary section -->
			<div id='adminform'>
				<form id='editsecondary' method="post" action='update_parent_details.php?idnumber=<?php echo $secondaryGuardianIdNumber;?>'>
					<?php
					if ($access_level > 1) {
						?>
						<input type=button id='editbutton' onclick="edit(this, 'secondary')" value="Edit"/>
						<?php 
					}
					?>
					<h2>Secondary guardian's information</h2>
					<p><label>ID number: </label>
						<input type="text" disabled class="<?php if ($secondaryGuardianIdNumber == "")
							echo 'secondary'; else echo 'noteditable';?>" pattern='<?php echo $validId; ?>' name='idnumber'
				        	value="<?php echo $secondaryGuardianIdNumber;?>"/></p>
					<p><label>First name: </label>
						<input type="text" disabled class="secondary" pattern='<?php echo $validName; ?>' name='firstname'
			    	   		value="<?php echo $secondaryGuardianFirstName;?>"/></p>					
					<p><label>Last name: </label>
						<input type="text" disabled class="secondary" pattern='<?php echo $validName; ?>' name='lastname'
			    	    	value="<?php echo $secondaryGuardianLastName;?>"/></p>
					<p><label>Home number: </label>
						<input type="text" disabled class="secondary" pattern='<?php echo $validPhone; ?>' name='homenumber'
			           		value='<?php echo $secondaryGuardianHomeNumber;?>'/></p>
					<p><label>Work number: </label>
						<input type="text" disabled class="secondary" pattern='<?php echo $validPhone; ?>' name='worknumber'
			           		value="<?php echo $secondaryGuardianWorkNumber;?>"/></p>
					<p><label>Cell number: </label>
						<input type="text" disabled class="secondary" pattern='<?php echo $validPhone; ?>' name='cellnumber'
		           			value="<?php echo $secondaryGuardianCellNumber;?>"/></p>
					<p><label>Address: </label>
						<input type="text" class="secondary" disabled name="address"
							value="<?php echo $secondaryGuardianAddress;?>"/></p>
					<?php
					if ($access_level > 1) {
						?>
						<input type="submit" name="submit" id="secondarysubmit" disabled hidden value="Save"/>
						<?php
					}
					?>
				</form>
			</div>
			<div id="adminform">
				<h2>Student attendance</h2>
				<table class="findtable">
					<tr>
						<th>Present</th>
						<th>Absent</th>
					</tr>
					<tr>
						<td><?php echo $daysAttended;?> days</td>
						<td><?php echo $daysAbsent;?> days</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>