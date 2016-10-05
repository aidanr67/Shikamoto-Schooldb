<?php
include('session.php');
require_once('menu.php');
/* 
 * Get the class number from GET['id']
 * Load class details with EDIT functionality
 * Load students associated with this class
 */

$newstudent = ''; //String to hold new students
$error = ""; //string to host errors
if ($_SESSION['updateClassReturn']) {
	$error = $_SESSION['updateClassReturn'];
}
$_SESSION['updateClassReturn'] = '';
//Connect DB
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	die;
}
/*
 * There are two ways a client might get here, either they've just clicked
 * on the class number in class lists and $_GET['classid'] is set with this
 * class number. Or they've already been here once and have left to find a 
 * student number to add to this class. Therefore we need to make class number
 * persistant for returning without $_GET['classid'] set and if $_GET['id'] is
 * set we need to store this number to add to the list.
 */
if ($_GET['classid']) {
	$_SESSION['currentclass'] = $_GET['classid'];
} else if ($_GET['id']) {
	$newstudent = $_GET['id'];
}
$classNumber = $_SESSION['currentclass'];
$teacher = '';
$description = '';
//Get additional details
/*******************************SQL QUERY**************************************/
$sql = "SELECT s.first_name, s.last_name, c.description, c.teacher "
. "FROM class c JOIN staff s ON s.`id_number` = c.`teacher` "
. "WHERE c.`class_number` = ?";
$params = array($classNumber);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
if (!$result) {
	$error = "Could not get class information " . mysqli_error($conn);
} else {
	$row = $result[0];
	$firstName = $row['first_name'];
	$lastName = $row['last_name'];
	$description = $row['description'];
	$teacherId = $row['teacher'];
}
/*Before getting a student list for this class we need to add a student
 * id to the list if we have one set
 */
if ($newstudent != '') { //If this variable is set 
	$sql = "UPDATE students SET `class`= ? WHERE `birth_certificate_number` = ?";
	$params = array($classNumber, $newstudent);
	$result = mysqli_prepared_query($conn, $sql, "ss", $params);
	if (!$result) {
		$error = "Could not add new student " . mysqli_error($conn);
	}
}
// Now get the results for students in this class
$sql = "SELECT `birth_certificate_number`, `first_name`, `last_name`, `gender` 
		FROM students WHERE class = ?";
$params = array($classNumber);
$result = mysqli_prepared_query($conn, $sql, "s", $params);
if (!$result) {
	$error = "There are no students assigned to this class";
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript">
			function edit(button, section) {
				var fields = document.getElementsByClassName(section);
				var i;
				if (button.value == 'Edit') {
					for (i = 0; i < fields.length; i++) {
						fields[i].hidden = false;
					}
					document.getElementById(section + 'submit').disabled = false;
					button.value = "Cancel";
				} else {
					for (i = 0; i < fields.length; i++) {
						fields[i].hidden = true;
					}
					location.reload();
					button.value = "Edit";
					document.getElementById("classsubmit").disabled = true;
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
			?>
			<p id="<?php echo $_SESSION['format'];?>"><?php echo $error;?></p>
			<h2>Class information for class: <?php echo $classNumber;?></h2>
			<div id="adminform" class='tablecontainer'>
				<h3>Class information</h3>
				<form id="classupdateform" action="update_class.php?classid=<?php echo $classNumber;?>" method="post">
					<?php
					if ($access_level > 1) {
						?>
						<input id='editbutton' type="button" onclick="edit(this, 'class')" value="Edit">
						<?php
					}
					?>
					<table class="findtable">
						<tr>
							<td>Class name</td>
							<td><input type="text" name="newclassnumber" class="class" hidden 
					           value="<?php echo $classNumber;?>"/><?php echo $classNumber;?></td>
						</tr>
						<tr>
							<td>Teacher: </td>
							<td><input type="text" name="teacher" class="class" hidden 
					           value="<?php echo $teacherId;?>"/><?php echo $teacherId;?></td>
						</tr>
						<tr>
							<td>Teacher name: </td>
							<td><?php echo $firstName . " " . $lastName;?></td>
						</tr>
						<tr>
							<td>Description: </td>
							<td><input hidden class="class" name='description' value='<?php echo $description;?>'>
								<?php echo $description;?></td>
						</tr>
					</table>
					<?php
					if ($access_level > 1) {
						?>
						<input type="submit" name="submit" id="classsubmit" disabled value="Save"/>
						<?php
					}
					?>
				</form>
			</div>
			<div id="adminform" class="tablecontainer">
				<?php 
				if ($access_level > 1) {
					?> 
					<a href="find_student.php?page=editclass"><button type="button" id="addstudentbutton">Add Student</button></a>
					<?php
				}
				?>
				<h3>Students in this class</h3>
				<table class="findtable">
					<tr>
						<th>Birth certificate number</th>
						<th>First name</th>
						<th>Last name</th>
						<th class='edittablecolumn'>Remove</th>
						<th>Add Note</th>
					</tr>
					<?php
					foreach ($result as $row) {
						?>
						<tr>
							<td><?php echo $row['birth_certificate_number'];?></td>
							<td><?php echo $row['first_name'];?></td>
							<td><?php echo $row['last_name'];?></td>
							<?php
							if ($access_level > 1) {
								?>
								<td><a href="remove_from_class.php?id=<?php echo $row['birth_certificate_number'];?>"><button id="smalleditbutton">Remove</button></a></td>
								<?php 
							}
							?>
							<th><a href="add_student_note.php?id=<?php echo $row['birth_certificate_number']; ?>&classid=<?php echo $classNumber; ?>"><button id="smalleditbutton">Add Note</button></a></th>
						</tr>
						<?php
					}
					?>
				</table>
			</div>
		</div>
	</body>
</html>