<?php
include('session.php');
require_once('menu.php');

/*
 * We need to get the school from the SESSION variable 'school'
 * If this variable is 0 then no school is selected and we should 
 * notifiy the user and provide a school selecter
 */

//We'll need the date and time
$now = new \DateTime('now');
$date = $now->format('Y-m-d');
$time = $now->format('H:i'); // example 17:35
//If school is not set, if that's not set set to 0
$selectedSchool = isset($_SESSION['school']) ? $_SESSION['school'] : 0;
//If we set 0 above we need to see if POST school is set and reset selectedSchool if it is
$selectedSchool = isset($_POST['school']) ? $_POST['school'] : $selectedSchool; //if not leave as is
//If school is not selected, get the schools list from DB
// May as well store a bool since we'll use it more than once
$isSchoolSelected = ($selectedSchool != 0);
//Connect DB
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
   print_r($DBerror);
   die;
}
if (!$isSchoolSelected) { //School is not selected
	/****************SQL QUERY*********************/
	$sql = "SELECT * FROM school";
	$schools = mysqli_prepared_query($conn, $sql);
	if (!$schools) {
		print_r("ERROR: FAILED TO GET SCHOOLS " . mysqli_error($conn));
  		die;
	}
} else {
/* Now if the school is set we can get the staff list for that school
 * In order to get a full staff list as well as dates and times IF they 
 * exist (ie. if attendance has already been taken for this day) we need 
 * to do two SELECTs, once from staff and one from staff_attendance.
 * We'll then combine these values in the table. This causes some overhead, especially since we can't 
 * restrict selection to a school on the staff_attendance but this seems to be the only way to 
 * achieve this result. We then need to organize the attendance data into an array with id numbers as keys
 */
	$sql = "SELECT `id_number` AS id, `first_name` AS first, `last_name` AS last FROM `staff` WHERE `school_id` = ?";
	$params = array($selectedSchool);
	$staff = mysqli_prepared_query($conn, $sql, "i", $params);
	if (!$staff) {
		print_r("FAILED TO GET STAFF " . mysqli_error($conn));
		die;
	}
	$sql = "SELECT `staff_id_number` as id, `date`, `time_in`, `time_out` FROM `staff_attendance` WHERE `date` = ?";
	$params = array($date);
	$result = mysqli_prepared_query($conn, $sql, 's', $params);
	//This result can be empty
	$staffDates = array();
	foreach ($result as $staffDate) {
		$staffDates[$staffDate['id']]['date'] = $staffDate['date'];
		$staffDates[$staffDate['id']]['time_in'] = $staffDate['time_in'];
		$staffDates[$staffDate['id']]['time_out'] = $staffDate['time_out'];
	}
}
?>
<!DOCTYPE html>
<html>
<head></head>
<body>
	<div id="main">
		<?php
            if ($access_level < 2) {
                ?>
                    <h2>Student Reports</h2>
                    <h3>Only managers and administrators can register students</h3>
                <?php
            } else {
	            $out = $_SESSION['staffSignInReturn']; //return messages from the register_student.php script
	            $format = $_SESSION['format']; //Formatting for the return messages
	            echo "<span id=$format>$out</span>";
	            $_SESSION['staffSignInReturn'] = "";
	            ?>
            	<h2>Staff Sign-in sheet: <?php echo $date; ?></h2>
            	<?php if (!$isSchoolSelected) { ?>
            	<div id="adminform">
            		<h3>Please select a school</h3>
            		<form id="school_selecter_form" action="staff_signin.php" method="post">
            			<p><label>School: </label>
            			<select name="school" class="forminput">
            				<?php foreach ($schools as $school) { ?>
            					<option value=<?php echo $school['school_id']; ?>><?php echo $school['school_name']; ?></option>	
            				<?php } ?>
            			</select></p>
            			<input type="submit" value="Update"></input>
            		</form>
            	</div>
            	<?php } else { ?>
            	<div id="adminform">
            		<h3>Today's staff attendance</h3>
            		<form id="staff_signin_form" action="register_staff_signin.php" method="post">
            			<table class="findtable">
            				<thead>
            					<th>Staff ID Number</th>
            					<th>First Name</th>
            					<th>Last Name</th>
            					<th>Date (DD/MM/YYYY)</th>
            					<th>Time In (HH:MM)</th>
            					<th>Time Out (HH:MM)</th>
            					<th>Present</th>
            				</thead>
            				<?php foreach ($staff as $staffMember) { ?>
            				<tbody>
            					<td><?php echo $staffMember['id']; ?></td>
            					<td><?php echo $staffMember['first']; ?></td>
            					<td><?php echo $staffMember['last']; ?></td>
            					<!-- For date, time_in and time_out we want to check the values from the DB
            					before we decide how we want to handle them. For example, if today's date
            					is already in the DB then we've already taken a signin today and we can output
            					the old values here. If there is no time_in then the user is most likely looking to fill it in and we can auto fill the current time in. If there is a time_in and no time_out then the user is most likely looking to fill in the time_out and we can auto fill this field with the current time. Also if date is filled in then mark present by default. Note: You can't sign out if you haven't signed in, If not signed in sign out will be blank.-->
            					<?php
            					$isDate = !is_null($staffDates[$staffMember['id']]['date']);
            					$isSignedIn = !is_null($staffDates[$staffMember['id']]['time_in']);
            					$isSignedOut = !is_null($staffDates[$staffMember['id']]['time_out']);
            					?>
            					<td><input name=<?php echo 'date' . $staffMember['id']; ?> type="date" value=<?php echo $isDate ? $staffDates[$staffMember['id']]['date'] : $date; ?>></td>
            					<td><input name=<?php echo  'time_in' . $staffMember['id']; ?> type="time" pattern='^[0-2]{1}[0-9]{1}:[0-5]{1}[0-9]{1}$' value=<?php echo $isSignedIn ? $staffDates[$staffMember['id']]['time_in'] : $time; ?>></td>
            					<td><input name=<?php echo 'time_out' . $staffMember['id']; ?> type="time" pattern='^[0-2]{1}[0-9]{1}:[0-5]{1}[0-9]{1}$' value=<?php echo ($isSignedIn && !$isSignedOut) ? $time : $staffDates[$staffMember['id']]['time_out']; ?>></td>
            					<td><input type="checkbox" name=<?php echo $staffMember['id']; ?> <?php echo $isDate ? 'checked' : ''; ?>></td>
            				</tbody>
            				<?php } ?>
            			</table>
            			<input type="submit" value="Submit" />
            		</form>
            	</div>

            <?php }
            } ?>
	</div>
</body>
</html>