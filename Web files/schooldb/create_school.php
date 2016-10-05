<?php
include('session.php');
require_once('menu.php');

?>
<!DOCTYPE html>
<html>
	<body>
		<div id="main">
			<?php
            if ($access_level < 2) {
                ?>
                    <h2>Create School</h2>
                    <h3>Only managers and administrators can create classes</h3>
                <?php
            } else {
				$out = $_SESSION['registerSchoolReturn'];
				$format = $_SESSION['format']; //Formatting for the return messages
				echo "<span id=$format>$out</span>";
				$_SESSION['registerSchoolReturn'] = "";
				?>
				<h2>Create new school</h2>
				<div id="adminform">
					<form id="createschoolform" action="register_school.php" method="post">
						<p><span id="red">*</span>
						<label>School Name: </label><input type="text" name="schoolname" class="forminput" value="<?php echo $_POST['classnumber'];?>" /></p>
						<input type="submit" name="submit" value="submit"/>
					</form>
				</div>
				<?php } ?>
		</div>
	</body>
</html>