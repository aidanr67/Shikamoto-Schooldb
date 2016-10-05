<?php
include('session.php');
require_once('menu.php');

/* Users can be created, edited and deleted here.
 * Usernames and passwords can also be retrieved
 */
$conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
//Retieve all active users
$sql = "SELECT * FROM users";
$userList = mysqli_prepared_query($conn, $sql);
//Get School list
$sql = "SELECT * FROM school";
$schools = mysqli_prepared_query($conn, $sql);
//retrieve post details
$_POST = $_SESSION['postinput'];
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
	<body>
		<div id='main'>
		<?php
            if ($access_level < 3) {
                ?>
                	<h2>User management</h2>
                    <h3>Only administrators can manage user accounts</h3>
                <?php
            } else {
	            $out = $_SESSION['userReturn']; //return messages from the register_student.php script
	            $format = $_SESSION['format']; //Formatting for the return messages
	            echo "<span id=$format>$out</span>";
	            $_SESSION['userReturn'] = $out = "";
	            ?>
	            <h2>User management</h2>
	            <div id='adminform'>
	            	<form action='create_user.php' method="post" name='createuser'>
	            		<h3>Create a new user</h3>
	            		<p><label>School: (Limit user to a school)</label><select id='school' name='school' class='forminput'>
	            		<option value='0'>None</option>
	            			<?php
	            			foreach ($schools as $school) { ?>
	            				<option value=<?php echo $school['school_id'];?>><?php echo $school['school_name'];?></option>
	            				<?php
	            			}
	            			?>
	            		</select></p>
	            		<p><label>Username: </label><input type='text' name='username' class='forminput' placeholder='username' pattern='^[a-zA-Z]*$'
	            			value="<?php echo $_POST['username'];?>"></p>
	            		<p><label>Password: </label><input type='password' name='password' class='forminput' placeholder="***********"
	            			value="<?php echo $_POST['password'];?>"></p>
	            		<p><label>Confirm password: </label><input type='password' name='passwordconfirm' class='forminput' placeholder='***********'
	            			value="<?php echo $_POST['passwordconfirm'];?>"></p>
	            		<p><label>First name: </label><input type='text' class='forminput' name='firstname' placeholder="optional" pattern="^[a-zA-Z\s]*$"
	            			value="<?php echo $_POST['firstname']; ?>"></p>
	            		<p><label>Last name: </label><input type='text' class='forminput' name='lastname' placeholder="optional" pattern='^[a-zA-Z\s]*$'
	            			value="<?php echo $_POST['lastname']; ?>"></p>
	            		<p><label>Administration level</label><select class="forminput" name="adminlevel">
	            			<option value="1"><?php echo $adminLevel1;?></option>
	            			<option value="2"><?php echo $adminLevel2;?></option>
	            			<option value="3"><?php echo $adminLevel3;?></option>
	            		</select>
	            		<input type='submit' name='createsubmit' value="Submit">
	            	</form>
	            </div>
	            <div id='adminform' class='tablecontainer'>
	            	<h3>Current users</h3>
	            	<table class='findtable'>
	            		<tr>
	            			<th>Username</th>
	            			<th>First name</th>
	            			<th>Last name</th>
	            			<th class='edittablecolumn'>Edit</th>
	            		</tr>
	            		<?php
	            		foreach ($userList as $row) {
	            			$username = $row['user'];
	           				$firstName = $row['first_name'];
	           				$lastName = $row['last_name'];
	           				?>
	           				<tr>
	            				<td><?php echo $username;?></td>
	            				<td><?php echo $firstName;?></td>
	            				<td><?php echo $lastName;?></td>
	            				<td><a href="edit_user.php?user=<?php echo $username;?>"><button id="smalleditbutton">Edit</button></a></td>
	            			</tr>
	            			<?php
	            			}
	            			?>
	            		</tr>
	            	</table>
	            </div>
	            <?php
	        }
	        ?>
		</div>
	</body>
</html>