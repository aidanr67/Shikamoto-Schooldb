<?php
include('session.php');
require_once('menu.php');
/*
 * If $_GET['id'] is set we can use this as the teachers ID number
 * Likewise if $_POST['teacher'] is set we can use that.
 */
$idNumber = "";
if (isset($_GET['id'])) {
	$idNumber = $_GET['id'];
} else if (isset($_POST['teacher'])) {
	$idNumber = $_POST['teacher'];
}
//Get school list
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
} else {
	$sql = "SELECT * FROM school";
	$schools = mysqli_prepared_query($conn, $sql);
	mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>
	<body>
		<div id="main">
			<?php
            if ($access_level < 2) {
                ?>
                    <h2>Create Class</h2>
                    <h3>Only managers and administrators can create classes</h3>
                <?php
            } else {
				$out = $_SESSION['registerClassReturn'];
				$format = $_SESSION['format']; //Formatting for the return messages
				echo "<span id=$format>$out</span>";
				$_SESSION['registerClassReturn'] = "";
				?>
				<h2>Create new class</h2>
				<div id="adminform">
					<form id="createclassform" action="register_class.php" method="post">
						<p><span id="red">*</span>
							<label>Main teacher ID number: </label><input type="text" name="teacher"
	                        	class="forminput" pattern='^\d{13}$' placeholder='13 digit number' value="<?php echo $idNumber;?>"/><br><br><label> or find a teacher ID number: </label>
						<a href="find_staff.php?page=createclass"><button type="button" class="searchbutton">Find ID number</button></a></p>
						<p><label>School: </label>
						<select id="schoolselect" name="school" class="staff">
							<?php foreach ($schools as $school) { ?>
								<option value=<?php echo $school['school_id']; 
									if ($schoolId == $school['school_id']) { echo ' selected'; }?>><?php echo $school['school_name'];?></option>
								<?php
							} ?>
						</select></p>
						<p><span id="red">*</span>
							<label>Class number: </label><input type="text" name="classnumber" class="forminput" placeholder='eg. A1' pattern='^[a-zA-Z0-9]*$' value="<?php echo $_POST['classnumber'];?>"/></p>
						<p><label>Description: </label><input type='text' name="description" class="forminput" value="<?php echo $_POST['description'];?>"></p>
						<input type="submit" name="submit" value="submit"/>
					</form>
				</div>
				<?php
			}
			?>
		</div>
	</body>
</html>