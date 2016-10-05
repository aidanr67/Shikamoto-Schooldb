<!-- Top menu -->
<!DOCTYPE html>
<html>
	<head>
		<title>Student Administration</title>
		<link rel="shortcut icon" href="images/ShikaLogoSmall.png" type="image/png" />
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="stylesheet" type="text/css" media="only print" href="print.css" />
		<link rel="stylesheet" type="text/css" media="only screen and (max-width:800px)" href="style_small.css" />
		<link rel="stylesheet" type="text/css" media="only screen and (min-width:801px)" href="style_large.css" />
		<script type="text/javascript">
			// close drop down if user clicks outside of it
			window.onclick = function(event) {
				if (!event.target.matches('.dropbtn')) {
    				var dropdowns = document.getElementsByClassName("dropdown-content");
			   		var i;
			    	for (i = 0; i < dropdowns.length; i++) {
			    		var openDropdown = dropdowns[i];
				    	if (openDropdown.classList.contains('show')) {
				        	openDropdown.classList.remove('show');
				    	}
					}
				}
			};
			// toggle hiding and showing dropdown content on click
			function dropdown(section) {
				//First hide all dropdowns in case any are open
				var dropdowns = document.getElementsByClassName("dropdown-content");
			   		var i;
			    	for (i = 0; i < dropdowns.length; i++) {
			    		var openDropdown = dropdowns[i];
				    	if (openDropdown.classList.contains('show') && openDropdown.id != section) {
				        	openDropdown.classList.remove('show');
				    	}
				    }
				// show selected dropdown
				document.getElementById(section).classList.toggle("show");
			};
		</script>
	</head>
	<body>
		<div id="logo">
			<a href="http://shikamoto.lan"><img src="./images/shika-moto-logo.gif" width="100" height="100"></a>
		</div>
		<div id="menu">
			<h1>Welcome to Student Administration: <strong id="right"> <?php echo $login_session; ?></strong></h1> 
			<div class="menubuttons">   
				<div class="dropdown">
					<button onclick="dropdown('studentDropdown')" class="dropbtn">Students</button>
					<div id="studentDropdown" class="dropdown-content">
						<a href="add_student.php">Add Student</a>
						<a href="find_student.php?page=studentdetails">Find Student</a>
					</div>
				</div>
				<div class="dropdown">
					<button onclick="dropdown('staffDropdown')" class="dropbtn">Staff</button>
					<div id="staffDropdown" class="dropdown-content">
						<a href="add_staff.php">Add Staff</a>
						<a href="find_staff.php?page=staffdetails">Find Staff</a>
						<a href="staff_signin.php">Staff Sign-in</a>
					</div>
				</div>
				<div class="dropdown">
					<button onclick="dropdown('classDropdown')" class="dropbtn">Class</button>
					<div id="classDropdown" class="dropdown-content">
						<a href="create_class.php">Create class</a>
						<a href="list_classes.php">List classes</a>
						<a href="today_register.php">Today's register</a>
					</div>
				</div>
				<div class="dropdown">
					<button onclick="dropdown('schoolDropdown')" class="dropbtn">School</button>
					<div id="schoolDropdown" class="dropdown-content">
						<a href="create_school.php">Create school</a>
						<a href="remove_school.php">Remove school</a>
					</div>
				</div>
				<div class="dropdown">
					<button onclick="dropdown('accountsDropdown')" class="dropbtn">Accounts</button>
					<div id="accountsDropdown" class="dropdown-content">
						<a href="student_accounts_list.php">Student accounts</a>
					</div>
				</div>
				<div class="dropdown">
					<button onclick="dropdown('usersDropdown')" class="dropbtn">Users</button>
					<div id="usersDropdown" class="dropdown-content">
						<a href="manage_users.php">Manage users</a>
					</div>
				</div>
				<button id="logoutbutton" type="button" onclick="location.href='logout.php'">Logout</button>
			</div>	
		</div>
	</body>
</html>
