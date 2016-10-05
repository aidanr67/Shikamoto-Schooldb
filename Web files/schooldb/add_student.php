<?php
include('session.php');
require_once('menu.php');

//if session params array is set set POST
if (isset($_SESSION['inputparams'])) {
    $_POST = $_SESSION['inputparams'];
    $_SESSION['inputparams'] = '';
}

if (isset($_SESSION['school'])) { //Check if school is selected, otherwise get schools from DB
    $schoolId = $_SESSION['school'];
    $schoolNotSelected = ($schoolId == "0");
    if ($schoolNotSelected) {
        $conn = mysqli_connect($DBhost, $DBuser, $DBpassword, $DBdatabase);
        if (!$conn) {
            print_r($DBerror);
            die;
        }
        $sql = "SELECT * FROM school";
        $schools = mysqli_prepared_query($conn, $sql);
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <script type="text/javascript">
            function showHideGuardian() {
                if (document.getElementById('hideguardian').style.display == "none") {
                    //show the div:
                    document.getElementById('hideguardian').style.display = "block";
                } else {
                    //hide the div:
                    document.getElementById('hideguardian').style.display = "none";
                    //clear the form:
                    document.getElementById('addstudent').reset();
                }
            };
            function showHideAddress() {
                var elems = document.getElementsByClassName("hideaddress");
                var i;
                for (i = 0; i < elems.length; i++) {
                    if (elems[i].style.display == "none") {
                        //show the div:
                        elems[i].style.display = "block";
                    } else {
                        //hide the div:
                        elems[i].style.display = "none";
                    }
                }
            };
    </script>
</head>
    <body>
        <div id="main">
            <?php
            if ($access_level < 2) {
                ?>
                    <h2>New Student Registration</h2>
                    <h3>Only managers and administrators can register students</h3>
                <?php
            } else {
                $out = $_SESSION['registerStudentReturn']; //return messages from the register_student.php script
                $format = $_SESSION['format']; //Formatting for the return messages
                echo "<span id=$format>$out</span>";
                $_SESSION['registerStudentReturn'] = "";
                ?>
                <h2>New Student Registration</h2>
                <div id="adminform">
                    <form id="addstudentform" action="register_student.php" method="post">
                        <p><h3>Child's information</h3></p>
                        <?php 
                        if ($schoolNotSelected) { ?>
                            <p><label>School: </label>
                            <select id="schoolselect" name="school" class='forminput'>
                                <?php
                                foreach ($schools as $school) { ?>
                                    <option value=<?php echo $school['school_id'];?>><?php echo $school['school_name'];?></option>
                                    <?php
                                 } 
                                ?>
                            </select></p>
                            <?php
                        }
                        ?>
                        <p><span id="red">*</span>
                            <label>Date of admission: </label>
                            <?php echo "<input type='date' name='dateofadmission' pattern='(^[0-9]{4}-(0[1-9]|1[0-2])-[0-3][0-9]$)' placeholder='yyyy-mm-dd' 
                            class='forminput' value='".date('Y-m-d')."'";?>"</p>
                        <p><span id="red">*</span>
                            <label>First names: </label>
                            <input type="text" name="firstname" class='forminput' pattern='^[a-zA-Z\s]*$' placeholder="first names" value="<?php echo $_POST['firstname'];?>"></p>
                        <p><span id="red">*</span>
                            <label>Last name: </label>
                            <input type="text" name="lastname" class='forminput' pattern='^[a-zA-Z\s]*$' placeholder="last name" value="<?php echo $_POST['lastname'];?>"></p>
                        <p><span id="red">*</span>
                            <input type="radio" name="gender" value="male" <?php if ($_POST['gender'] == 'male') {echo "checked";}?>>Male
                            <input type="radio"name="gender" value="female" <?php if ($_POST['gender'] == 'female') {echo "checked";}?>>Female</p>
                        <p><span id="red">*</span>
                            <label>Birth certificate number: </label>
                            <input type="text" name="birthcertnumber" placeholder='13 digit number' pattern='<?php echo $validId; ?>' class='forminput' value="<?php echo $_POST['birthcertnumber'];?>"></p>
                        <p><span id ="red">*</span>
                            <label>Date of birth: </label>
                            <input type='date' name="dateofbirth" class='forminput' pattern='(^[0-9]{4}-(0[1-9]|1[0-2])-[0-3][0-9]$)' placeholder="yyyy-mm-dd" value="<?php echo $_POST['dateofbirth'];?>"></p>
                        <p><h3>Primary Guardian's information</h3></p>
                        <p><span id="red">*</span>
                            <label>First names: </label>
                            <input type="text" name="guardian1firstname" class='forminput' pattern='^[a-zA-Z\s]*$' placeholder="first names" value="<?php echo $_POST['guardian1firstname'];?>"></p>
                        <p><span id="red">*</span>
                            <label>Last name: </label>
                            <input type="text" name="guardian1lastname" class='forminput' pattern='^[a-zA-Z\s]*$' placeholder="last name" value="<?php echo $_POST['guardian1lastname'];?>"></p>
                        <p><span id="red">*</span>
                            <label>ID number: </label>
                            <input type="text" name="guardian1idnumber" class='forminput' placeholder='13 digit number' pattern='<?php echo $validId; ?>' value="<?php echo $_POST['guardian1idnumber'];?>"></p>
                        <p><span id="red">*</span><h7> Primary guardian's contact details (Provide at least a cellphone number)</h7></p>
                        <p><label>Home phone number: </label>
                            <input type="text" name="guardian1homenumber" class='forminput' pattern='^\d{10}$' placeholder='10 digit number' value="<?php echo $_POST['guardian1homenumber'];?>"></p>
                        <p><label>Work phone number: </label>
                            <input type="text" name="guardian1worknumber" class='forminput' pattern='^\d{10}$' placeholder='10 digit number' value="<?php echo $_POST['guardian1worknumber'];?>"></p>
                        <p><span id="red">*</span>
                            <label>Cell phone number: </label>
                            <input type="text" name="guardian1cellnumber" class='forminput' pattern='^\d{10}$' placeholder='10 digit number' value="<?php echo $_POST['guardian1cellnumber'];?>"></p>
                        <p><span id="red">*</span><label> Primary guardian's address:</label>
                            <input type="text" name="guardian1address" class='forminput' value="<?php echo $_POST['guardian1address'];?>"></p>
                        <p><input type="checkbox" name="isguardian2" value="yes" onchange="showHideGuardian()" checked>Provide a second guardian</p>
                        <div id="hideguardian">
                            <p><h3>Secondary Guardian's information</h3></p>
                            <p><span id="red">*</span>
                                <label>First names: </label>
                                <input type="text" name="guardian2firstname" placeholder="first names" pattern='^[a-zA-Z\s]*$' class='forminput' value="<?php echo $_POST['guardian2firstname'];?>"></p>
                            <p><span id="red">*</span>
                                <label>Last name: </label>
                                <input type="text" name="guardian2lastname" placeholder="last name" pattern='^[a-zA-Z\s]*$' class='forminput' value="<?php echo $_POST['guardian2lastname'];?>"></p>
                            <p><span id="red">*</span>
                                <label>ID number: </label>
                                <input type="text" name="guardian2idnumber" class='forminput' placeholder='13 digit number' pattern='<?php echo $validId; ?>' value="<?php echo $_POST['guardian2idnumber'];?>"></p>
                            <p><span id="red">*</span><h7>Secondary guardian's contact details (Provide at least a cellphone number)</h7></p>
                            <p class="hideaddress"><label>Home phone number: </label>
                                <input type="text" name="guardian2homenumber" class='forminput' pattern='^\d{10}$' placeholder='10 digit number' value="<?php echo $_POST['guardian2homenumber'];?>"
                                    placeholder="Leave blank if same as primary home number"></p>
                            <p><label>Work phone number: </label>
                                <input type="text" name="guardian2worknumber" class='forminput' pattern='^\d{10}$' placeholder='10 digit number' value="<?php echo $_POST['guardian2worknumber'];?>"></p>
                            <p><span id="red">*</span>
                                <label>Cell phone number: </label>
                                <input type="text" name="guardian2cellnumber" class='forminput' pattern='^\d{10}$' placeholder='10 digit number' value="<?php echo $_POST['guardian2cellnumber'];?>"></p>
                            <p><span id="red">*</span><h7>Secondary guardian's address: </h7></p>
                                <p><input type="checkbox" name="guardian2addresssame" value="yes" onchange="showHideAddress()"> Same as primary guardian</p>
                                <p class='hideaddress'><input type="text" name="guardian2address" class='forminput' value="<?php echo $_POST['guardian2address'];?>"></p>
                        </div>
                        <p><input type="submit" name='submit' value="Submit"></p>
                    </form>        
                </div>
                <?php
            }
            ?>
        </div> 
    </body>
</html>