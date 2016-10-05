<?php
include('session.php');
require_once('menu.php');
/*
 * Fetch a list of all classes and display in a table
 */

$error = ""; //string to hold errors
//Connect DB
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);

if (!$conn) {
	print_r($DBerror);
die;
}

$schoolId = $_SESSION['school'];
/*****************************SQL QUERY****************************************/
$sql = "SELECT `class_number`, `teacher` FROM class";
if ($schoolId != "0") {
	$sql = $sql . " WHERE `school_id` = ?";
	$params = array($schoolId);
	$result = mysqli_prepared_query($conn, $sql, "i", $params);
} else {
	$result = mysqli_prepared_query($conn, $sql);
}
if (!$result) {
	$error = 'No results for class' . mysqli_error($conn);
}
// Get 
?>
<!DOCTYPE html>
<html>
	<body>
		<div id="main">
			<p id="red"><?php echo $error;?></p>
			<h2>List of classes</h2>
			<div id="adminform">
				<div class='adminspacer'></div>
				<table class="findtable">
					<tr>
						<th>Class number</th>
						<th>Teacher</th>
					</tr>
					<?php
					foreach ($result as $row) {
						?>
						<tr>
							<td><a href="edit_class.php?classid=<?php echo $row['class_number'];?>">
								<?php echo $row['class_number'];?></a></td>
							<td><a href="staff_details.php?id=<?php echo $row['teacher'];?>">
								<?php echo $row['teacher']?></a></td>
						</tr>
						<?php
					}
					?>
				</table>
			</div>
		</div>
	</body>
</html>

