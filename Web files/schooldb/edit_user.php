<?php
include('session.php');
require_once('menu.php');
/* Get username from GET and allow editing of said user*/

$username = $_GET['user'];
if (empty($username)) {
	print_r("Communication error, call administrator");
	return 0;
	die;
}
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
if (!$conn) {
	print_r($DBerror);
	die;
}
/***************************************SQL Query*******************************************/
$sql = "SELECT * FROM `users` WHERE `user`=?";
$params = array($username);
$result = mysqli_prepared_query($conn, $sql, 's', $params);
//There will only be one result since we're SELECTing on the PK
$userArray = $result[0];
//Assign user details to variables for easy use
$password = $userArray['password'];
$firstName = $userArray['first_name'];
$lastName = $userArray['last_name'];
$schoolId = $userArray['school_id'];
$adminLevel = $userArray['administrator'];
/***************************************SQL Query*******************************************/
$sql = "SELECT * FROM `school`";
$schools = mysqli_prepared_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript">
			function edit(button) {
				var fields = document.getElementsByClassName('forminput');
				var i;
				if (button.value == 'Edit') {
					for (i = 0; i < fields.length; i++) {
						fields[i].hidden = false;
						fields[i].disabled = false;
					}
					document.getElementById('showhide').hidden = false;
					document.getElementById('submitedituser').hidden = false;
					document.getElementById('password').type='password';
					button.value = "Cancel";
				} else {
					for (i = 0; i < fields.length; i++) {
						fields[i].hidden = true;
					}
					location.reload();
					button.value = "Edit";
					document.getElementById("submitedituser").hidden = true;
				}
			}
			function revealPassword(button) {
				if (button.value == 'Reveal password') {
					document.getElementById('password').type = 'text';
					button.value = 'Hide password';
				} else {
					document.getElementById('password').type = 'password';
					button.value = 'Reveal password';
				}
			}
		</script>
	</head>
	<body>
		<div id='main'>
			<?php
			if ($username == 'admin') {
				?>
				<h3>Admin user cannot be changed!</h3>
				<?php
			}
            $out = $_SESSION['userReturn']; //return messages from the register_student.php script
            $format = $_SESSION['format']; //Formatting for the return messages
            echo "<span id=$format>$out</span>";
            $_SESSION['userReturn'] = $out = "";
            ?>
            <h2>Edit user: <?php echo $username;?></h2>
            <div id='adminform'>
            	<input type='button' id='removebutton' class='removebutton' onclick="revealPassword(this)" value="Reveal password">
            	<form id='edituser' action="update_user.php?user=<?php echo $username;?>" method='post'>
	            	<div class='adminspacer'>
	            	<?php
	            	if ($username != "admin") {
	            		?>
	            		<input id="editbutton" type="button" onclick="edit(this)" value="Edit">
	            		<?php
	            	}
	            	?>
	            		<h3>User details</h3>
	            	</div>
	            	<p><label>School: </label>
	            		<select id="schoolselect" disabled name="school" class="forminput">
							<option value='0'>All</option>
							<?php foreach ($schools as $school) { ?>
								<option value=<?php echo $school['school_id']; 
									if ($schoolId == $school['school_id']) { echo ' selected'; }?>><?php echo $school['school_name'];?></option>
								<?php
							} ?>
						</select></p></p>
	            	<p><label>Username: </label><input type='text' name='username' disabled class='forminput' value="<?php echo $username;?>"></p>
	            	<p><label>Password: </label><input type='password' name='password' id='password' disabled class='forminput' value="<?php echo $password;?>"></p>
	            	<p><label hidden id='showhide'>Confirm password: </label><input type='password' name='passwordconfirm' disabled hidden class="forminput" class='showhide'
	            		value="<?php echo $password;?>"></p>
	            	<p><label>First name: </label><input type='text' disabled name='firstname' class='forminput' value="<?php echo $firstName;?>"></p>
	            	<p><label>Last name: </label><input type='text' disabled name='lastname' class="forminput" value="<?php echo $lastName;?>"></p>
	            	<p><label>Administrative level: </label>
	            		<select disabled class="forminput" name='adminlevel'>
	            			<option value="1" <?php if ($adminLevel == "1") { echo 'selected'; }?> ><?php echo $adminLevel1;?></option>
	            			<option value="2" <?php if ($adminLevel == "2") { echo 'selected'; }?> ><?php echo $adminLevel2;?></option>
	            			<option value="3" <?php if ($adminLevel == "3") { echo 'selected'; }?> ><?php echo $adminLevel3;?></option>
	            		</select></label></p>
	            	<input type='submit' name='submitedituser' id='submitedituser' value="Submit" hidden>
	            </form>
            </div>
		</div>
	</body>
</html>