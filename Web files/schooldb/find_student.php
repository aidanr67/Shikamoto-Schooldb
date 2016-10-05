<?php
include('session.php');
require_once('menu.php');

/* User should be able to search by birth certificate number, 
 * first name or surname
 */

if ($_GET['page'] == "editclass") {
	$_SESSION['page'] = 'edit_class.php';
	$action = 'Add';
	$showReport = false;
} else if ($_GET['page'] == 'studentdetails') {
	$_SESSION['page'] = 'student_details.php';
	$action = 'Details';
	$showReport = true;
}
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
				if (selected == "bybirthcertnumber") {
					show = 'hidebirthcert';
					hide1 = 'hidelastname';
					hide2 = 'hidefirstname';
				} else if (selected == "bylastname") {
					show = 'hidelastname';
					hide1 = 'hidebirthcert';
					hide2 = 'hidefirstname';
				} else {
					show = 'hidefirstname';
					hide1 = 'hidebirthcert';
					hide2 = 'hidelastname';
				}
				document.getElementById(hide1).style.display = "none";
				document.getElementById(hide2).style.display = "none";
				document.getElementById(show).style.display = "block";
//clear the form:
//document.getElementById('findstudentform').reset();
			};
		</script>
	</head>
	<body>
		<div id="main">
			<?php
			$out = $_SESSION['findStudentReturn']; //return messages from the register_student.php script
			$format = $_SESSION['format']; //Formatting for the return messages
			echo "<span id=$format>$out</span>";
			$_SESSION['findStudentReturn'] = "";
			?>
			<h2>Find a student's information</h2>
			<?php if ($schoolNotSelected) { ?>
				<div id='adminform'>
					<h3>Filter by school</h3>
					<form id='selectschoolform' action='find_student.php?page=studentdetails' method="post">
					<p><select id='selectschool' name='school' class='findinput'>
						<option value="0">All</option>
						<?php foreach ($schools as $school) { ?>
							<option value=<?php echo $school['school_id']; if ($selectedSchool == $school['school_id']) { echo 'selected'; } ?>><?php echo $school['school_name']; ?></option>
							<?php
						} ?>
					</select></p>
					<input type='submit' name='submit' value='Submit'>
					</form>
				</div>
				<?php
			} ?>							
			<div id='adminform'>
				<form id="findstudentform" action='find_student.php?page=studentdetails' method='post'>
					<h3>Please select a search option</h3>
					<select id="searchby" name="searchby" onchange="showHide()" class='findinput'>
					    <option value='bybirthcertnumber'>Birth certificate number</option>
					    <option value="bylastname">Last name</option>
					    <option value="byfirstname">First name</option>
					</select>
					<p id='hidebirthcert'><label>Please enter whole or partial birth certificate number: </label>
					    <input name='birthcertnumber' type='text' class="findinput" pattern='^\d*$' placeholder='Whole or part of 13 digit number' value=""></p>
					<p id='hidelastname' style="display: none"><label>Please enter a whole or partial last name: </label>
					    <input name='lastname' type='text' class="findinput" pattern='^[a-zA-Z\s]*$' placeholder="last name" value=""></p>
					<p id='hidefirstname' style="display: none"><label>Please enter a whole or partial first name: </label>
					    <input name='firstname' type='text' class="findinput" pattern='^[a-zA-Z\s]*$' placeholder="first name" value=""></p>
					<input type="submit" name="submit" value="Search">
				</form>
			</div>
			<?php
/* This page posts to itself to allow the returned values to be displayed
 * on the same page.
 * Get data from above for and buid a table of matching students
 */

			//get school info from session
			$schoolId = $_SESSION['school'];
			//Bool to decide whether to restrict students to school or not
			$allStudents = ($schoolId == "0");
