<?php
$modul="ajax";

require("../inc/req.php");
validate('id','int');
validate('right_id', 'int');
validate('yn', 'boolean');

/*** Rights ***/
// Generally for people with right to grant rights
RR(4);
// Admin group for Administrators only
if ($_VALID['id'] == 1) {
    GRGR(1);
}

if (!isset($_MISSING)) {
    if ($_VALID['yn'] == 1) {
        $sql = "REPLACE INTO right2gr(right_id,gr_id,yn) VALUES(".$_VALIDDB['right_id'].",".$_VALIDDB['id'].",".$_VALIDDB['yn'].")";
    } else {
        $sql = "DELETE FROM right2gr WHERE right_id = ".$_VALIDDB['right_id']." AND gr_id = ".$_VALIDDB['id'];
    }
    mysqli_query($con, $sql) or print(mysqli_error());
    if (LOG == 1) {
        $sql = "INSERT INTO right2gr_log(gr_id,right_id,yn,dbupdate,update_user_id)
            VALUES(".$_VALIDDB['id'].",".$_VALIDDB['right_id'].",".(int) $_VALID['yn'].",now(),'".$_SESSION['user_id']."')";
        mysqli_query($con, $sql) or print(mysqli_error());
    }
}
?>
