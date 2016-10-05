<?php
include('session.php');
require_once('menu.php');
/* User should be able to search by ID number, 
 * first name or surname
 */

/*
 * The user has gotten to this page in one of two ways and this affects the
 * link below. We need to check the page GET variable to determine which
 */
if ($_GET['page'] == "createclass") {
    $_SESSION['page'] = 'create_class.php';
    $action = 'Select';
    $showAttendance = false;
} else if ($_GET['page'] == 'staffdetails') {
    $_SESSION['page'] = 'staff_details.php';
    $action = 'Details';
    $showAttendance = true;
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
                if (selected == "byidnumber") {
                    show = 'hideidnumber';
                    hide1 = 'hidelastname';
                    hide2 = 'hidefirstname';
                } else if (selected == "bylastname") {
                    show = 'hidelastname';
                    hide1 = 'hideidnumber';
                    hide2 = 'hidefirstname';
                } else {
                    show = 'hidefirstname';
                    hide1 = 'hideidnumber';
                    hide2 = 'hidelastname';
                }
                document.getElementById(hide1).style.display = "none";
                document.getElementById(hide2).style.display = "none";
                document.getElementById(show).style.display = "block";
            };
        </script>
    </head>
    <body>
        <div id="main">
            <?php
            $out = $_SESSION['findStaffReturn'];
            $format = $_SESSION['format']; //Formatting for the return messages
            echo "<span id=$format>$out</span>";
            $_SESSION['findStaffReturn'] = "";
            ?>
            <h2>Find a staff member's information</h2>
            <div id='adminform'>
                <form id="findstaffform" action='find_staff.php' method='post'>
                    <h3>Please select a search option</h3>
                    <select id="searchby" name="searchby" onchange="showHide()" class='findinput'>
                        <option value='idnumber'>ID number</option>
                        <option value="bylastname">Last name</option>
                        <option value="byfirstname">First name</option>
                    </select>
                    <p id='hideidnumber'><label>Please enter whole or partial ID number</label>
                        <input name='idnumber' type='text' class="findinput" pattern='^\d*$' placeholder='Whole or part of 13 digit number' value=""></p>
                    <p id='hidelastname' style="display: none"><label>Please enter a whole or partial last name</label>
                        <input name='lastname' type='text' class="findinput" pattern='^[a-zA-Z\s]*$' placeholder="last name" value=""></p>
                    <p id='hidefirstname' style="display: none"><label>Please enter a whole or partial first name</label>
                        <input name='firstname' type='text' class="findinput" pattern='^[a-zA-Z\s]*$' placeholder="first name" value=""></p>
                    <input type="submit" name="submit" value="Search">
                </form>
            </div>
            <?php
/* This page posts to itself to allow the returned values to be displayed
 * on the same page.
 * Get data from above for and build a table of matching students
 */
            
            //get school info from session
            $schoolId = $_SESSION['school'];
            //Bool to decide whether to restrict staff to school or not
            $allStaff = ($schoolId == "0");

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
                if ($searchBy == "idnumber") { //Seach birth cert numbers
                    $searchString = $_POST['idnumber'];
                    $sql = "SELECT id_number, first_name, last_name FROM staff 
                    WHERE id_number LIKE CONCAT('%', ?, '%')";
                }
                if ($searchBy == "bylastname") { //Search last names
                    $searchString = $_POST['lastname'];
                    $sql = "SELECT id_number, first_name, last_name FROM staff 
                    WHERE last_name LIKE CONCAT('%', ?, '%')";
                }
                if ($searchBy == "byfirstname") { //Search first names
                    $searchString = $_POST['firstname'];
                    $sql = "SELECT id_number, first_name, last_name FROM staff 
                    WHERE first_name LIKE CONCAT('%', ?, '%')";
                }
                if (empty($searchString)) { //No valid input was entered
                    $_SESSION['findStaffReturn'] = "Please enter some information <br>";
                    header ("refresh: 0"); //Refreshes the page, displays error msg
                    die;
                } else { //A valid search string was entered
                    //Need to determine if we want all students or only from a certain school
                    if (!$allStaff) { //Filter by school if applicable
                        $sql = $sql . " AND `school_id` LIKE ?";
                        $params = array($searchString, $schoolId);
                        $paramTypes = "si";
                    } else {
                        $params = array($searchString);
                        $paramTypes = "s";
                    }
                    $result = mysqli_prepared_query($conn, $sql, $paramTypes, $params);
                    if (!$result) { //A result was found
                    //Set up table to hold results
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
                                        <th>ID number</th>
                                        <th>Last name</th>
                                        <th>First name</th>
                                        <th class='edittablecolumn'><?php echo $action;?></th>
                                        <?php if ($showAttendance) { ?>
                                            <th>Attendence</th>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                    foreach($result as $row) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['id_number'];?></td>
                                            <td><?php echo $row['first_name'];?></td>
                                            <td><?php echo $row['last_name'];?></td>
                                            <td><a href="<?php echo $_SESSION['page'];?>?id=<?php echo $row['id_number'];?>"><button id="smalleditbutton"><?php echo $action;?></button></a></td>
                                            <?php if ($showAttendance) {?>
                                                <td><a href="<?php echo 'staff_attendance.php';?>?id=<?php echo $row['id_number'];?>"><button id="smalleditbutton"><?php echo 'Attendance';?></button></a></td>    
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
/*************************************************SQL QUERY************************************************/
            $sql = "SELECT id_number, first_name, last_name FROM staff";
            if (!$allStaff) {
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
            mysqli_close($conn) //Close DB
            ?>
            <div id="adminform" class="tablecontainer">
                <h3>Full staff List</h3>
                <table class="findtable">
                    <tr>
                        <th>ID number</th>
                        <th>Last name</th>
                        <th>First name</th>
                        <th class='edittablecolumn'><?php echo $action;?></th>
                        <?php if ($showAttendance) { ?>
                            <th>Attendence</th>
                        <?php } ?>
                    </tr>
                    <?php
                    foreach($result as $row) {
                        ?>
                        <tr>
                            <td><?php echo $row['id_number'];?></td>
                            <td><?php echo $row['first_name'];?></td>
                            <td><?php echo $row['last_name'];?></td>
                            <td><a href="<?php echo $_SESSION['page'];?>?id=<?php echo $row['id_number'];?>"><button id="smalleditbutton"><?php echo $action;?></button></a></td>
                            <?php if ($showAttendance) {?>
                                <td><a href="<?php echo 'staff_attendance.php';?>?id=<?php echo $row['id_number'];?>"><button id="smalleditbutton"><?php echo 'Attendance';?></button></a></td>    
                            <?php } ?>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        </div>
    </body>
</html>