//Connect DB
			$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
			if (!$conn) { //DB connection failed, report error and exit
				print_r($DBerror);
				die;
			}
			if (isset($_POST['submit'])) { //Check if the submit button has been clicked
				$_SESSION['format'] = "red"; //Set formatting to red for errors
				$searchBy = $_POST['searchby']; //Which field to search in
				$searchString = ""; //To hold the search string entered by the user
/**************************************SQL QUERIES**********************************/
				if ($searchBy == "bybirthcertnumber") { //Seach birth cert numbers
				$searchString = $_POST['birthcertnumber'];
				$sql = "SELECT birth_certificate_number, first_name, last_name, class FROM students "
				. "WHERE birth_certificate_number LIKE CONCAT('%', ?, '%')";
				}
				if ($searchBy == "bylastname") { //Search last names
					$searchString = $_POST['lastname'];
					$sql = "SELECT birth_certificate_number, first_name, last_name, class FROM students "
					. "WHERE last_name LIKE CONCAT('%', ?, '%')";
				}
				if ($searchBy == "byfirstname") { //Search first names
					$searchString = $_POST['firstname'];
					$sql = "SELECT birth_certificate_number, first_name, last_name, class FROM students "
					. "WHERE first_name LIKE CONCAT('%', ?, '%')";
				}
				if (empty($searchString)) { //No valid input was entered
					$_SESSION['findStudentReturn'] = "Please enter some information <br>";
					header ("refresh: 0"); //Refreshes the page, displays error msg
					die;
				} else { //A valid search string was entered
					//Need to determine if we want all students or only from a certain school
					if (!$allStudents) { //Filter by school if applicable
						$sql = $sql . " AND `school_id` LIKE ?";
						$params = array($searchString, $schoolId);
						$paramTypes = "si";
					} else {
						$params = array($searchString);
						$paramTypes = "s";
					}
					$result = mysqli_prepared_query($conn, $sql, $paramTypes, $params);
					if (!$result) { //No result was found
						?>
						<div id="adminform">
							<h3>No results found</h3>
						</div>
					<?php
					} else {
						?> 
						<div id="adminform" class="tablecontainer">
							<h3>Results found</h3>
							<table class="findtable">
								<tr>
									<th>Birth certificate number</th>
									<th>Last name</th>
									<th>First name</th>
									<th>Class</th>
									<th class='edittablecolumn'><?php echo $action; ?></th>
									<!-- Only show report if on student details page -->
									<?php if ($showReport) { ?>
										<th>Report</th>
									<?php } ?>
								</tr>
								<?php
								foreach ($result as $row) { //Build table of results
									?>
									<tr>
										<td><?php echo $row['birth_certificate_number'];?></a></td>
										<td><?php echo $row['first_name'];?></td>
										<td><?php echo $row['last_name'];?></td>
										<td><?php echo $row['class'];?></td>
										<td><a href="<?php echo $_SESSION['page'];?>?id=<?php echo $row['birth_certificate_number'];?>"><button id="smalleditbutton"><?php echo $action; ?></button></a></td>
										<?php if ($showReport) { ?>
										<td><a href="student_report.php?id=<?php echo $row['birth_certificate_number']; ?> "><button id="smalleditbutton">Report</button></td>
										<?php } ?>
									</tr>
									<?php
								}
							?>
							</table>
						</div>
					<?php
					}
				}
			}
/*
 *List all students
 */
/**************************************SQL QUERY*******************************************************/
			//Get student count
			$sql = "SELECT COUNT(birth_certificate_number) FROM students";
			$result = mysqli_prepared_query($conn, $sql);
			$count = $result[0]['COUNT(birth_certificate_number)'];
			$sql = "SELECT birth_certificate_number, first_name, last_name, class FROM students";
			if (!$allStudents) {
				$sql = $sql . " WHERE `school_id` LIKE ?";
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
			?>
			<div id="adminform" class="tablecontainer">
				<h3>Full student List: There are currently <?php echo $count;?> students enrolled</h3>
				<table class="findtable">
					<tr>
						<th>Birth certificate number</th>
						<th>Last name</th>
						<th>First name</th>
						<th>Class</th>
						<th class='edittablecolumn'><?php echo $action; ?></th>
						<?php if ($showReport) { ?>
							<th>Report</th>
						<?php } ?>
					</tr>
					<?php
					foreach ($result as $row) { //Build table of results
						?>
						<tr>
							<td><?php echo $row['birth_certificate_number'];?></td>
							<td><?php echo $row['first_name'];?></td>
							<td><?php echo $row['last_name'];?></td>
							<td><?php echo $row['class'];?></td>
							<td><a href="<?php echo $_SESSION['page'];?>?id=<?php echo $row['birth_certificate_number'];?>"><button id="smalleditbutton"><?php echo $action; ?></button></a>
							<?php if ($showReport) { ?>
								<td><a href="student_report.php?id=<?php echo $row['birth_certificate_number']; ?>"><button id="smalleditbutton">Report</button></td>
							<?php } ?>
						</tr>
						<?php
					}
					mysqli_close($conn) //Close DB
					?>
				</table>
			</div>
		</div>
	</body>
</html>
