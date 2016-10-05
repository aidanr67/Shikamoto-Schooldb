<?php
include('session.php');
require_once('menu.php');

/* Get ID number from find_student.php via get
 * use this to print out staff information
 */
/**********Variables***********************************************************/
$error = ""; //hold error messages for return

if (isset($_GET['id'])) { //This should be set
    $idNumber = $_GET['id']; //Staff member's ID number
/* We need to store this in a session variable to get back to this page
 * after editting parents
 */
	$_SESSION['currentidnumber'] = $idNumber;
} else { //if 'id' is not set there is a serious error
    print_r("ERROR: communication error, please call an administrator");
    die;
}
/* We'll fetch all the information and store it in varialbles, then output it 
 * in HTML. This it to create some seperation between the php and html code.
 */
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) { //connection to DB failed, print error and exit
    print_r($DBerror);
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
//First get the date from the staff table
$sql = "SELECT * FROM staff WHERE id_number = ?";
$params = array($idNumber);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
if (!$result) {
    $error = "No results found for staff member. <br>";
}
//There should only be one row since we searched on the primary key
$row = $result[0];
//We have the id number so we can ignore the first value in the row
$schoolId = $row['school_id'];
$firstName = $row['first_name'];
$lastName = $row['last_name'];
$gender = $row['gender'];
$employmentDate = $row['date_of_employment'];
$homeNumber = $row['home_number'];
$cellNumber = $row['cell_number'];
$address = $row['address'];
$isCurrent = ($row['current'] == 1); //Is the employee still employed
//Next of Kin
$nokFirstName = $row['nok_first_name'];
$nokLastName = $row['nok_last_name'];
$nokHomeNumber = $row['nok_home_number'];
$nokWorkNumber = $row['nok_work_number'];
$nokCellNumber = $row['nok_cell_number'];
$nokAddress = $row['nok_address'];
if ($isCurrent) {
	$currentString = "This Employee is currently employed, to change this click 'change status'";
} else {
	$currentString = "This Employee is currently not employed, to change this click 'change status'";
}
mysqli_close($conn); //Close DB
?>
<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript">
			function changeStaffStatus() {
    			setTimeout('window.location.href="remove_staff.php?id=<?php echo $idNumber;?>"', 0);
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
			$out = $_SESSION['updateNokReturn'];
			$format = $_SESSION['format']; //Formatting for the return messages
			echo "<span id=$format>$out</span>";
			$_SESSION['updateNokReturn'] = $out = "";
			$out = $_SESSION['updateStaffReturn'];
			$format = $_SESSION['format']; //Formatting for the return messages
			echo "<span id=$format>$out</span>";
			$_SESSION['updateStaffReturn'] = $out = "";
			if ($access_level > 1) {
				?>
				<button id='removebutton' class='removebutton' onclick="changeStaffStatus()">Change Status</button>
				<?php 
			} 
			?>
			<p id='red'><?php echo $error;?></p>
			<!--Staff section -->
			<div id="adminform">
				<form id='editstaff' method="post" action='update_staff_details.php?section=staff'>
					<?php
					if ($access_level > 1) {
						?>
						<input id="editbutton" class="editbutton" type="button" onclick="edit(this, 'staff')" value="Edit">
						<?php
					}
					?>
					<h2>Staff Information</h2>
					<p><label>School: </label>
						<select id="schoolselect" name="school" class="staff" disabled>
							<option value='0'>All</option>
							<?php foreach ($schools as $school) { ?>
								<option value=<?php echo $school['school_id']; 
									if ($schoolId == $school['school_id']) { echo ' selected'; }?>><?php echo $school['school_name'];?></option>
								<?php
							} ?>
						</select></p>
					<p><label>ID number: </label>
						<input type="text" disabled class="noteditable" name='idnumber' 
			    			value="<?php echo $idNumber?>"/></p>
					<p><label>First name: </label>
						<input type='text' class="staff" pattern='<?php echo $validName; ?>' disabled name='firstname' 
					       	value="<?php echo $firstName;?>"/></p>
					<p><label>Last name: </label>
						<input type='text' class="staff" pattern='<?php echo $validName; ?>' disabled name='lastname' 
		 	        		value="<?php echo $lastName;?>"/></p>
					<p><label>Gender: </label>
						<select class="staff" disabled name='gender'>
							<option value="Male" <?php  if ($gender == 'Male') { echo 'selected'; }?>>Male</option>
							<option value="Female" <?php if ($gender == 'Female') { echo 'selected'; }?>>Female</option>
						</select></p>
					<p><label>Date of employment: </label>
						<input type='text' class="staff" pattern='<?php echo $validDate; ?>' disabled name='dateofemployment' 
				        	value="<?php echo $employmentDate; ?>"/></p>
					<p><label>Home number: </label>
						<input type="text" class="staff" disabled name="homenumber" pattern='<?php echo $validPhone; ?>' 
							value="<?php echo $homeNumber;?>"/></p>
					<p><label>Cellphone number: </label>
						<input type="text" class="staff" disabled name="cellnumber" pattern='<?php echo $validPhone; ?>'
							value="<?php echo $cellNumber;?>"/></p>
					<p><label>Address: </label>
						<input type="text" class="staff" disabled name="address"
							value="<?php echo $address;?>"/></p> 
							<?php if ($access_level > 2) { ?>
								<p><?php echo $currentString;?></p>
							<?php } ?>
					<?php 
					if ($access_level > 1 ) {
						?>
						<input type="submit" name="submit" id="staffsubmit" class='submit' disabled hidden value="Save"/>
						<?php
					}
					?>
				</form>
			</div>
		<!--nok section -->
			<div id="adminform">
				<form id='editnok' method="post" action='update_staff_details.php?section=nok'>
					<?php
					if ($access_level > 1) {
						?>
						<input id='editbutton' class="editbutton" type="button" onclick="edit(this, 'nok')" value="Edit">
						<?php
					}
					?>
					<h2>Next of kin's information</h2>
					<p><label>First name: </label>
						<input type='text' disabled class="nok" pattern='<?php echo $validName; ?>' name='nokfirstname'
					       	value='<?php echo $nokFirstName;?>'/></p>
					<p><label>Last name: </label>
						<input type='text' disabled class="nok" pattern='<?php echo $validName; ?>' name='noklastname'
					       value="<?php echo $nokLastName;?>"/></p>
					<p><label>Home number: </label>
						<input type='text' disabled class="nok" pattern='<?php echo $validPhone; ?>' name='nokhomenumber'
					       	value='<?php echo $nokHomeNumber;?>'/></p>
					<p><label>Work number: </label>
						<input type='text' disabled class="nok" pattern='<?php echo $validPhone; ?>' name='nokworknumber'
				        	value="<?php echo $nokWorkNumber;?>"/><?php echo $nokWorkNumber;?></p>
					<p><label>Cell number: </label>
						<input type="text" disabled class="nok" pattern='<?php echo $validPhone; ?>' name='nokcellnumber'
				        	value="<?php echo $nokCellNumber;?>"/></p>
					<p><label>Address: </label>
						<input type="text" class="nok" disabled name="nokaddress"
								value="<?php echo $nokaddress;?>"/></p>
					<?php
					if ($access_level > 1) {
						?>
						<input type="submit" name="submit" class='submit' id="noksubmit" disabled hidden value="Save"/>
						<?php
					}
					?>
				</form>
			</div>
		</div>
	</body>
</html>