<?php
$modul="ajax";

require("../inc/req.php");
validate('user_id','int');
validate('gr_id', 'int');
validate('yn', 'boolean');

/*** Rights ***/
// Generally for people with right do manage groups
RR(2);

// Admin group for Administrators only
if ($_VALID['user_id'] == 1 || $_VALID['gr_id'] == 1) {
    GRGR(1);
}

if (!isset($_MISSING)) {
    if ($_VALID['yn'] == 1) {
        $sql = "REPLACE INTO user2gr(user_id,gr_id) VALUES(".$_VALIDDB['user_id'].",".$_VALIDDB['gr_id'].")";
    } else {
        $sql = "DELETE FROM user2gr WHERE user_id = ".$_VALIDDB['user_id']." AND gr_id = ".$_VALIDDB['gr_id'];
    }
    mysqli_query($con, $sql) or print(mysqli_error());
    if (LOG == 1) {
        $sql = "INSERT INTO user2gr_log(user_id,gr_id,yn,dbupdate,update_user_id)
            VALUES(".$_VALIDDB['user_id'].",".$_VALIDDB['gr_id'].",".(int) $_VALID['yn'].",now(),'".$_SESSION['user_id']."')";
        mysqli_query($con, $sql) or print(mysqli_error());
    }
    $_SESSION['gr']['rl'] = true;
}
?>
