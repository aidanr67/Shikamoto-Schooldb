<?php
include('session.php');
require_once('menu.php');

//Connect DB
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
} else {
	$sql = "SELECT * FROM school";
	$schools = mysqli_prepared_query($conn, $sql);
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
				$out = $_SESSION['removeSchoolReturn'];
				$format = $_SESSION['format']; //Formatting for the return messages
				echo "<span id=$format>$out</span>";
				$_SESSION['removeSchoolReturn'] = "";
				?>
				<h2>Remove School</h2>
				<div id="adminform">
					<p><strong>Warning! </strong> This will unallocate all students, classes and staff allocated to this school</p>
					<form id="removeschoolform" action="deregister_school.php" method="post">
						<p><span id="red">*</span>
							<label>School: </label>
						<select id="schoolselect" name="school" class="staff">
							<?php foreach ($schools as $school) { ?>
								<option value=<?php echo $school['school_id']; 
									if ($schoolId == $school['school_id']) { echo ' selected'; }?>><?php echo $school['school_name'];?></option>
								<?php
							} ?>
						</select></p>
						<input type="submit" name="submit" value="submit"/>
					</form>
				</div>
				<?php
			}
			?>
		</div>
	</body>
</html>