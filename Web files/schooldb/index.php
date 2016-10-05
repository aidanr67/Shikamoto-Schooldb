<?php
include('login.php');
//If the user is already logged in direct them to admin.php
if (isset($_SESSION['login_user'])) {
    header('location: admin.php');
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Student Administration</title>
		<link rel="shortcut icon" href="images/ShikaLogoSmall.png" type="image/png" />
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="stylesheet" type="text/css" media="only screen and (max-width:1080px)" href="style_small.css" />
		<link rel="stylesheet" type="text/css" media="only screen and (min-width:1081px)" href="style_large.css" />
	</head>
	<body>
		<div class='topspacer'>
		</div>
		<p id='red'><?php echo $_SESSION['timeoutmsg']; ?></p>
		<div id="main">
			<div id="logo">
				<a href="http://shikamoto.lan"><img src="./images/shika-moto-logo.gif" width="100" height="100"></a>
			</div>
			<h1>Student Administration</h1>
			<div id="login">
				<h2>Login</h2>
				<div class='form'>
					<form action="" method="post" class='login'>
						<p><label>School: </label>
						<select id="schoolid" name="schoolid" class='logininput'>
	                        <option value="0" >All</option>
	                        <?php
	                        	foreach ($schools as $school) {?>
	                        		<option value=<?php echo $school['school_id'];?>><?php echo $school['school_name']?></option>
	                        		<?php
	                        	}
	                        ?>
                    	</select></p><br>
						<p><label>Username: </label><input id="name" name="username" class="logininput" type="text"></p><br>
						<p><label>Password: </label><input id="password" name="password" class="logininput" placeholder="***********" type="password"></p><br>
						<input name="submit" type="submit" value="login" class='submitbutton'>
						<p><span id="red"><?php echo $error; ?></span></p>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>

