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
    <body>
        <div id="main">
            <?php
            if ($access_level < 2) {
                ?>
                    <h2>New Staff Registration</h2>
                    <h3>Only managers and administrators can register staff</h3>
                <?php
            } else {
                $out = $_SESSION['registerStaffReturn']; //return messages from the register_student.php script
                $format = $_SESSION['format']; //Formatting for the return messages
                echo "<span id=$format>$out</span>";
                $_SESSION['registerStaffReturn'] = $out = "";
                ?>
                <h2>New Staff Registration</h2>
                <div id="adminform">
                    <form id="addstudentform" action="register_staff.php" method="post">
                        <p><h3>Staff members' information</h3></p>
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
                            <label>Date of employment: </label>
                            <?php echo "<input type='date' name='employmentdate' placeholder='yyyy-mm-dd' pattern='(^[0-9]{4}-(0[1-9]|1[0-2])-[0-3][0-9]$)' class='forminput' value='".date('Y-m-d')."'";?>"</p>
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
                            <label>ID number: </label>
                            <input type="text" name="idnumber" pattern='<?php echo $validId; ?>' placeholder='13 digit number' class='forminput' value="<?php echo $_POST['idnumber'];?>"></p>
                        <p><span id="red">*</span><h7>Contact details (Provide at least a cellphone number)</h7></p>
                        <p><label>Home phone number: </label>
                            <input type="text" name="homenumber" pattern='^\d{10}$' placeholder='10 digit number' class='forminput' value="<?php echo $_POST['homenumber'];?>"></p>
                        <p><span id="red">*</span>
                            <label>Cell phone number: </label>
                            <input type="text" name="cellnumber" pattern='^\d{10}$' placeholder='10 digit number' class='forminput' value="<?php echo $_POST['cellnumber'];?>"></p>
                        <p><label>Address: </label>
                            <input type="text" name="address" class='forminput' value="<?php echo $_POST['address'];?>"></p>
                        <h3>Next of kin information</h3>
                        <p><span id="red">*</span>
                            <label>First names: </label>
                            <input type="text" name="nokfirstname" class='forminput' pattern='^[a-zA-Z\s]*$' placeholder="first names" value="<?php echo $_POST['nokfirstname'];?>"></p>
                        <p><span id="red">*</span>
                            <label>Last name: </label>
                            <input type="text" name="noklastname" class='forminput' pattern='^[a-zA-Z\s]*$' placeholder="last name" value="<?php echo $_POST['noklastname'];?>"></p>
                        <p><span id="red">*</span><h7>Next of kin's contact details (Provide at least a cellphone number)</h7></p>
                        <p><label>Home phone number: </label>
                            <input type="text" name="nokhomenumber" class='forminput' pattern='^\d{10}$' placeholder='10 digit number' value="<?php echo $_POST['nokhomenumber'];?>"></p>
                        <p><label>Work phone number: </label>
                            <input type="text" name="nokworknumber" class='forminput' pattern='^\d{10}$' placeholder='10 digit number' value="<?php echo $_POST['nokworknumber'];?>"></p>
                        <p><span id="red">*</span>
                            <label>Cell phone number: </label>
                            <input type="text" name="nokcellnumber" class='forminput' pattern='^\d{10}$' placeholder='10 digit number' value="<?php echo $_POST['nokcellnumber'];?>"></p>
                        <p><label>Address: </label>
                            <input type="text" name="nokaddress" class='forminput' value="<?php echo $_POST['nokaddress'];?>"></p>
                        <p><input type="submit" name='submit' value="Submit"></p>
                    </form>        
                </div>
                <?php
            }
            ?>
        </div> 
    </body>
</html>