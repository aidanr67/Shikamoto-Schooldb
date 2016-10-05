<?php
include('session.php');
require_once('menu.php');

/*
 * Get student ID number from GET
 */
if (isset($_GET['id']) && isset($_GET['classid'])) { //This should be set
    $birthCertNumber = $_GET['id']; //Student's birth certificate number
    $classNumber = $_GET['classid']; //Class number, used to return to correct class
} else { //if 'id' is not set there is a serious error
    print_r("ERROR: communication error, please call an administrator");
    die;
}
?>

<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <div id="main">
            <?php
            $out = $_SESSION['newNoteReturn']; //return messages from the register_student.php script
            $format = $_SESSION['format']; //Formatting for the return messages
            echo "<span id=$format>$out</span>";
            $_SESSION['newNoteReturn'] = "";
            ?>
            <h2>New Student Note</h2>
            <div id="adminform">
                <form id="addstudentform" action="new_student_note.php?id=<?php echo $birthCertNumber; ?>&classid=<?php echo $classNumber; ?>" method="post">
                    <p><h3>Add student note for student number: <?php echo $birthCertNumber; ?></h3></p>
                    <p><textarea class="noteinput" name="note" type="text" cols="40" rows="10"></textarea></p>
                    <p><input type="submit" name='submit' value="Save note"></p>
                </form>        
            </div>
        </div> 
    </body>
</html>