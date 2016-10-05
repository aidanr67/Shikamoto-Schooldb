<?php
include('session.php');
require_once('menu.php');

$now = date('Y-m-d'); //Current system date
$showReport = false; //Only show report when a time period has been selected
/*
 * Get student ID number from GET
 */
if (isset($_GET['id'])) { //This should be set
    $birthCertNumber = $_GET['id']; //Student's birth certificate number
} else { //if 'id' is not set there is a serious error
    print_r("ERROR: communication error, please call an administrator");
    die;
}
//Connect DB
 $conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
 if (!$conn) {
    print_r($DBerror);
    die;
 }
 //First get the time period if it has already been set
 if(isset($_POST['submit'])) {
    $showReport = true;
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    //Now we can use this time period to retrieve the data we need from the DB
    /*********************************SQL***********************************/
    $sql = "SELECT * FROM `student_notes` WHERE time_stamp BETWEEN ? AND ?";
    $params = array($startDate, $endDate);
    $notes = mysqli_prepared_query($conn, $sql, "ss", $params);
    // Additionally we want to show the student attendance for the given period
    /***********************************SQL QUERY**********************************/
    $sql = "SELECT COUNT(birth_certificate_number) FROM student_attendance 
            WHERE `birth_certificate_number` = ? AND `attended` = ? AND `date` BETWEEN ? AND ? ";
    $params = array($birthCertNumber, 'Y', $startDate, $endDate);
    $result = mysqli_prepared_query($conn, $sql, "ssss", $params);
    if (!$result) {
        $daysAttended = 0;
    } else {
        $row = $result[0];
        $daysAttended = $row['COUNT(birth_certificate_number)'];
    }
    /***********************************SQL QUERY**********************************/
    $sql = "SELECT COUNT(birth_certificate_number) FROM student_attendance 
            WHERE `birth_certificate_number` = ? AND `attended` = ? AND `date` BETWEEN ? AND ?";
    $params = array($birthCertNumber, 'N', $startDate, $endDate);
    //No need to redefine params
    $result = mysqli_prepared_query($conn, $sql, "ssss", $params);
    if (!$result) {
        $daysAbsent = 0;
    } else {
        $row = $result[0];
        $daysAbsent = $row['COUNT(birth_certificate_number)'];
    }
}
//Get student details: name, etc
/*************************SQL QUERY*********************************************/
$sql = "SELECT CONCAT(`first_name`, ' ', `last_name`) AS name, `gender`, `class`, TIMESTAMPDIFF(YEAR, `birth_date`, CURDATE()) AS age FROM students WHERE birth_certificate_number = ?";
$params = array($birthCertNumber);
$studentDetails = mysqli_prepared_query($conn, $sql, "s", $params);
$studentDetails = $studentDetails[0];
mysqli_close($conn); //Close DB
?>
<!DOCTYPE html>
<html>
    <body>
        <div id="main">
            <?php
            if ($access_level < 2) {
                ?>
                    <h2>Student Reports</h2>
                    <h3>Only managers and administrators can register students</h3>
                <?php
            } else { ?>
                <h2>Student Report for student: <?php echo $birthCertNumber; ?></h2>
                <div id="adminform">
                    <h3>Personal details</h3>
                    <p>Name: <?php echo $studentDetails['name']; ?></p>
                    <p>Gender: <?php echo $studentDetails['gender']; ?></p>
                    <p>Class: <?php echo $studentDetails['class']; ?></p>
                    <p>Age: <?php echo $studentDetails['age']; ?></p>
                </div>
                <div id="adminform" class="noprint">
                    <h3>Select specific period</h3>
                    <form id="selectperiodform" action="student_report.php?id=<?php echo $birthCertNumber; ?>" method="post">
                        <p><label>Start Date: </label><input type="date" name="start_date" value=<?php if ($showReport) { echo $startDate; } else { echo $now; } ?>></input></p>
                        <p><label>End Date: </label><input type="date" name="end_date" value=<?php if ($showReport) { echo $endDate; } else { echo $now; } ?>></input></p>
                        <input type="submit" name="submit" value="Update"></input>
                    </form>
                </div>
                <?php if ($showReport) { ?>
                <h2>Report for the period <?php echo $startDate . ' to ' . $endDate; ?></h2>
                <div id="adminform">
                    <h3>Teacher's notes</h3>
                    <?php foreach ($notes as $note) { ?>
                        <div class="note">
                            <div class="notedate"><?php echo $note['time_stamp']; ?></div>
                            <div class="notetext"><?php echo $note['note']; ?></div><br >
                        </div>
                    <?php } ?>
                    <h3>Student attendance</h3>
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
                <?php
                }
            }
            ?>
        </div> 
    </body>
</html>