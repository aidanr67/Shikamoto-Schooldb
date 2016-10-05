<?php 
include('session.php');
require_once('menu.php');

$error = "";
$showReport = false; //Only show a report if the time period has been specified
$now = date('Y-m-d'); //Current system date
/*
 * Get staff ID number from GET
 */
if (isset($_GET['id'])) { //This should be set
    $id = $_GET['id']; //Student's birth certificate number
} else { //if 'id' is not set there is a serious error
    print_r("ERROR: communication error, please call an administrator");
    die;
}

/*
 * We need to get staff hours worked per month with a month selector. Hours worked are the sum of the difference
 * between timeout and timein for all days worked in the given month. We must check if timein and timeout are not
 * null before checking the difference (For example if we'll looking at the current month it may be true that the * staff member has not yet checked out for today).
 */

//Connect DB
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
 if (!$conn) {
    print_r($DBerror);
    die;
}
if (isset($_POST['submit'])) {
    //Get the start and end dates entered by the user
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $showReport = true;
	/* Get all the relivant data from the database for the selected staff member
	 * At this point all we have to report is attendance. We will calculate hours within the query
	 */

	$sql = "SELECT SUM(HOUR(`time_out`) - HOUR(`time_in`)) AS `hours` FROM `staff_attendance` WHERE `staff_id_number` = ? AND `date` BETWEEN ? AND  ?";
	$params = array($id, $startDate, $endDate);
	$hours = mysqli_prepared_query($conn, $sql, 'sss', $params);
	$hours = isset($hours[0]['hours']) ? $hours[0]['hours'] : 0; //Set hours to zero if NULL
}
?>

<!DOCTYPE html>
<html>
	<body>
	<div id='main'>
		<h2>Staff Attendance</h2>		
		<?php
            if ($access_level < 2) {
                ?>
                    <h3>Only managers and administrators can register students</h3>
                <?php
             } else if ($error != "") { ?>
             	<h3> <?php echo $error; ?></h3>
             	<?php
             } else { ?>
            <div id='adminform'>
            	<h3>Select specific period</h3>
                    <form id="selectperiodform" action="staff_attendance.php?id=<?php echo $id; ?>" method="post">
                        <p><label>Start Date: </label><input type="date" name="start_date" value=<?php if ($showReport) { echo $startDate; } else { echo $now; } ?>></input></p>
                        <p><label>End Date: </label><input type="date" name="end_date" value=<?php if ($showReport) { echo $endDate; } else { echo $now; } ?>></input></p>
                        <input type="submit" name="submit" value="Update"></input>
                    </form>
            </div>
            <?php if($showReport) { ?>
            <div id='adminform'>
            	<h3>Hours worked</h3>
            	<p>Hours worked for the period: <?php echo $hours; ?></p>
            </div>
            <?php }
            } ?>
	</div>
	</body>
</html>